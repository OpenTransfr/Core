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
$error;
$result=callRoot('username/create','{"username":"'.$user.'","public_key":"'.$pubSignKey.'"}',$error);

if($error){
	
	// Error claiming the username.
	// This mainly indicates that one or more people tried to obtain it at the same time.
	error('username/unclaimed');
	
}

// Hex the private key too:
$privSignKey=bin2hex($signPair['private']);

// Create the account now:
$dz->query(
	'insert into `Bank.Accounts`(`Username`,`FullName`,`Registered`,`Country`,`SignKey`) values ("'
	.$user.'","'.$fullName.'",'.time().',0,unhex("'.$privSignKey.'"))'
);

// Get the account row ID:
$accountID=$dz->insert_id();

// Apply account settings next.
$dz->query('insert into `Bank.Account.Settings`(`Setting`,`Value`,`Account`) values '.
	
	// Create the favourite commodity setting:
	'("commodity.pref","'.$bankCurrency.'",'.$accountID.'), '.
	
	// Email address setting:
	'("email","'.$email.'",'.$accountID.')'
	
);

// Associate the device with the account:
$dz->query('update `Bank.Devices` set `Account`='.$accountID.' where ID='.$verifiedDevice);

echo '{"ID":"'.$accountID.'"}';

?>