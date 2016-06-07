<?php

// Checkout listing API (for a particular merchant account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the checkouts:
$checkouts=$dz->get_list('select `ID`,`Code`,`CreatedOn`,`Type` from `Merchant.Checkouts` where `Account`='.$verifiedAccount);

// Show the list now!
showList($checkouts,array('ID','Code','CreatedOn','Type'));

?>