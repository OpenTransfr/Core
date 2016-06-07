<?php

// Transaction creation API (from a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// How much for?
$amount=safe('amount',VALID_NUMBER,null,true);

if(!$amount){
	
	// Amount required:
	error('field/invalid','amount');
	
}

// Which commodity is the amount in?
$commodity=safe('commodity',VALID_DOMAIN);

if(!$commodity){
	
	// Commodity can't be blank.
	error('field/invalid','commodity');
	
}

// Get the reference. True because it's 'as-is'
$reference=trim( escape( safe('reference',true) ) );

// Get the nice name for the transaction (e.g. 'Power bill'):
$title=safe('title',VALID_TITLE);

// The username that will be paid:
$toUsername=safe('username',VALID_NAME);

// The name of the merchant; essentially a nicer form of username (e.g. 'NPower')
$toName=safe('name',VALID_TITLE);

// Get the item info (discounts are optional):
$products=safe('products',VALID_ARRAY);
$discounts=safe('discounts',VALID_ARRAY,null,true);

// Notify is typically the information that goes on the back of a receipt.
// It's a good place to add things like promotions or available deals.
$notify=escape( safe('notify',true,null,true) );

$itemInfo=cleanProducts($products,$discounts,$notify);

// Get the from username, unless told otherwise:
$privateUsername=safe('customer_privacy',VALID_NUMBER,null,true);
$fromUsername='';

if(!$privateUsername){
	
	// Get their username:
	$sender=$dz->get_row('select `Username` from `Bank.Accounts` where `ID`='.$verifiedAccount);
	
	// Update from username:
	$fromUsername=$sender['Username'];
	
}

// At this point we've got all our required information.

// Build the transfer data:
$transferData=array(
	'Commodity'=>$commodity,
	'Amount'=>$amount,
	'ItemInformation'=>$itemInfo,
	'Reference'=>$reference,
	'Username'=>$toUsername,
	'Title'=>$title,
	'Name'=>$toName
);

if($fromUsername){
	$transferData['FromUsername']=>$fromUsername;
}

// Perform the transfer now:
transfer($transferData);

?>