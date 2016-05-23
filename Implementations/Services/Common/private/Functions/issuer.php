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
	
	// Build the JWS:
	$message=jws($payload,'');
	
	// Request now:
	$error;
	$response=post('https://'.$endpoint.'/v1/commodity/status',$message,$error);
	
	return ($response=='{"result":"OK"}' && $error===NULL);
	
}

/*
* Builds a JWS signed with this entities key.
*/
function jws($payload,$protected){
	
	// Base64 the payload:
	$payload=base64_encode($payload);
	
	// And the header:
	$protected=base64_encode($protected);
	
	// Build the signed message:
	return '{"header":{"entity":"'.$thisEntity['Endpoint'].'"},"protected":"'.$protected.'","payload":"'
		.$payload.'","signature":"'.base64_encode( sign($protected.'.'.$payload) ).'"}';
	
}

/*
* Submits a request to root using this entities private key to sign a JSON payload.
*/
function root($path,$payload,$protected){
	
	$message=jws($payload,$protected);
	
	// Request now:
	$error;
	$response=post('https://'.randomRoot().'/v1/'.$path,$message,$error);
	
	if($error!=NULL){
		return false;
	}
	
	return $response;
	
}

/*
* Gets a random root node from this entities group.
*/
function randomRoot(){
	
	global $rootKeys;
	
	if($rootKeys==null){
		// Root keys holds a mapping of endpoint to key.
		// We'll use it here to get those endpoints.
		getRootKeys();
	}
	
	// The key is actually the endpoint, so this is nice and simple:
	return array_rand($rootKeys);
	
}

?>