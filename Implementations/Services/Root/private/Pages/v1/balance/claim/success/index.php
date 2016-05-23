<?php

// This occurs when every root node has signed to say it believes an issue was valid.
// A root node collected all those signatures and then sent the whole lot to every root node.
// This allows them to independently form a proof of majority.
// In this case, when the majority is successful, an issue occurs.

// Always JSON posted here:
$publicKey=postedTo(true);

if($forwardedFromRoot==0){
	
	// Must have been forwarded by a root node.
	error('entity/notroot');
	
}

// Get the challenges:
// We want it as-is (it's an array)
$challenges=safe('challenges',VALID_ARRAY);

// Get the signature, address and current balance:
$signature=safe('signature',VALID_BASE64);
$address=safe('address',VALID_HEX);
$balance=safe('balance',VALID_NUMBER);

// The signed data is as follows:
$signed=bin2hex($publicKey).'.'.$balance;

// Test for a majority. This time we check all signatures including our own
// because the whole set came from somewhere else (we're not leading the majority process here).
$majority=testMajority($challenges,$signed);

// Majority formed!

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