<?php

// Creates a new classification system. Requires a registered root Entity.
postedTo();

if($verifiedEntity==0){
	
	// Entity required!
	error('entity/required');
	
}

// Code, e.g. 'com':
$code=safe('code',VALID_ALPHA);

// Is the code taken?
$row=$dz->get_row('select ID from `Merchant.ProductClassSystems` where `Code`="'.$code.'"');

if($row){
	
	// Code taken.
	error('code/exists');
	
}

// Name:
$name=safe('name',VALID_TITLE);

// Endpoint (A URL):
$endpoint=trim ( escape( safe('endpoint',true) ) );

// Test the endpoint:
$error;
$result=post($endpoint,'{"code":"'.$code.'","mode":"test"}');

if($error){
	// Endpoint failed.
	error('endpoint/invalid');
}else if($result!='{"test":"OK"}'){
	
	// The result should exactly match.
	error('endpoint/failed');
	
}

// Create the row now:
$dz->query('insert into `Merchant.ProductClassSystems`(`Code`,`Name`,`Endpoint`,`RegisteredBy`) values ("'.$code.'","'.
		$name.'","'.$endpoint.'",'.$verifiedEntity.')');

?>