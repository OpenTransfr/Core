<?php

// This occurs when every root node has signed to say it believes an outroot transaction was valid.
// A root node collected all those signatures and then sent the whole lot to every root node.
// This allows them to independently form a proof of majority.
// In this case, when the majority is successful, a half-transaction occurs.

// Always JSON posted here:
postedTo();

if($forwardedFromRoot==0){
	
	// Must have been forwarded by a root node.
	error('entity/notroot');
	
}

// Get the challenges:
// We want it as-is (it's an array)
$challenges=safe('challenges',VALID_ARRAY);

$from=safe('from',VALID_ARRAY);
$to=safe('to',VALID_ARRAY);

// Get the from address and the from group:
$fromAddress=safe('address',VALID_HEX,$from);
$fromGroup=safe('group',VALID_NUMBER,$from);

// We also need the from balance as that acts as replay prevention:
$fromBalance=safe('balance',VALID_NUMBER,$from);

// Get the to address and the to group:
$toAddress=safe('address',VALID_HEX,$to);
$toGroup=safe('group',VALID_NUMBER,$to);

// Get the amount to transfer:
$amount=safe('amount',VALID_NUMBER);

if($amount==0){
	
	// Must be non-zero (we know it's positive as VALID_NUMBER doesn't accept -):
	error('field/invalid','amount');
	
}

// Get the signature for the address itself:
$signature=safe('signature',VALID_BASE64);

// The signed data is as follows:
$signed=$fromGroup.'/'.$fromAddress.'-'.$toGroup.'/'.$toAddress.'-'.$amount.'-'.$fromBalance;

// Test for a majority. This time we check all signatures including our own
// because the whole set came from somewhere else (we're not leading the majority process here).
testMajority($challenges,$signed);

// Majority formed!

// Note: From the previous test a few ms ago, we know the balance exists and is of the correct commodity.
// Because of the locking process, we know that the balance can't have been emptied and deleted.

// Update the amount now! This is a half-transaction so we only update the to address:
$dz->query('update `Root.Balances` set Balance=Balance+'.$amount.',LockedBalance=LockedBalance-'.$amount.' where `Key`=UNHEX("'.$toAddress.'")');

// Create a tx record (occurs in transfer/create/success as well):
changed('tx',array(
	'Amount'=>$amount,
	'To'=>array('address'=>$toAddress,'group'=>$toGroup),
	'From'=>array('address'=>$fromAddress,'group'=>$fromGroup),
	'Signature'=>$signature,
	'FromBalance'=>$fromBalance
));

?>