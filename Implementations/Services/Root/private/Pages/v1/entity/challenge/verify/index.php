<?php

// Challenge check.

// Here we allow the domain to be given over GET. This is so the user
// can simply put the URL given by the challenge in their browser to manually perform the verify.
if(isset($_GET['domain'])){
	
	// Treat the $_GET array as if it were posted JSON:
	$posted=$_GET;
	
}else{
	
	// This endpoint receives posted JSON:
	postedTo();

}

// Domain must be a valid domain name only:
$domain=safe('domain',VALID_DOMAIN);

// Get the pending request:
$row=$dz->get_row('select * from `Root.Entities.Pending` where `Endpoint`="'.$domain.'"');

if(!$row){
	
	// Not found!
	error('entity/notfound');
	
}

// Get the public key bytes:
$publicKey=$row['Key'];

// Get the public key in hex:
$hexPublicKey=bin2hex($publicKey);

// The URL we'll be requesting:
$url='https://'.$domain.'/opentx/'.substr($hexPublicKey,0,32).'.json';

// Perform the GET request now:
$json=get($url);

// Got anything?
if($json==null){
	
	// Remote site errored in some way.
	error('geturl/failed',$url);
	
}

// Try to decode the JSON:
$json=json_decode($json,true);

if($json==null){
	
	// Invalid JSON.
	error('json/invalid','response');
	
}

// Update the signed ID. Normally this is delivered in the request
// however this time as there are two distinctive requests, the ID originates
// from the first one (and is stored in the database until this point):
$signedPublicID=array(
	'id'=>$row['RequestID'],
	'time'=>$row['RequestTime'],
	'signature'=>$row['RequestSig']
);

// Get the token (the set of challenge responses) from the JSON (base 64):
$payloadB64=safe('token',VALID_BASE64,$json);

// Try base64 decoding now:
$decodedPayload=base64_decode($payloadB64,true);

if($decodedPayload==null){
	
	// Not valid base64.
	error('field/invalid','payload');
	
}

// Get the signature of the token (base 64):
$signature=safe('signature',VALID_BASE64,$json);

// Now we have the public key (bytes) to verify the signature (base64) with.
if(!verify($signature,$payloadB64,$publicKey)){
	
	// Verify failed.
	error('signature/invalid');
	
}

// It verified! JSON decode the payload:
$json=json_decode($decodedPayload,true);

// A little tidying up:
$payloadB64=null;

if($json==null){
	
	// Invalid JSON.
	error('json/invalid','payload');
	
}

// At this point, payload is the set of signed challenges.
// Hopefully, we should see our challenge in it.
// It's indexed by endpoint, so it's nice and easy to see:
$endpoint=$thisEntity['Endpoint'];

if(!isset($json[$endpoint])){
	
	// Not in there! This can be ok though; possible that this node was simply offline.
	// However, we still issue an error. Note that this occurs before the broadcast to all other roots
	// So if the root they send the request to happened to be offline then the broadcast wouldn't occur.
	// However, the 'uri' seen in the original challenge always directs the user back to the same
	// root node to verify their challenge. In other words, it will never have been offline.
	error('entity/notpresent');
	
}

// Does it match my original challenge info?
if($json[$endpoint]!=$row['Challenge']){
	
	// Doesn't match!
	error('challenge/invalid');
	
}

// We have a successful match! This root node now trusts that our pending entity owns the domain name.
// So, it signs the challenge to declare that:

// Sign the challenge data along with the hex public key:
$signature=sign($row['Challenge'].$hexPublicKey);

// Build my signature JSON:
$mySignature='{"challenge":"'.$row['Challenge'].'","signature":"'.base64_encode($signature).'"}';

// At this point, we now forward the request to other root nodes so they can all perform the above check.
// (Provided we weren't forwarded to!)

// Is this already forwarded? If so, respond at this point and stop there.
if($forwardedFromRoot!=0){
	
	// Some other root forwarded the request here. Just return my challenge:
	echo $myChallenge;
	exit();
	
}

// Forward this request to other root nodes, stating that this root node is leading it.
// Custom forward call here because domain is almost always given over GET.
// I.e. php://input isn't being set to what we want it to be.
// pHeader (2nd parameter of sendToRoot) is the same string as seen inside the forward() function.

// Base64 the payload:
$payload=base64_encode( '{"domain":"'.$domain.'"}' );

// Forward now!
$results=sendToRoot($payload,'eyJmd2QiOjF9'); // See above about this base64 text

// Results is now a set of successful responses. Each contains the signature
// of the challenge for a given endpoint and the public key.

// Verify each signature (except our own - we pass that in instead) to test if we have a majority.
// If we do, forward the whole set to complete the creation of a new entity!

$fullSet=testMajority($results,$hexPublicKey,$mySignature);

// Majority formed! - send it to the root so everyone can verify all the signatures too.

// Add the domain to it:
$fullSet='{"domain":"'.$domain.'","challenges":'.$fullSet.'}';

// For info on the base64 string in the middle, see the forward() function.
sendToRoot(base64_encode($fullSet),'eyJmd2QiOjF9',false,'entity/challenge/success');

// Remove from pending:
$dz->query('delete from `Root.Entities.Pending` where `Endpoint`="'.$domain.'"');

// Transfer the entity to being an official entity:
$dz->query('insert into `Root.Entities` (`Key`,`Endpoint`,`Type`,`Group`,`Name`,`Country`) values (unhex("'.$hexPublicKey.'"),"'.$domain.'",'.$row['Type'].','.$thisEntity['Group'].',"'.$row['Name'].'",'.$row['Country'].')');

// Create the change event too:
changed('entity',array(
	'key'=>$hexPublicKey,
	'type'=>$row['Type'],
	'endpoint'=>$domain,
	'group'=>$thisEntity['Group'],
	'name'=>$row['Name'],
	'country'=>$row['Country']
));

echo '{"entity":"'.$domain.'"}';

?>