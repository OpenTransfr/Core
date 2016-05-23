<?php

// Device listing API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the devices:
$devices=$dz->get_list('select `ID`,`Name`,`CreatedOn` from `Bank.Devices` where `Account`='.$verifiedAccount);

// Show the list now!
showList($devices,array('ID','CreatedOn','Name'));

?>