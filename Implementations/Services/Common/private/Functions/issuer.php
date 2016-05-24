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
	
	return ($response=='{"result":"OK"}' && $error===NULL);
	
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
	$result;
	if(!callRoot('commodity/issue',$payload,$result)){
		
		// Failed.
		return false;
		
	}
	
	// Ok!
	return true;
	
}

?>