<?php

// Delivery address listing API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the locations:
$locations=$dz->get_list('select * from `Bank.AccountLocations` where `Account`='.$verifiedAccount);

// Show the list now!
showList($locations,array('ID','Name','Type','Content','Preference'));

?>