<?php

// Balance listing API.

$balances=$dz->get_list('select `Root.Balances`.`Key`,`Root.Balances`.`Balance`,`Root.Balances`.`Commodity`,`Root.Entities`.`Endpoint` as `Entity` from `Root.Balances` left join `Root.Entities` on `Root.Balances`.`Entity` = `Root.Entities`.`ID`');

// Show the list now!
showList($balances,array('Key','Balance','Commodity','Entity'),function(&$row){
	
	// Hex encode the key:
	$row['Key']=bin2hex($row['Key']);
	
	if(!$row['Entity']){
		$row['Entity']='';
	}
	
});

?>