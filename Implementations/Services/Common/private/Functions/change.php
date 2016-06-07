<?php

/*
* Implement this interface and set it to a global called $onChanged.
* The run function is called when changed() is called.
* It's typically done inside (or in a file included from) your settings.php
*/
interface OnChanged{
  public function run($change);
}

/*
* Functionality which tracks changing data. Changes can be used to reconstruct anything (such as the balance table).
*/

function changed($type,$fields){
	
	// Get the signed public ID:
	global $signedPublicID,$dz;
	
	if($signedPublicID==null){
		error('field/missing','id');
	}
	
	$id=$signedPublicID['id'];
	$pubsig=$signedPublicID['signature'];
	
	// Grab the time:
	$time=$signedPublicID['time'];
	
	// Build the query:
	$query='insert into `Root.Changes`(`RequestID`,`Signature`,`Time`,`Type`,`Content`) values ("'.
				$id.'","'.$pubsig.'",'.$time.',"'.$type.'","'.escape(json_encode($fields),false).'")';
	
	// Run the query:
	$dz->query( $query );
	
	// Got an on changed function?
	global $onChanged;
	
	if(isset($onChanged)){
		
		// Yes - let's run it now:
		$onChanged->run(
			array(
				'Type'=>$type,
				'Content'=>$fields,
				'Signature'=>$pubsig,
				'RequestID'=>$id
			)
		);
		
	}
	
}

/*
* Builds an SQL update string using the given fields.
* Null values are considered to have not changed.
* All the values being passed here MUST have been SQL escaped.
* safe() responds with already escaped values.
* If nothing has changed, then this generates a changes/none error.
* Example output: `Issuer`='bank.opentrans.fr',`Name_en`='Example'
*/
function changes($fields){
	
	// Start building the response. It will go into this $changes var:
	$changes='';
	
	// For each field..
	foreach($fields as $fieldName=>$value){
		
		if($value==null){
			// Null value - no change.
			continue;
		}
		
		// Add the comma between fields:
		if($changes!=''){
			$changes.=',';
		}
		
		// Add the field:
		$changes.='`'.$fieldName.'`=';
		
		// Assuming it has been escaped..
		if(is_string($value)){
			
			// Add the value in quotes:
			$changes.='"'.$value.'"';
			
		}else{
			
			// It's a number - no quotes needed:
			$changes.=$value;
			
		}
		
	}
	
	// Anything?
	if($changes==''){
		
		// No changes were submitted.
		error('changes/none');
		
	}
	
	return $changes;
	
}

/*
* Process the data from a tx change.
*/
function processTxChange($change){
	
	global $dz;
	
	// Get the row:
	$row=$dz->get_row('select * from `Bank.Incomings` where `Key`=unhex("'.$change['to']['address'].'")');
	
	if(!$row || $row['Status']){
		// Some other bank, or we've already processed it etc.
		return;
	}
	
	// Update the status:
	$dz->query('update `Bank.Incomings` set `Status`=1 where `ID`='.$row['ID']);
	
	// Get the from address:
	$from=$dz->get_row('select `Commodity` from `Root.Balances` where `Key`=unhex("'.$change['from']['address'].'")');
	
	if(!$from){
		// Database is out of sync.
		serverError();
	}
	
	// Build the details set for the receive call:
	$details=array(
		'Commodity'=>$from['Commodity'],
		'Amount'=>$change['amount'],
		'Reference'=>$row['Reference'],
		'Title'=>$row['Title'],
		'Name'=>$row['Name'],
		'FromUsername'=>$row['From'],
		'ItemInformation'=>$row['ItemInformation']
	);
	
	// Receive it:
	receive($details,$row['Account']);
	
	// Finish by completing the status:
	$dz->query('update `Bank.Incomings` set `Status`=2 where `ID`='.$row['ID']);
	
}

?>