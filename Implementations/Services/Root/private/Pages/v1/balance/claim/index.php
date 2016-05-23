<?php

// Balance claim API.

// Receives JWS:
$publicKey=postedTo(true);

if($verifiedEntity==0){
	
	// Must be an entity.
	error('entity/required');
	
}

// Get the signature, address and current balance:
$signature=safe('signature',VALID_BASE64);
$address=safe('address',VALID_HEX);
$balance=safe('balance',VALID_NUMBER);

// Get the balance info - is it already claimed, and does the balance match?
$row=$dz->get_row('select `Balance`,`Entity` from `Root.Balances` where `Key`=UNHEX("'.$address.'")');

if($row){
	
	// Balance doesn't exist.
	error('balance/notfound');
	
}

if($row['Balance']!=$balance){
	
	// The balance does not match.
	error('balance/invalid');
	
}

if($row['Entity']!=0){
	
	// Someone has claimed it already (probably the requester).
	error('balance/claimed');
	
}

// Validate the signature:
$signed=bin2hex($publicKey).'.'.$balance;

if(!verify($signature,$signed,$address)){
	
	// Invalid signature:
	error('signature/invalid');
	
}

// Valid claim!

// Sign it to say this node agrees, using some additional random data:
$challenge=randomString(45);

$myChallenge='{"challenge":"'.$challenge.'","signature":"'.base64_encode( sign($challenge.$signed) ).'"}';

// Was this request forwarded?
if($forwardedFromRoot!=0){
	
	// Yes - just output my challenge. We'll wait for a majority.
	echo $myChallenge;
	exit();
	
}

// Forward:
$responses=forward();

// Did it obtain a majority?
$fullSet=testMajority($responses,$signed,$myChallenge);

// Yes - majority obtained! Forward the key set to the root now:


// Add the address:
$fullSet='{"address":"'.$address.'","challenges":'.$fullSet.'}';

// For info on the base64 string in the middle, see the forward() function.
$encodedFullSet=base64_encode($fullSet);

// Send now:
sendToRoot($encodedFullSet,'eyJmd2QiOjF9',false,'balance/claim/success');

// Update the balance:
$dz->query('update `Root.Balances` set `Entity`='.$verifiedEntity.' where `Key`=unhex("'.$address.'")');

// Create a clm record (occurs in balance/claim/success as well):
changed('clm',array(
	'Entity'=>$verifiedEntityEndpoint,
	'Address'=>$address,
	'Signature'=>$signature,
	'Balance'=>$balance
));

?>