<?php

if (!isset($_GET['name'])){
	error('field/required','name');
}

// Get the name:
$name = trim(safe('name',VALID_NAME,$_GET));

$row=$dz->get_row('select `Root.Entities`.`Name` as `Entity` from `Root.Usernames` left join `Root.Entities` on `Root.Entities`.`ID`=`Root.Usernames`.`Entity` where Username="'.$name.'"');

if(!$row){
	echo '{}';
	exit();
}

echo '{"entity":"'.$row['Entity'].'"}';
?>