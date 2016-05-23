<?php

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

?>