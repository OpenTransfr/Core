<?php

// Entity visibility API.

// The delay, in seconds, that an entity joining root waits for. Always at least 5 days.
$joinDelay=432000; // 60 * 60 * 24 * 5;


postedTo();

// Get the entity DNS address:
$entityEndpoint=safe('entity',VALID_DOMAIN);

// Get the regulator reference ID. Note that this can be anything; the FCA happen to use a number so we'll verify as that:
$regulatorID=safe('id',VALID_NUMBER);

if(!$regulatorID){
	
	// Invalid ID.
	error('field/invalid','id');
	
}

// Make sure it's actually an entity:
$entity=$dz->get_row('select `ID` from `Root.Entities` where Endpoint="'.$entityEndpoint.'"');

// Exists?
if(!$entity){
	
	// Nope!
	error('entity/notfound');
	
}

/////////////////////////////////////////
// Regulator specific code occurs here
/////////////////////////////////////////

// - Lookup the entity in your database.
//   Find a match based on the $entityEndpoint (which is e.g. txroot.bank.com).

// If a match is found, output JSON of the form types/regulated.

// Otherwise:
// error('entity/notregulated');

error('function/notimplemented');

?>