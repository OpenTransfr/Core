<?php

// Deletes a delivery address (from a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the ID:
$id=safe('id',VALID_NUMBER);

// Delete the location:
$result=$dz->query('delete from `Bank.AccountLocations` where `Account`='.$verifiedAccount.' and `ID`='.$id);

if(!$result){
	
	// Not found.
	error('location/notfound');
	
}

?>