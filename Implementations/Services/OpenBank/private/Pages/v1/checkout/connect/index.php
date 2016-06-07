<?php

// This performs two tasks - it essentially verifies the users pin
// and connects their (now auth'd) account to an existing checkout-in-progress entry.
postedTo();

if(!$verifiedAccount){
	// Nope! Account required.
	error('account/required');
}

// We're on the clock! Get the ID:
$id=safe('id',VALID_NUMBER);

// Update the row:
$update=$dz->query('update `Bank.Checkouts.Pending` set `Account`='.$verifiedAccount.' where `ID`='.$id.' and `Account`=0');

if(!$update){
	// ID not found.
	error('checkout/notfound');
}

// Ok! This request doesn't return anything.
?>