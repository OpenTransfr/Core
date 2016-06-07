<?php

// Issues the given username some testcoin.
// This kind of API would not exist with ordinary currencies as it makes it completely worthless.
// (as anyone can simply 'create' the currency)
// However, it is used to test OpenTransfr and it's surrounding infrastructure.

// The username to send to is posted here:
postedTo();

// Get the username:
$username=safe('username',VALID_NAME);

// How much do they want? Maxes out at 1000000 ($10,000)
$amount=safe('amount',VALID_NUMBER);

if(!$amount || $amount>1000000){
	
	// Invalid amount:
	error('field/invalid',$amount);
	
}

// Issue that much to the given username:
issueTo('virt.currency.test',$amount,$username,'TestCoin Deposit','Create your own commodity like TestCoin at https://issuer.opentrans.fr/');

?>