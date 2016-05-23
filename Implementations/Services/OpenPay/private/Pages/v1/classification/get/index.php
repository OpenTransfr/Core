<?php

// Gets information for a particular classification which
// uses one of the classSystems:// that direct the request here.
// For example, 'gpc' uses this lookup.

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
$row=$dz->get_row('select * from `Merchant.OPC` where `ClassNumber`="'.$code.'://'.$value.'"');

if(!$row){
	// Not found.
	error('classification/notfound');
}

echo '{"opc":'.$row['ID'].',"class_id":"'.escape($row['ClassNumber']).'","name":{"en":"'.escape($row['Name_en']).'"},"data":'.$row['RawData'].'}';

?>