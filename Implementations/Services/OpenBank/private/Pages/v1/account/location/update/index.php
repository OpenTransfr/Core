<?php

// Updates a delivery address (from a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the ID:
$id=safe('id',VALID_NUMBER);

// Get the new properties (all optional but there must be at least one):
$preference=safe('preference',VALID_NUMBER,null,true);
$name=safe('name',VALID_TITLE,null,true);

// Build the change query (errors if there are none):
$changeQuery=changes(array('Preference'=>$preference,'Name'=>$name));

// Update the location now:
$result=$dz->query('update `Bank.Account.Locations` set '.$changeQuery.' where `Account`='.$verifiedAccount.' and `ID`='.$id);

if(!$result){
	
	// Not found.
	error('location/notfound');
	
}

?>