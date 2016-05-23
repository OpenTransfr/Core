<?php

// Account creation API.

// This endpoint receives posted JSON:
postedTo();

if(!$verifiedDevice){
	
	// A device is required.
	error('device/required');
	
}

// Get their full name:
$fullName=ucfirst( trim( safe('name',VALID_TITLE) ) );

// Get the username:
$user=strtolower( safe('username',VALID_NAME) );

// Email address:
$email=strtolower( safe('email',VALID_EMAIL) );

// Already exists?
$row=$dz->get_row('select ID from `Bank.Accounts` where `Email`="'.$email.'"');

if($row){
	
	// Email used.
	error('email/exists');
	
}

// Has the device already got an account assigned to it? If so, another device entry is required.
// This prevents one device having access to potentially thousands of accounts (i.e. badly implemented API users).
if($verifiedAccount!=0){
	
	// Device already has an account assigned to it.
	error('device/assigned');
	
}

// Username available?
$row=$dz->get_row('select Username from `Root.Usernames` where `Username`="'.$user.'"');

if($row){
	
	// Username used.
	error('username/exists');
	
}

// Generate a keypair which is used to sign for this user:
$signPair=generateKeyPair();

// Get the public key as hex:
$pubSignKey=bin2hex($signPair['public']);

// 'Claim' the username by calling the root API:
$result=null;
$success=callRoot('username/create','{"username":"'.$user.'","public_key":"'.$pubSignKey.'"}',$result);

if(!$success){
	
	// Error claiming the username.
	print_r($rootErrors);
	error('username/unclaimed');
	
}

// Hex the private key too:
$privSignKey=bin2hex($signPair['private']);

// Create the account now:
$dz->query(
	'insert into `Bank.Accounts`(`Username`,`FullName`,`Email`,`Registered`,`Country`,`FavouriteCommodity`,`SignKey`) values ("'
	.$user.'","'.$fullName.'","'.$email.'",'.time().',0,"'.$bankCurrency.'",unhex("'.$privSignKey.'"))'
);

// Get the account row ID:
$accountID=$dz->insert_id();

// Associate the device with the account:
$dz->query('update `Bank.Devices` set `Account`='.$accountID.' where ID='.$verifiedDevice);

echo '{"ID":"'.$accountID.'"}';

?>