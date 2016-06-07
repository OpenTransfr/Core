<?php

// Used to start the transaction process, sending to a particular username.
// This essentially converts a username into a root balance, 
// essentially making the recipient anonymous from the networks point of view.

// Receives JWS data. Here however, the signee doesn't matter at all.
postedTo();

// Get the payment reference - this is simply as-is as it can be anything:
$reference=escape( safe('reference',true) );

// The username of who is being paid. Required as this essentially gets converted into an address:
$username=safe('username',VALID_NAME);

// A title for the payment. E.g. 'Shirt Order':
$title=safe('title',VALID_TITLE);

// The from username (optional). E.g. 'starling.shirts':
$from=safe('from',VALID_NAME,null,true);

// The from name (optional). E.g. 'Starling Shirts':
$name=safe('name',VALID_TITLE,null,true);

// The item data:
$itemData=safe('items',VALID_ARRAY);

// Must contain at least products:
safe('products',VALID_ARRAY,$itemData);

// Does the account exist here?
$account=$dz->get_row('select ID from `Bank.Accounts` where Username="'.$username.'"');

if(!$account){
	
	// Nope!
	error('account/notfound');
	
}

// Generate a new keypair. This is where they primarily originate from.
// When a transaction is seen on the public key, we'll know it completed for this reference.
$keypair=generateKeyPair();
$publicKey=storeKeyPair($keypair);

// Add to table of pending incomings:
$dz->query('insert into `Bank.Incomings`(`Reference`,`Key`,`Account`,`Title`,`From`,`ItemInformation`,`Name`) values("'.$reference.'",unhex("'.$publicKey.'"),'.$account['ID'].',"'.$title.'","'.$from.'","'.escape(json_encode($itemData),true).'","'.$name.'")');

// An address is always available and the TX will happen instantly.
// Note that if this is an address cache, delay will typically be set.
// The address pool may have been exhausted (in which case we return {"status":"EMPTY","refill":a_unix_timestamp} instead)
echo '{"address":{"status":"OK","value":"'.$publicKey.'"},"delay":0}';

?>