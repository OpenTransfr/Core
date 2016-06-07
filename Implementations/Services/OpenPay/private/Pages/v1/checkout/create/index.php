<?php

// Receives JSON data:
postedTo();

/*
* Allocates a code. Optionally it's a shortform code (<6 characters).
*/
function createCodeID($short=false){
	
	// Include the database:
	global $dz;
	
	// Which field are we using?
	if($short){
		$field='Short';
	}else{
		$field='Long';
	}
	
	// Obtain the ID. We'll use LAST_INSERT_ID so we can increment and read the field at the same time:
	$dz->query('update `Merchant.Codes` set `'.$field.'`=LAST_INSERT_ID( `'.$field.'` + 1 )');
	
	// The base ID is..
	$id=$dz->insert_id();
	
	if($short){
		
		// Short code - special case as we may need to wrap.
		// The point at which we wrap varies depending on the time the first code was allocated at.
		
		// First though, check if we're even at a wrap point:
		if($id==780 || $id==21950 || $id==614650 || $id==17210360){
			// 2, 3, 4, 5 character wrap points.
			
			// Test if the time has been more than 6 hours since we allocated #1.
			$row=$dz->get_row('select `ShortWrappedOn` from `Merchant.Codes`');
			
			// How long has it been?
			$delta=time() - $row['ShortWrappedOn'];
			
			// The minimum shortcode wrap time is 6 hours (in seconds):
			$minimumWrapTime=21600; // 6 * 60 * 60.
			
			// We wrap if it's at least been the minimum time:
			$wrap=($delta >= $minimumWrapTime);
			
		}else if($id==481890300){
			// 6 character wrap point. Always wrap this one.
			// Note that we don't use >= as other users may be running this code at the same time.
			$wrap=true;
		}else{
			// Don't wrap.
			$wrap=false;
		}
		
		if($wrap){
			// Wrap it back to 0. The next user will allocate #1:
			$dz->query('update `Merchant.Codes` set `Short`=0, `ShortWrappedOn`='.time());
		}
		
	}
	
	return $id;
}

/* Gets the spoken/printed transaction code for the given ID. These are typically
* spoken over the phone or printed. They're designed to be easy to
* say, read and non-vulgar.
* A 'shortform' code is one that is 6 characters or less (numbers below 481m)
* ID is always positive and non-zero.
*/
function idToCode($id){
	
	$id=(int)$id;
	
	// The available character set:
	$pattern='23456789BCDFGHJKLMNPQRSTWXYZ';
	
	// Split the number up into a summation of powers of 29.
	$code='';
	
	while($id!=0){
		
		// Divide by 28 and round down:
		$rounded=(int)floor( $id/28 );
		
		// Figure out the quotient; that will be our index in $pattern.
		$offset=$id-($rounded*28);
		
		$code.=$pattern{ $offset };
		
		// Update id:
		$id=$rounded;
		
	}
	
	// Done!
	return $code;
	
}

// Type: verbal, online, physical (physical point-of-sale)
$typeNumber=safe('type',array('verbal'=>1,'online'=>2,'physical'=>3),null);

$code='';

if($typeNumber==1){
	
	// Verbal. Allocate a shortcode.
	$code=createCodeID(true);
	
}else if($typeNumber==3){
	
	// Physical. Allocate a longcode.
	$code=createCodeID(false);
	
}

// Convert code into the textual form:
if(!($code==='')){
	$code=idToCode($code);
}

// Get the account ID next. This API allows 'anyone' to create an online checkout, but only a verified account
// can create physical/ verbal:
$merchantID=0;
$site='';

if($typeNumber==2){
	
	// Online. Anybody can call this - we're expecting the account ID too:
	$merchantID=safe('merchant',VALID_NUMBER);
	
	// Get the website (optional):
	$site=safe('site',VALID_DOMAIN,null,true);
	
}else{
	
	// Verfied only.
	if($verifiedAccount==0){
		
		// Account required!
		error('account/required');
		
	}
	
	// Merchant is simply the verified account:
	$merchantID=$verifiedAccount;
	
}

// Create the row now:
$dz->query('insert into `Merchant.Checkouts`(`Code`,`CreatedOn`,`Type`,`Account`,`Site`) values("'.
			$code.'",'.time().','.$typeNumber.','.$merchantID.',"'.$site.'")'
		);

// Get the raw ID:
$id=$dz->insert_id();

// Done!
echo '{"id":'.$id.',"code":"'.$code.'"}';

?>