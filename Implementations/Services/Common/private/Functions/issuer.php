<?php

/*
* Calls the issuer status API. This lets an issuer know the status of their
* request to issue a sub-commodity. (Where this server is the issuer of the parent
* commodity).
*/
function issuerStatus($endpoint,$tag,$status,$reason=NULL){
	
	// The payload is:
	$payload='{"tag":"'.$tag.'","status":"'.$status.'"';
	
	if($reason!=null){
		$payload.=',"reason":{"en":"'.escape($reason).'"}';
	}
	
	$payload.='}';
	
	$error;
	$response=sendTo($endpoint,'commodity/status',$payload,$error);
	
	if($error){
		
		// Client error header:
		header('HTTP/1.0 400 Bad Request');
		echo $error;
		exit();
		
	}
	
	return ($response=='{"result":"OK"}');
	
}

/* Issues the given amount of the given commodity to the given username. */
function issueTo($com,$amount,$username,$title='',$notify='',$reference=''){
	
	global $thisEntity;
	
	if(!$title){
		$title=$thisEntity['Name'].' Deposit';
	}
	
	// Issue that much:
	issue($com,$amount);
	
	// Build the transfer data:
	$transferData=array(
		'Commodity'=>$com,
		'Amount'=>$amount,
		'ItemInformation'=>'{"products":[{"opn":"com://'.$com.'","volume":'.$amount.',"total":0}],"notify":"'.escape($notify,false).'"}',
		'Reference'=>$reference,
		'Username'=>$username,
		'Title'=>$title,
		'Name'=>$thisEntity['Name'],
		'FromUsername'=>$thisEntity['Username']
	);
	
	// Transfer now, from the issuer itself:
	transfer($transferData,false);
	
}

/*
* Issues the given amount of the given commodity.
* Must of course be the issuer of that commodity to issue it.
*/
function issue($commodity,$amount){
	
	if(!is_numeric($amount) || $amount<=0){
		
		// Not a suitable number.
		error('field/invalid','amount');
		
	}
	
	// Make and store the address pair:
	$keypair=generateKeyPair();
	$publicKey=storeKeyPair($keypair);
	
	// Build the API message. It requires the tag, amount and an address.
	$payload='{"tag":"'.$commodity.'","amount":'.$amount.',"address":"'.$publicKey.'"}';
	
	// Call the issue API:
	$error;
	$result=callRoot('commodity/issue',$payload,$error);
	
	if($error){
		
		// Failed.
		return false;
		
	}
	
	// Ok!
	return true;
	
}

?>