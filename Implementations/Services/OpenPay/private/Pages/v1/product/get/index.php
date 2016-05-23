<?php

// Gets information for a particular product which
// uses one of the numberSystems:// that direct the request here.
// For example, 'com' uses this lookup.

// Receives JSON:
postedTo();

// Get the code:
$code=safe('code',VALID_ALPHA);

// What mode?
$mode=safe('mode',VALID_ALPHA,null,true);

if($mode=='test'){
	
	// Testing - respond with an OK:
	echo '{"test":"OK"}';
	exit();
	
}

// 'get' mode (default if mode is either not present or is not 'test').
$value=trim ( safe('value',VALID_TEXT) );

// Try finding it:
$row=$dz->get_row('select * from `Merchant.OPN` where `ProductNumber`="'.$code.'://'.$value.'"');

if(!$row){
	// Not found.
	error('product/notfound');
}

echo '{"opn":'.$row['OPN'].',"product_id":"'.escape($row['ProductNumber']).'","name":{"en":"'.escape($row['Name_en']).'"},"data":'.$row['RawData'].'}';

?>