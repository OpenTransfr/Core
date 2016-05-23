<?php

// Used to start the transaction process, sending to a particular username.
// This essentially converts a username into a root balance, 
// essentially making the recipient anonymous from the networks point of view.

// Receives JWS data. Here however, the signee doesn't matter at all.
postedTo();

// Get the payment reference - this is simply as-is as it can be anything:
$reference=escape( safe('reference',true) );

// The username of who is being paid:
$username=safe('username',VALID_NAME);

// A nice name for the payment:
$niceName=safe('name',VALID_TITLE);

// Does the account exist here?
$account=$dz->get_row('select ID from `Bank.Accounts` where Username="'.$username.'"');

if(!$account){
	
	// Nope!
	error('account/notfound');
	
}

// Generate a new keypair. This is where they primarily originate from.
// When a transaction is seen on the public key, we'll know it completed for this reference.
$keypair=generateKeyPair();

$publicKey=bin2hex($keypair['public']);
$privateKey=bin2hex($keypair['private']);

// Add to this banks address pool:
$dz->query('insert into `Bank.Balances`(`Key`,`Private`) values(unhex("'.$publicKey.'"),unhex("'.$privateKey.'"))');

// Add to table of pending incomings:
$dz->query('insert into `Bank.Incomings`(`Reference`,`Key`,`Account`,`Name`) values("'.$reference.'",unhex("'.$publicKey.'"),'.$account['ID'].',"'.$niceName.'")');

// An address is always available and the TX will happen instantly.
// Note that if this is an address cache, delay will typically be set.
// The address pool may have been exhausted (in which case we return {"status":"EMPTY","refill":a_unix_timestamp} instead)
echo '{"address":{"status":"OK","value":"'.$publicKey.'"},"delay":0}';

?>