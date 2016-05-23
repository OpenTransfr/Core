<?php

error('replay/sensitive');

// Out root API. This is essentially a combination of 'transfer/create' and the 'transfer/create/success' API.
// The other root essentially completed the transaction and put the value into a balance it owns.
// It's now giving that balance 'to us' (some other root group).

// -> Must be given a complete signature set from the other root in order to proceed (transfer/create/success functionality).
// -> When it succeeds, it acts like the transaction completed 'here' too (transfer/create functionality).

// The 'from' owning entity signs these requests, and it's submitted to the 'from' root group.
// To access a particular address, they must also sign the 
// transaction data with the private key of the address.

// Note: We could check that this is being dropped by a root node specifically from the 'fromGroup'
// however that makes sending information between planets considerably harder and is seemingly unnecessary
// due to the block of signatures in the request anyway.
// {Check if Entity with ID $verifiedEntity is in Group fromGroup}

// Get the commodity:
$commodity=safe('commodity',VALID_DOMAIN);

// Get the to/from information:
$to=safe('to',VALID_ARRAY);
$from=safe('from',VALID_ARRAY);

// Get the to address and the to group:
$toAddress=safe('address',VALID_HEX,$to);
$toGroup=safe('group',VALID_NUMBER,$to);

// Get the from address and the from group:
$fromAddress=safe('address',VALID_HEX,$from);
$fromGroup=safe('group',VALID_NUMBER,$from);

// We also need the from balance as that acts as replay prevention:
$fromBalance=safe('balance',VALID_NUMBER,$from);

if($toGroup!=$thisEntity['Group']){
	
	// Wrong group!
	error('group/invalid');
	
}

// Get the amount that got transferred:
$amount=safe('amount',VALID_NUMBER);

if($amount==0){
	
	// Must be non-zero (we know it's positive as VALID_NUMBER doesn't accept -):
	error('field/invalid','amount');
	
}

// Get the signature for the address itself:
$signature=safe('signature',VALID_BASE64);

// The signed data is as follows:
$signed=$fromGroup.'/'.$fromAddress.'-'.$toGroup.'/'.$toAddress.'-'.$amount.'-'.$fromBalance;

// Verify the signature:
if(!verify($signature,$signed,$publicKey)){
	
	// Invalid.
	error('signature/invalid');
	
}

// Next, check if the sending root did receive a majority.
// Get its signature set:
$results=safe('challenges',VALID_ARRAY);

// Test for a majority in that sending root:
testMajority($results,$signed,null,$fromGroup);

// Ok! So far we're convinced that this is a valid transaction request and the from root obtained a successful majority.
// We're now going to receive that amount locked as we'll need a majority here too.

// Lock the given amount into the given receiving address. This happens first because it fails
// if the commodities do not match:
receiveLocked($toAddress,$amount,$commodity);

// Ok! We've locked $amount. This half-transaction is now in progress.

// Generate some random challenge data:
$challenge=randomString(45);

// Sign the challenge data along with the data that was signed earlier:
$rootSignature=sign($challenge.$signed);

// Build my signature JSON:
$myPair='{"challenge":"'.$challenge.'","signature":"'.base64_encode($rootSignature).'"}';

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

$fullSet=testMajority($results,$signed,$myPair);

// Majority obtained! Forward all the signatures to everyone else.

// Add the amount and address:
$fullSet='{"amount":"'.$amount.'","commodity":"'.$commodity.'","from":{"address":"'.$fromAddress.'","group":'.
	$fromGroup.',"balance":'.$fromBalance.'},"to":{"address":"'.$toAddress.'","group":'.$toGroup.'},"signature":"'.$signature.'","challenges":'.$fullSet.'}';

// For info on the base64 string in the middle, see the forward() function.
sendToRoot(base64_encode($fullSet),'eyJmd2QiOjF9',false,'transfer/outroot/success');

// Update the amounts now!
$dz->query('update `Root.Balances` set Amount=Amount+'.$amount.',LockedAmount=LockedAmount-'.$amount.' where Key=UNHEX("'.$toAddress.'")');

// Create a tx record (occurs in transfer/outroot/success as well):
changed('tx',array(
	'Amount'=>$amount,
	'To'=>array('address'=>$toAddress,'group'=>$toGroup),
	'From'=>array('address'=>$fromAddress,'group'=>$fromGroup),
	'Signature'=>$signature,
	'FromBalance'=>$fromBalance
));

?>