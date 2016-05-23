<?php

if (!isset($_GET['name'])){
	error('field/required','name');
}

// Get the name:
$name = escape(trim($_GET['name']));

$row=$dz->get_row('select iso2 from countries where short_name="'.$name.'" or long_name="'.$name.'"');

if(!$row){
	error('country/notfound');
}

echo '{"code":"'.$row['iso2'].'"}';
?>