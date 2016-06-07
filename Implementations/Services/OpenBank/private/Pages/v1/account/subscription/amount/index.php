<?php

// Subscription amount API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Which one?
$id=safe('id',VALID_NUMBER);

// Fetch it:
$sub=$dz->get_row('select `Amount`,`DynamicUrl`,`Commodity`,`Reference`,`ItemInformation` from `Bank.Subscriptions` where `Account`='.$verifiedAccount.' and `ID`='.$id);

if(!$sub){
	
	// Not found:
	error('subscription/notfound');
	
}

// Is the amount set?
if($sub['Amount']){
	
	echo '{"amount":'.$sub['Amount'].',"items":'.$sub['ItemInformation'].'}';
	
}else{
	
	// Get the dynamic URL:
	$url=$sub['DynamicUrl'];
	
	// Try posting to it:
	$error;
	$response=post($url,'{"reference":"'.escape($sub['Reference']).'","commodity":"'.$sub['Commodity'].'"}',&$error)
	
	if(!$response){
		
		// Invalid response.
		error('subscription/response');
		
	}
	
	// Try JSON decoding it:
	$response=json_decode($response,true);
	
	if(!$response){
		
		// JSON response failed.
		error('field/invalid','response');
		
	}
	
	// Must be at least 'amount' and 'products' (same as ordinary subscription data).
	// 'discounts' and 'notify' are optional.
	$amount=safe('amount',VALID_NUMBER,$response);
	
	// Get the item info (discounts are optional):
	$products=safe('products',VALID_ARRAY,$response);
	$discounts=safe('discounts',VALID_ARRAY,$response,true);
	
	// Notify is typically the information that goes on the back of a receipt.
	// It's a good place to add things like promotions or available deals.
	$notify=escape( safe('notify',true,$response,true) );
	
	// Build item info now:
	$itemInfo=cleanProducts($products,$discounts,$notify);
	
	// Output it now:
	echo '{"amount":'.$amount.',"items":'.$itemInfo.'}';
	
}

?>