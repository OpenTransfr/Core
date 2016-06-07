<?php

// Device auth API.
// Requesting this is mainly used to check if the user is using the correct private key.

// This endpoint receives posted JSON:
postedTo();

if(!$verifiedDevice){
	
	// A device is required.
	// Note that the input system will likely have errored before it even got here
	// (as the verify call fails if they e.g. got the pin wrong).
	error('device/required');
	
}

if(!$verifiedAccount){
	
	// This device is not authorised to use any accounts.
	error('device/noaccount');
	
}

// Get the account information and the device sequence code.
$row=$dz->get_row('select `Sequence` from `Merchant.Devices` where ID='.$verifiedDevice);

if(!$row || !$row['Sequence']){
	
	// This should never happen (as the device ID was found ms ago) so we'll 500:
	serverError();
	
}

// Get the account information:
$account=$dz->get_row('select FullName from `Merchant.Accounts` where ID='.$verifiedAccount);

echo '{"account":{"id":'.$verifiedAccount.',"fullName":"'.$account['FullName'].'"}}';

?>