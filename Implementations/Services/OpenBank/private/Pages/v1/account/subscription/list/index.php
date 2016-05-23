<?php

// Subscription listing API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the subs:
$subs=$dz->get_list('select * from `Bank.Subscriptions` where `Account`='.$verifiedAccount);

// Show the list now!
showList($subs,array('ID','Amount','Commodity','Reference','Interval','NextTime','Name','Username','ToName','DynamicUrl','ItemInformation'));

?>