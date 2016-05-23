<?php

// List all usernames within a given range.

$usernames=$dz->get_list('select `Root.Usernames`.`Key`,`Username`,`Root.Entities`.`Endpoint` as `Entity` from `Root.Usernames` left join `Root.Entities` on `Root.Usernames`.`Entity` = `Root.Entities`.`ID`');

// Show the list now!
showList($usernames,array('Key','Username','Entity'),function(&$row){
	
	// Base 64 encode the key:
	$row['Key']=base64_encode($row['Key']);
	
});

?>