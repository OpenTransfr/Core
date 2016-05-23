<?php

// Username update API. Must be signed by the current entity.

error('broken query'); // UNHEX is currently being surrounded by quotes.

// Receives a JWS:
postedTo();

// Get the username:
$username=safe('username',VALID_NAME);

// Get the row:
$row=$dz->get_row('select ID,Entity from `Root.Usernames` where Username="'.$username.'"');

// Does it exist?
if(!$row){
	
	// Username was not found.
	error('username/notfound');
	
}

// Get the ID of the entity:
$entityID=$row['Entity'];

// Is the entity that signed this request the entity that registered it?
if($verifiedEntity!=$entityID){
	
	// Not signed by the current owner.
	error('entity/notowner');
	
}

// Ok! Get the new values for the fields.

// New entity?
$newEntity=safe('entity',VALID_DOMAIN,null,true);

if($newEntity!=null){
	
	// Get the local ID for this entity:
	$entityRow=$dz->get_row('select ID from `Root.Entities` where Endpoint="'.$newEntity.'"');
	
	if(!$entityRow){
		// Entity was not found.
		error('entity/notfound');
	}
	
	// Update newEntity with the ID:
	$newEntity=$entityRow['ID'];
	
}

// New public key?
$key=safe('key',VALID_HEX,null,true);

if($key!=null){
	
	// Add unhex around the hex key:
	$key='UNHEX("'.$key.'")';
	
}

// Get the SQL changes string (errors if there are none):
$changes=changes(array('Entity'=>$newEntity,'Key'=>$key));

// Run the query now:
$dz->query('update `Root.Usernames` set '.$changes.' where ID='.$row['ID']);


?>