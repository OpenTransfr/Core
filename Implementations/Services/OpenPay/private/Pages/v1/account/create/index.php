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

// Username exists?
$row=$dz->get_row('select Username from `Root.Usernames` where `Username`="'.$user.'"');

if(!$row){
	
	// Username was not found.
	error('username/notfound');
	
}

// Create the account now:
$dz->query(
	'insert into `Merchant.Accounts`(`Username`,`FullName`,`Registered`,`Country`) values ("'
	.$user.'","'.$fullName.'",'.time().',0)'
);

// Get the account row ID:
$accountID=$dz->insert_id();

// Apply account settings next.
$dz->query('insert into `Merchant.Account.Settings`(`Setting`,`Value`,`Account`) values '.
	
	// Create the favourite commodity setting:
	'("commodity.pref","'.$bankCurrency.'",'.$accountID.'), '.
	
	// Email address setting:
	'("email","'.$email.'",'.$accountID.')'
	
);

// Associate the device with the account:
$dz->query('update `Merchant.Devices` set `Account`='.$accountID.' where ID='.$verifiedDevice);

echo '{"ID":"'.$accountID.'"}';

?>