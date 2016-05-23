<?php

// Username create API.
// Signed by the entity registering it.

// This endpoint receives posted JSON:
postedTo();

if($verifiedEntity==0){
	
	// Must be an entity to use this API.
	error('entity/required');
	
}

// Get the username:
$username=safe('username',VALID_NAME);

// Get the key (hex):
$key=safe('public_key',VALID_HEX);

// Has the username been taken?
$row=$dz->get_row('select ID from `Root.Usernames` where Username="'.$username.'"');

// Does the row exist?
if($row){
	
	// Taken!
	error('username/exists');
	
}

// Insert the row now:
$dz->query(	'insert into `Root.Usernames`(`Username`,`Entity`,`Key`) values ("'.
				$username.'",'.$verifiedEntity.',UNHEX("'.$key.'"))'
			);
?>