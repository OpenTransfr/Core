<?php// Loads data from a merchant service checkout and begins a transaction with it.// In order to save time, this request occurs in parallel to the user entering their pin// thus it doesn't know which account is currently requesting this.// Note: This API does not receive a JWS (because it doesn't know which account).postedTo();// Get the gateway (the merchant service - typically this will be 'pay.opentrans.fr')$gateway=safe('gateway',VALID_DOMAIN);// The from username:$fromUsername=safe('username',VALID_NAME,null,true);// Get the code. Essentially the ID of the checkout with the merchant service.// It's always alphanumeric and uppercase.$code=safe('code',VALID_DEVICE);// Perform the request now - read the full info from the checkout:$rawData=get('https://'.$gateway.'/v1/checkout/get?code='.$code);// JSON decode it:$data=json_decode($rawData,true);// Got any response?if($data===null){		// Error!	error('checkout/notfound');	}// Get the checkout/merchant data:$checkout=safe('checkout',VALID_ARRAY,$data);$merchant=safe('merchant',VALID_ARRAY,$data);// What commodity is the price etc in?$priceCommodity=safe('commodity',VALID_DOMAIN,$checkout);// Look it up:$comRow=$dz->get_row('select `Name_en`,`Divisor` from `Root.Commodities` where `Tag`="'.$priceCommodity.'"');if(!$comRow){		// This should never happen - it indicates a faulty merchant service.	// So, emit an error just in case:	error('commodity/notfound');	}// Data contains things like the total, the merchant name, the products etc.// Most of the data stays here; only a small amount goes on to the customer.// As we're doing this in parallel to the user entering their pin, we want to do as much as possible.// This results in a much faster feeling transaction from the users point of view.// So, let's get an address to send to. This has a convenient side effect of being able// to directly tell the user about any active delays that might affect their transaction.$toUsername=safe('username',VALID_NAME,$merchant,true);$toAddress=safe('address',VALID_BASE64,$merchant,true);$toName=safe('name',VALID_TITLE,$merchant);$reference=trim( escape( safe('reference',true,$checkout) ) );$title=safe('title',VALID_TITLE,$checkout);$total=safe('total',VALID_NUMBER,$checkout);$ready=safe('ready',VALID_NUMBER,$checkout);if(!$toUsername && !$toAddress){	// Need either a username or an address (or both).	error('field/invalid','username','address');}// Should the from username be private?$privateUsername=safe('customer_privacy',VALID_NUMBER,$merchant,true);if($privateUsername){	// Clear the from username:	$fromUsername='';}// Get the item info (discounts are optional):$products=safe('products',VALID_ARRAY,$data);$discounts=safe('discounts',VALID_ARRAY,$data,true);// Notify is typically the information that goes on the back of a receipt.// It's a good place to add things like promotions or available deals.$notify=escape( safe('notify',true,$merchant,true) );$itemInfo=cleanProducts($products,$discounts,$notify);// Get an address to send to:$transferData=array(	'Commodity'=>$priceCommodity,	'Amount'=>$total,	'ItemInformation'=>$itemInfo,	'Reference'=>$reference,	'Username'=>$toUsername,	'Title'=>$title,	'Name'=>$toName);if($toAddress){		// Put the address in the transfer data:	$transferData['ToAddress']=$toAddress;		// Get the group ID:	$transferData['ToGroup']=safe('group',VALID_NUMBER,$merchant);	}else{		// Get an address:	getAddress($transferData);}$addr=$transferData['ToAddress'];$toGroup=$transferData['ToGroup'];// Add it to the database and assign an ID.$dz->query('insert into `Bank.Checkouts.Pending`(`CheckoutData`,`Reference`,`ItemInformation`,`Amount`,`Commodity`,`Username`,`Title`,`Name`,`ToAddress`,`ToGroup`,`FromUsername`) values ("'.				escape($rawData,false).'","'.$reference.'","'.escape($itemInfo,false).'",'.$total.',"'.$priceCommodity.				'","'.$toUsername.'","'.$title.'","'.$toName.'",unhex("'.$addr.'"),'.$toGroup.',"'.$fromUsername.'")');$id=$dz->insert_id();// Output the summary which will be displayed to the user:echo '{"id":'.$id.',"merchant":{"name":"'.$toName.'"},"commodity":{"tag":"'.$priceCommodity.'","name":"'.$comRow['Name_en'].'","divisor":'.$comRow['Divisor'].'},"total":'.$total.',"delay":'.$delay.',"ready":'.$ready.',"title":"'.$title.'"}';?>