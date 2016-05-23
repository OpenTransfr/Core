<?php

// This occurs when every root node has signed to say it believes an issue was valid.
// A root node collected all those signatures and then sent the whole lot to every root node.
// This allows them to independently form a proof of majority.
// In this case, when the majority is successful, an issue occurs.

// Always JSON posted here:
postedTo();

if($forwardedFromRoot==0){
	
	// Must have been forwarded by a root node.
	error('entity/notroot');
	
}

// Get the challenges:
// We want it as-is (it's an array)
$challenges=safe('challenges',VALID_ARRAY);

// Amount must be a positive non-zero number:
$amount=safe('amount',VALID_NUMBER);

$amount=(int)$amount;

if($amount==0){
	// They specified 0 - this isn't valid:
	error('field/invalid','amount');
}

// Get the address to issue into:
$hexAddress=safe('address',VALID_HEX);

// Test for a majority. This time we check all signatures including our own
// because the whole set came from somewhere else (we're not leading the majority process here).
testMajority($challenges,$hexAddress.$amount);

// Majority formed!

// Note: From the previous test a few ms ago, we know the balance exists and is of the correct commodity.
// Because of the locking process, we know that the balance can't have been emptied and deleted.

// Update the amount now!
$dz->query('update `Root.Balances` set Amount=Amount+'.$amount.',LockedAmount=LockedAmount-'.$amount.' where Key=UNHEX("'.$hexAddress.'")');

// Create an issue record (occurs in issue/success as well):
changed('issue',array(
	'Amount'=>$amount,
	'To'=>$hexAddress,
	'Tag'=>$tag
));

?>