<?php

// Balance listing API.

$balances=$dz->get_list('select `Address`,`Balance`,`Commodity`,`Root.Entities`.`Endpoint` as `Entity` from `Root.Balances` left join `Root.Entities` on `Root.Balances`.`Entity` = `Root.Entities`.`ID`');

// Show the list now!
showList($balances,array('Address','Balance','Commodity','Entity'),function(&$row){
	
	// Base 64 encode the key:
	$row['Address']=base64_encode($row['Address']);
	
});

?>