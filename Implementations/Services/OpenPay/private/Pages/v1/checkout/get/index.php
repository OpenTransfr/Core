<?php

// Gets the information for a particular checkout.
$code=safe('code',VALID_DEVICE,$_GET,true);

$search='';

if($code){

	// Search using the (safely escaped) code:
	$search='`Code`="'.$code.'"';
	
}else{
	
	// Use a straight ID instead:
	$id=safe('id',VALID_NUMBER,$_GET);
	
	if($id){
		
		// Search using the ID:
		$search='`ID`='.$id;
		
	}else{
		
		// Code or ID is required.
		error('field/required','code','id');
	}
	
}

// Get the checkout:
$checkout=$dz->get_row('select * from `Merchant.Checkouts` where '.$search);

if(!$checkout){
	
	// Not found.
	error('checkout/notfound');
	
}

// Get merchant settings/ data:

/*
* Gets the value of a given set of settings.
*/
function getSettings($set,$account){
	
	global $dz,$apiMode;
	
	// The built setting string:
	$settingString='';
	
	// For each one:
	foreach($set as $setting){
		
		if($settingString!=''){
			$settingString.=',';
		}
		
		$settingString.='"'.escape($setting).'"';
		
	}
	
	// Run the query now, selecting the values from the account settings:
	$list=$dz->get_list('select `Setting`,`Value` from `'.$apiMode.'.Account.Settings` where `Account`='.
		$account.' and `Setting` in ('.$settingString.')');
	
	$result=array();
	
	// Make it associative:
	foreach($list as $setting){
		
		$result[$setting['Setting']]=$setting['Value'];
		
	}
	
	// Any settings that were not present are set to null:
	foreach($set as $setting){
		
		// Was it added?
		if(!isset($result[$setting])){
			
			// No - Add it but as a null:
			$result[$setting]=null;
			
		}
		
	}
	
	return $result;
}

// Get various settings from the merchant:
$settings=getSettings(array('commodity.accepted','commodity.price','delivery.service','checkout.notify','checkout.title','customer.private'),$checkout['Account']);

// Get their values (can be null). Note that all of these settings are already HTML escaped:
$acceptedUnsafe=$settings['commodity.accepted'];
$priceCommodity=$settings['commodity.price'];
$title=$settings['checkout.title'];
$notify=$settings['checkout.notify'];
$deliveryService=$settings['delivery.service'];
$privateAddress=$settings['customer.private'];

// $checkout['Accepted'] is combined - note that this is a group of 
// comma-seaparated JSON objects and it varies with the products being added.
// Each contains: commodity, value, stacking info.

// Trim and lowercase the accepted string:
$acceptedUnsafe=trim(strtolower($acceptedUnsafe));

// The cleaned accepted string. It's comma separated.
$accepted=$checkout['Accepted'];

if($accepted===null){
	// Make sure it's at least a blank string:
	$accepted='';
}

if($acceptedUnsafe){
	
	// Split by the comma:
	$acceptedUnsafe=explode(',',$acceptedUnsafe);
	
	// How many?
	$acceptedCount=count($acceptedUnsafe);
	
	// Check them all - each must be a VALID_DOMAIN.
	for($i=0;$i<$acceptedCount;$i++){
		
		// Read it:
		$acceptedCommodity=safe($i,VALID_DOMAIN,$acceptedUnsafe);
		
		if($accepted){
			// Add the comma:
			$accepted.=',';
		}
		
		$accepted.='{"commodity":"'.$acceptedCommodity.'"}';
		
	}
	
}else{
	
	// The default is just 'currency':
	if($accepted){
		$accepted.=',';
	}
	
	$accepted.='{"commodity":"currency"}';
	
}

$itemInformation='"products":[';

// We'll compute the total and build product info at the same time.
$total=0;

// Output all the product information:
$products=$dz->get_list('select * from `Merchant.Checkouts.Products` where `Checkout`='.$checkout['ID']);

$first=true;

// For each one..
foreach($products as $product){
	
	// Add the comma:
	if($first){
		$first=false;
	}else{
		$itemInformation.=',';
	}
	
	// Increase total:
	$total+=$product['Total'];
	
	// Output the row as JSON:
	$itemInformation.='{"id":'.$product['ID'].',"opn":'.$product['OPN'].',"volume":'.$product['Volume'].',"total":'.$product['Total'].'}';
	
}

$itemInformation.='],"discounts":[';

// Output all the discount information:
$discounts=$dz->get_list('select * from `Merchant.Checkouts.Discounts` where `Checkout`='.$checkout['ID']);

$first=true;

// For each one..
foreach($discounts as $discount){
	
	// Add the comma:
	if($first){
		$first=false;
	}else{
		$itemInformation.=',';
	}
	
	// Decrease total:
	$total-=$discount['Amount'];
	
	// Output the row as JSON:
	$itemInformation.='{"id":'.$discount['ID'].',"name":"'.escape($discount['Name'],false).'","total":'.$discount['Amount'].'}';
	
}

if($total<0){
	// Always bottoms out at 0:
	$total=0;
}


$itemInformation.=']';

// Escaped version for the orders table:
$escapedItemInfo=escape('{'.$itemInformation.',"notify":"'.escape($notify,false).'"}',false);

// already got a reference/ orderID?
$orderID=$checkout['Reference'];

if(!$orderID){
	
	// Create one now:
	$dz->query('insert into `Merchant.Orders`(`Checkout`,`Merchant`,`TimeCreated`,`ItemInformation`,`Commodity`,`Total`) values ('.$checkout['ID'].','.$checkout['Account'].','.time().',"'.$escapedItemInfo.'","'.$priceCommodity.'",'.$total.')');
	
	// Get the order ID:
	$orderID=$dz->insert_id();
	
	// Update the checkout:
	$dz->query('update `Merchant.Checkouts` set `Reference`='.$orderID.' where ID='.$checkout['ID']);
	
}else{
	
	// Update the order:
	$dz->query('update `Merchant.Orders` set `ItemInformation`="'.$escapedItemInfo.'",`Commodity`="'.$priceCommodity.'",`Total`='.$total.' where ID='.$orderID);
	
}

// Great; time to return the information.
echo '{"checkout":{';

// Output checkout information:
echo '"id":'.$checkout['ID'].',"title":"'.escape($title,false).'","code":"'.$checkout['Code'].'",';

echo '"reference":"{\"url\":\"https://pay.opentrans.fr/v1/order/get?id='.$orderID.'\"}","type":'.$checkout['Type'].',"commodity":"'.escape($priceCommodity,false).'",';

echo '"created":'.$checkout['CreatedOn'].',"total":'.$total.',"ready":'.$checkout['ReadyToPay'];

echo '},"merchant":{';

// Next we're outputting merchant information.
$merchant=$dz->get_row('select `FullName`,`Username` from `Merchant.Accounts` where ID='.$checkout['Account']);

echo '"id":'.$checkout['Account'].',"name":"'.escape($merchant['FullName'],false);
echo '","username":"'.$merchant['Username'].'","accepted":['.$accepted.'],"notify":"'.escape($notify,false).'",';

if($privateAddress){
	// The customer_privacy field tells the bank to not send the merchants bank the senders username.
	echo '"customer_privacy":1,';
}

echo '"delivery_service":"'.escape($deliveryService,false).'"';

echo '},'.$itemInformation.'}';

?>