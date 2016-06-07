<?php

// Device creation API.

// This endpoint receives posted JSON:
$public_key=postedTo(true);

// Get the public key as hex:
$hexKey=bin2hex($public_key);

// Get the device name.

// Device name must be a valid title if it's declared:
$name=safe('name',VALID_TITLE,null,true);

if($name==''){
	// We'll use the user agent for the name instead.
	include('../private/Functions/userAgent.php');
	
	$ua=parse_user_agent();
	
	if(!$ua['platform']){
		$ua['platform']='Unknown platform';
	}
	
	if(!$ua['browser']){
		$ua['browser']='Unknown client';
	}
	
	// Escape including html stripping:
	$name=escape( $ua['platform'].' / '.$ua['browser'] );
	
}

// Generate a string which forms part of the device ID so an attacker
// Can't simply guess IDs.
$publicID=randomString(16);

// Generate the first sequence code too.
$sequence=randomString(16);

// Create the device row (Note: many of these are safe due to either being generated or checked already; escape not required):
$dz->query(
	'insert into `Bank.Devices`(`Key`,`PublicID`,`Sequence`,`CreatedOn`,`Name`) values (unhex("'.
	$hexKey.'"),"'.$publicID.'","'.$sequence.'",'.time().',"'.$name.'")'
);

// Get the device row ID:
$deviceID=$dz->insert_id();

echo '{"id":"'.$deviceID.'-'.$publicID.'","sequence":"'.$sequence.'"}';

?>