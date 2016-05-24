<?php

// Commodity issue API. Must be signed by the issuer.

// Receives a JWS:
postedTo();

// Get the tag:
$tag=safe('tag',VALID_DOMAIN);

// Get the commodity:
$commodity=$dz->get_row('select ID,Issuer from `Root.Commodities` where Tag="'.$tag.'"');

// Does it exist?
if(!$commodity){
	
	// Commodity was not found.
	error('commodity/notfound');
	
}

// Get the ID of the issuer:
$issuerID=$commodity['Issuer'];

// Is the entity that signed this request the issuer of this commodity?
if($verifiedEntity!=$issuerID){
	
	// Not signed by the current issuer.
	error('entity/notissuer');
	
}

// Amount must be a positive non-zero number:
$amount=safe('amount',VALID_NUMBER);

$amount=(int)$amount;

if($amount==0){
	// They specified 0 - this isn't valid:
	error('field/invalid','amount');
}

// Get the address to issue into:
$hexAddress=safe('address',VALID_HEX);

// Lock the given amount into the given receiving address:
receiveLocked($hexAddress,$amount,$tag,$issuerID);

// Generate some random challenge data:
$challenge=randomString(45);

// Sign the challenge data along with the hex public key and amount:
$signature=sign($challenge.$hexAddress.$amount);

// Build my signature JSON:
$myPair='{"challenge":"'.$challenge.'","signature":"'.base64_encode($signature).'"}';

// Is this already forwarded? If so, just return my signature.
if($forwardedFromRoot!=0){
	
	// Some other root forwarded the request here. Just output the signature and stop:
	echo $myPair;
	exit();
	
}

// Forward to the group:
$results=forward();

// Next, verify all the signatures. If we have a valid majority, forward it to the group.
// At which point, a successful issue has occured.

$fullSet=testMajority($results,$hexAddress.$amount,$myPair);

// Majority obtained! Forward all the signatures to everyone else.

// Add the amount and address:
$fullSet='{"amount":"'.$amount.'","address":"'.$hexAddress.'","challenges":'.$fullSet.'}';

// For info on the base64 string in the middle, see the forward() function.
sendToRoot(base64_encode($fullSet),'eyJmd2QiOjF9',false,'commodity/issue/success');

// Update the amount now!
$dz->query('update `Root.Balances` set Balance=Balance+'.$amount.',LockedBalance=LockedBalance-'.$amount.' where `Key`=UNHEX("'.$hexAddress.'")');

// Create an issue record (occurs in issue/success as well):
changed('issue',array(
	'Amount'=>$amount,
	'To'=>$hexAddress,
	'Tag'=>$tag
));

?>