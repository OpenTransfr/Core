<?php

// Transfer submit API.

// The 'from' owning entity signs these requests, and it's submitted to the 'from' root group.
// To access a particular address, they must also sign the 
// transaction data with the private key of the address.

$from=safe('from',VALID_ARRAY);
$to=safe('to',VALID_ARRAY);

// Get the from address and the from group:
$fromAddress=safe('address',VALID_HEX,$from);
$fromGroup=safe('group',VALID_NUMBER,$from);

// This group must be the from group. If it isn't, wrong place!
if($fromGroup!=$thisEntity['Group']){
	
	// Wrong group!
	error('group/invalid');
	
}

// We also need the from balance as that acts as replay prevention:
$fromBalance=safe('balance',VALID_NUMBER,$from);

// Get the to address and the to group:
$toAddress=safe('address',VALID_HEX,$to);
$toGroup=safe('group',VALID_NUMBER,$to);

// Is it going to another group?
$outbound=($toGroup!=$thisEntity['Group']);

// Get the amount to transfer:
$amount=safe('amount',VALID_NUMBER);

if($amount==0){
	
	// Must be non-zero (we know it's positive as VALID_NUMBER doesn't accept -):
	error('field/invalid','amount');
	
}

// We know the inputs are valid and so we now need to know if the from address is valid too:
$balanceRow=$dz->get_row('select Entity,Commodity from `Root.Balances` where `Key`=UNHEX("'.$fromAddress.'")');

if(!$balanceRow){
	
	// From balance doesn't exist!
	error('balance/notfound');
	
}

// The entity that signed this request must be the same as the balance owner, if it is non-zero.
// (i.e. if the address has been 'claimed' by the entity itself - typically after the first
// inward transaction to that address.).
$balanceEntity=$balanceRow['Entity'];

// This attempts to block stolen private keys.
if($balanceEntity!=0 && $balanceEntity!=$verifiedEntity){
	
	// Wrong signing entity.
	error('entity/notowner');
	
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

// Ok! So far we're convinced that this is a valid transaction request.
// Now we need to try and lock the amount but *only* if the balance matches fromBalance.
// If we're successful, the request is forwarded.

$tag=$balanceRow['Commodity'];

if(!$outbound){
	
	// Lock the given amount into the given receiving address. This happens first because it fails
	// if the commodities do not match:
	receiveLocked($toAddress,$amount,$tag);
	
}

$success=$dz->query( 'update `Root.Balances` set LockedBalance=LockedBalance+'.$amount.',Balance=Balance-'.$amount.
					' where `Key`=UNHEX("'.$fromAddress.'") and Balance='.$fromBalance
				   );

if(!$success){
	
	// Note that this has a side effect of leaving 'amount' in the locked balance of the to address.
	// We can completely ignore this as it's harmless (as implementations MUST NOT restore
	// Locked amounts in the event of a fatal crash), but we'll tidy it up anyway:
	$dz->query('update `Root.Balances` set LockedBalance=LockedBalance-'.$amount.' where `Key`=UNHEX("'.$toAddress.'")');
	
	// Invalid balance.
	error('balance/invalid');
	
}

// Ok! We've locked $amount. This transaction is now in progress.


// Generate some random challenge data:
$challenge=randomString(45);

// Sign the challenge data along with the data that was signed earlier:
$rootSignature=sign($challenge.$signed);

// Build my signature JSON:
$myPair='{"challenge":"'.$challenge.'","signature":"'.base64_encode($rootSignature).'"}';

// Is this already forwarded? If so, just return my signature.
if($forwardedFromRoot!=0){
	
	// Some other root forwarded the request here.
	
	// Create a lock for this balance. This is for if a majority is not obtained and we need to reverse the lock:
	$dz->query('insert into `Root.Balances.Locks`(`RequestID`,`Amount`) values("'.$signedPublicID['id'].'",'.$amount.')');
	
	// Output the signature and stop:
	echo $myPair;
	exit();
	
}

// Forward to the group:
$results=forward();

// Next, verify all the signatures. If we have a valid majority, forward it to the group.
// At which point, a successful issue has occured.
// Note that the false turns off the error (and instead we look out for a 'false')
$fullSet=testMajority($results,$signed,$myPair,false);

if($fullSet===false){
	
	$majority=false;
	
	// No majority formed. Unlike transfer/create/success, this can occur in normal circumstances.
	// As this is the same file which creates the balance locks in the first place, we can (and must) safely
	// reverse those locks here:
	
	// Send the from amount back to it's balance:
	$dz->query('update `Root.Balances` set LockedBalance=LockedBalance-'.$amount.',Balance=Balance+'.$amount.
					' where `Key`=UNHEX("'.$fromAddress.'")');
	
	// And the to amount needs to simply have the locked amount reduced:
	$dz->query('update `Root.Balances` set LockedBalance=LockedBalance-'.$amount.' where `Key`=UNHEX("'.$toAddress.'")');
	
	// Now we need to remove any remote locks too.
	// We do it by calling the same function but with an empty results set, which is set like so:
	$fullSet='{}';
	
}else{
	
	$majority=true;
	// Majority obtained! Forward all the signatures (fullSet) to everyone else.
	
}

// Add the amount and address:
$fullSet='{"amount":"'.$amount.'","commodity":"'.$tag.'","from":{"address":"'.$fromAddress.'","group":'.
	$fromGroup.',"balance":'.$fromBalance.'},"to":{"address":"'.$toAddress.'","group":'.$toGroup.'},"signature":"'.$signature.'","challenges":'.$fullSet.'}';

// For info on the base64 string in the middle, see the forward() function.
$encodedFullSet=base64_encode($fullSet);

sendToRoot($encodedFullSet,'eyJmd2QiOjF9',false,'transfer/create/success');

if(!$majority){
	
	// Now produce the error:
	error('majority/notformed');
	
}

// Update the amounts now!
$dz->query('update `Root.Balances` set LockedAmount=LockedAmount-'.$amount.' where Key=UNHEX("'.$fromAddress.'")');

if(!$outbound){
	// It's going to this root - update the to address as well:
	$dz->query('update `Root.Balances` set Amount=Amount+'.$amount.',LockedAmount=LockedAmount-'.$amount.' where Key=UNHEX("'.$toAddress.'")');
}

// Create a tx record (occurs in transfer/create/success as well):
changed('tx',array(
	'Amount'=>$amount,
	'To'=>array('address'=>$toAddress,'group'=>$toGroup),
	'From'=>array('address'=>$fromAddress,'group'=>$fromGroup),
	'Signature'=>$signature,
	'FromBalance'=>$fromBalance
));

// Is it going out of this root?
// Note: Only the 'leading' root node needs to do this.
if($outbound){
	
	// Yes - forwarding the to balance to another group.
	// These should be grouped together and sent in bulk for significant link latencies.
	
	// For info on the base64 string in the middle, see the forward() function.
	sendToRoot($encodedFullSet,'eyJmd2QiOjF9',false,'transfer/outroot',$toRoot);
	
}

?>