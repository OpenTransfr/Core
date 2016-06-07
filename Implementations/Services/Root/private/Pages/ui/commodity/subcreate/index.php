<?php

// Creates a sub-commodity. Used by the commodity/create UI.
// Note that the UI could request the issuer directly, but CORS (in web browsers) may cause problems.

// Receives JSON:
postedTo();

// Quick verify of the request; we're just forwarding it on to the issuer.
if(!$verifiedEntity){
	
	// Requires an entity.
	error('entity/required');
	
}

// Get the tag they want to register:
$tag=safe('tag',VALID_DOMAIN);

// Get the parent issuer for that tag:
$targetIssuer=findParent($tag);

if(!$targetIssuer){
	// Not found.
	error('entity/notfound');
}

// Get the issuers endpoint:
$row=$dz->get_row('select Endpoint from `Root.Entities` where ID='.$targetIssuer['ID']);

if(!$row){
	// server error; the server has an out-of-date entity/coms table:
	serverError();
}

// Send it to the issuer now:
$payload=file_get_contents('php://input');

$error;
// Post it off:
$response=post('https://'.$row['Endpoint'].'/v1/commodity/request',$payload,$error);

if($error){
	
	// 400.
	header('HTTP/1.0 400 Bad Request');
	echo $error;
	exit();
	
}

// Ok!
echo $response;

?>