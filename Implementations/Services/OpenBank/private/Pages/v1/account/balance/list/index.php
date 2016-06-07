<?php

// Balance listing API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Get the balances:
$balances=$dz->get_list('select `Amount`,`Root.Commodities`.`Name_en` as `Name`,`Root.Commodities`.`Divisor` as `Divisor`,`Root.Commodities`.`Tag` as `Tag` from `Bank.Account.Balances` left join `Root.Commodities` on `Root.Commodities`.`Tag` = `Bank.Account.Balances`.`Commodity` where `Bank.Account.Balances`.`Account`='.$verifiedAccount);

// Show the list now!
showList($balances,array('Amount','Name','Divisor','Tag'));

?>