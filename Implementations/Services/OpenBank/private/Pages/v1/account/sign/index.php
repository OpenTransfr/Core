<?php

// Signs a message using an accounts signing key. Used for smart contracts.
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the signing key (bytes):
$account=$dz->get_row('select `SignKey` from `Bank.Accounts` where ID='.$verifiedAccount);

if(!$account){
	// System error.
	serverError();
}

// Get the private key (bytes):
$privateKey=$account['SignKey'];

// The message can be just about anything.
$message=safe('message',true);

// Is it actually text?
if(!is_string($message)){
	
	// The message must be a string.
	error('field/invalid','message');
	
}

// Sign the message (sig is bytes):
$sig=sign($message,$privateKey);

// Ok!
echo '{"signature":"'.base64_encode($sig).'"}';

?>