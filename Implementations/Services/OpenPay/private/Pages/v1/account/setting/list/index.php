<?php

// Setting listing API (for a particular merchant account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the settings:
$settings=$dz->get_list('select * from `Merchant.Account.Settings` where `Account`='.$verifiedAccount);

// Show the list now!
showList($settings,array('ID','Setting','Value'));

?>