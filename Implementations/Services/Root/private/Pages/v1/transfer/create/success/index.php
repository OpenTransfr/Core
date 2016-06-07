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

// Is it going to another group?
$outbound=($toGroup!=$thisEntity['Group']);

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
$majority=testMajority($challenges,$signed,null,false);

if(!$majority){
	
	// No majority formed. We were told about this so we can reverse our locks.
	// We must first check if we actually had a lock for this request like so:
	$lock=$dz->get_row('select Amount from `Root.Balances.Locks` where RequestID="'.$signedPublicID['id'].'"');
	
	if($lock){
		
		// Remove the complete lock now, provided amounts match:
		if($lock['Amount']!=$amount){
			
			// Nope - invalid input.
			error('field/invalid','amount');
			
		}
		
		// Send the from amount back to it's balance:
		$dz->query('update `Root.Balances` set LockedBalance=LockedBalance-'.$amount.',Balance=Balance+'.$amount.
						' where `Key`=UNHEX("'.$fromAddress.'")');
		
		// Delete the lock row:
		$dz->query('delete from `Root.Balances.Locks` where RequestID="'.$signedPublicID['id'].'"');
		
		// And the to amount needs to simply have the locked amount reduced:
		$dz->query('update `Root.Balances` set LockedBalance=LockedBalance-'.$amount.' where `Key`=UNHEX("'.$toAddress.'")');
		
	}
	
	error('majority/notformed');
	
}

// Majority formed!

// Note: From the previous test a few ms ago, we know the balance exists and is of the correct commodity.
// Because of the locking process, we know that the balance can't have been emptied and deleted.

// Update the amounts now!
$dz->query('update `Root.Balances` set LockedBalance=LockedBalance-'.$amount.' where `Key`=UNHEX("'.$fromAddress.'")');

if(!$outbound){
	// It's going to this group:
	$dz->query('update `Root.Balances` set Balance=Balance+'.$amount.',LockedBalance=LockedBalance-'.$amount.' where `Key`=UNHEX("'.$toAddress.'")');
}

// Create a tx record (occurs in transfer/create as well):
changed('tx',array(
	'amount'=>$amount,
	'to'=>array('address'=>$toAddress,'group'=>$toGroup,'balance'=>$fromBalance),
	'from'=>array('address'=>$fromAddress,'group'=>$fromGroup),
	'signature'=>$signature
));

?>