<?php

// Transfer history API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the tx history:
$subs=$dz->get_list('select * from `Bank.Transactions` where `Account`='.$verifiedAccount);

// Show the list now!
showList($subs,array('ID','Type','Amount','Commodity','Reference','Name','Username','ItemInformation','TimeAt'));

?>