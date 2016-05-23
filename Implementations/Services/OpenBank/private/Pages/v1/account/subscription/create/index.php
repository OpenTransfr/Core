<?php

// Subscription creation API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// How much for? Optional because you can instead provide a Dynamic URL.
$amount=safe('amount',VALID_NUMBER,null,true);
$dynamicUrl='';

if(!$amount){
	
	// Set it to the number 0:
	$amount='0';
	
	// The URL is now required. True because it's 'as-is'
	$dynamicUrl=trim( escape( safe('url',true) ) );
	
}

// Which commodity is the amount in?
$commodity=safe('commodity',VALID_NAME);

if(!$commodity){
	
	// Commodity can't be blank.
	error('field/invalid','commodity');
	
}

// Get the reference. True because it's 'as-is'
$reference=trim( escape( safe('reference',true) ) );

// Get the interval string. True because it's 'as-is'
$interval=strtoupper( trim( escape( safe('interval',true) ) ) );

// Get the current time:
$time=time();

// Figure out the next time this interval will trigger:
$nextTime=computeIntervalTime($interval,$time);

if(!$nextTime){
	// Bad interval.
	error('field/invalid','interval');
}

// Get the nice name for the subscription (e.g. 'Power bill'):
$name=safe('name',VALID_TITLE);

// The username that will be paid at this interval:
$username=safe('username',VALID_NAME);

// The name of the merchant; essentially a nicer form of username (e.g. 'NPower')
$toName=safe('merchant',VALID_TITLE);

if($dynamicUrl){
	
	// Item info is provided on each request instead.
	$itemInfo='';
	
}else{
	
	// Get the item info (discounts are optional):
	$products=safe('products',VALID_ARRAY);
	$discounts=safe('discounts',VALID_ARRAY,null,true);
	
	// Notify is typically the information that goes on the back of a receipt.
	// It's a good place to add things like promotions or available deals.
	$notify=escape( safe('notify',true,null,true) );
	
	$itemInfo=cleanProducts($products,$discounts,$notify);
	
}

// Create the subscription now:
$dz->query('insert into `Bank.Subscriptions`(`Amount`,`Commodity`,`Reference`,`Interval`,`NextTime`,'.
		'`Name`,`Username`,`ToName`,`DynamicUrl`,`ItemInformation`,`CreatedOn`) values ('.$amount.',"'.
		$commodity.'","'.$reference.'","'.$interval.'",'.$nextTime.',"'.$name.'","'.
		$username.'","'.$toName.'","'.$dynamicUrl.'","'.escape($itemInfo).'",'.$time.')');

// Get the latest ID:
$subID=$dz->insert_id();

// Done!
echo '{"ID":'.$subID.'}';
?>