<?php

// Complete the payment of a checkout. We're on the clock here!
postedTo();

if(!$verifiedAccount){
	// Nope! Account required.
	error('account/required');
}

// Get the ID:
$id=safe('id',VALID_NUMBER);

// Get the whole pending checkout as it contains everything we need:
$checkout=$dz->get_row('select * from `Bank.Checkouts.Pending` where `ID`='.$id.' and `Account`='.$verifiedAccount);

// Perform the transfer now using the checkout data:
transfer($checkout);

// Ok!
?>