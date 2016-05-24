<?php

/*
* Functions in this file deal with all user input 
* (other nodes included), ensuring it's safe to process.
*/

/*
* Validation constants.
* These are used with the safe() function to validate user input.
*/
define('VALID_DOMAIN',"/[^a-z0-9\-\.]+/");
define('VALID_NUMBER',"/[^0-9]+/");
define('VALID_DECIMAL',"/[^0-9\.]+/");
define('VALID_ARRAY',true);
define('VALID_HEX',"/[^a-fA-F0-9]+/");
define('VALID_ID',"/[^a-fA-F0-9\@]+/");
define('VALID_DEVICE',"/[^a-zA-Z0-9\-]+/");
define('VALID_BASE64',true);
define('VALID_ALPHA',"/[^A-Za-z]+/");
define('VALID_EMAIL',"/[^\p{L}\p{N}\@\.]+/");
define('VALID_NAME',"/[^\p{Ll}\p{N}\.\-]+/");
define('VALID_TITLE',"/[^\p{L}\p{N} \.\-]+/");
define('VALID_TEXT',"*");

/*
* ID of the entity the message was forwarded from if it is another root.
*/
$forwardedFromRoot=0;

/* The ID of the verified entity that requested the API.
* Originates from the JWS.
*/
$verifiedEntity=0;
$verifiedEntityEndpoint='';

/* The ID of the verified device that requested the API.
* Used by the Bank API.
*/
$verifiedDevice=0;

/* The ID of an authorised account. A device authorises and then this
* comes from the account that the device is connected to.
*/
$verifiedAccount=0;

/*
* Contains the ID and the public signature of the current request.
* This is stored in the change log and is verifiable publically.
*/
$signedPublicID=null;

/*
* Obtains the given field from the given set and makes sure it's within
* the given range. If no range is given then an ordinary SQL escape occurs.
* Note that HTML will be escaped as well when that happens.
*/
function safe($field,$range=null,$set=null,$optional=false){
	
	if($set==null){
		
		// Makes sure we import the posted JSON variable:
		global $posted;
		
		// The set to use is the global JSON data:
		$set=$posted;
		
	}
	
	// Is the field actually defined?
	if(!isset($set[$field])){
		
		if($optional){
			return null;
		}
		
		// The field doesn't exist!
		error('field/missing',$field);
		
	}
	
	// Grab the value:
	$value=$set[$field];
	
	// If the range is an array, the value must be one of the entries in the array:
	if(is_array($range)){
		
		// Must match one of the entries:
		
		if(isset($range[0])){
			
			// It's just an ordinary array, in which case, the value is just the return.
			
			$size=count($range);
			
			for($i=0;$i<$size;$i++){
				
				if($range[$i]==$value){
					
					// Ok!
					return $value;
					
				}
				
			}
			
		}else{
			
			// It's associative, e.g. ('bank'=>0,'root'=>1)
			// These define the actual return value from this method.
			// E.g. if the user sends 'bank', 0 is the return value from this method.
			
			foreach($range as $acceptable=>$result){
				
				// Following the above example, $acceptable is 'bank'
				// And result is 0.
				
				if($acceptable==$value){
					
					// We have an acceptable value! Return *result*:
					return $result;
					
				}
				
			}
			
		}
		
		// Failed - unrecognised value.
		error('field/invalid',$field);
		
	}else if($range===true){
		
		// Just return the value as-is:
		return $value;
		
	}else if($range===NULL){
		
		// Range isn't defined - we just safety escape it here:
		return escape($value);
		
	}
	
	// Otherwise it's a regex string:
	if($range!='*' && preg_replace($range,"",$value)!=$value){
		
		// It contains unacceptable characters.
		error('field/invalid',$field);
		
	}
	
	return escape($value);
	
}

/*
* Loads the posted JSON data into the $posted variable.
*/
function postedTo($keyInfo=false){
	
	// Make sure we're using the global $posted variable:
	global $posted;
	
	// Decode the inputted JSON:
	$posted=json_decode(file_get_contents('php://input'), true);
	
	// JWS? Only look for payload at this point - loadJws will check for the rest.
	if(isset($posted['payload'])){
		
		// The public key (and where it came from) used to verify the JWS signature.
		$publicKey=NULL;
		$entityID=0;
		$entityEP='';
		$isRoot=false;
		
		// We'll make postedHeader global as well.
		global $postedHeader;
		
		// Load the JWS. Note:
		// - The payload is set to $posted
		// - The protected header is set to $postedHeader
		loadJws($entityID,$entityEP,$isRoot,$publicKey);
		
		if($isRoot){
			
			// Special case here. The message came from a root node.
			// Very likely that the root node is forwarding some data here.
			// In which case, the payload could be another JWS.
			
			// So, check $postedHeader for the special 'fwd' field:
			if(isset($postedHeader['fwd'])){
				
				// Get the forwarded variable:
				global $forwardedFromRoot;
				
				// Yep! It's being forwarded. Load the payload:
				$forwardedFromRoot=$entityID;
				
				// Load the JWS from the payload:
				if(isset($posted['payload'])){
					
					loadJws($entityID,$entityEP,$isRoot,$publicKey);
					
				}else{
					
					// Not a JWS being forwarded.
					$postedHeader=null;
					
				}
				
			}
			
		}
		
		// We should have 'pubkey' in the protected header, verify it.
		$pubsig=safe('pubsig',VALID_BASE64,$postedHeader);
		
		// Get the ID as well:
		$id=safe('id',VALID_ID,$postedHeader);
		
		// Check the structure of the ID is correct by pulling the time out.
		// We'll then check to see if the time is too far out from our local UTC time.
		
		// The time (in ms) is contained in the ID, at the very end after an @:
		$pieces=explode('@',$id);
		
		if(count($pieces)!=2){
			
			// Invalid number of @ signs.
			error('field/invalid','id');
			
		}
		
		// Grab the time (in ms):
		$time=$pieces[1];
		
		// Time must be numeric and the first piece must be 20 chars:
		if(!is_numeric($time) || strlen($pieces[0])!=20){
			
			// Invalid time:
			error('field/invalid','id');
			
		}
		
		// Verify the pubsig:
		if(!verify($pubsig,$id,$publicKey)){
			
			// Pubsig invalid.
			error('signature/invalid');
			
		}
		
		global $apiMode,$signedPublicID;
		
		// Update the signed ID:
		$signedPublicID=array('id'=>$id,'signature'=>$pubsig,'time'=>$time);
		
		if($apiMode=='bank'){
			
			global $verifiedDevice,$verifiedAccount;
			
			// Update verified device (note: it's returned as entityID):
			$verifiedDevice=$entityID;
			
			// Update the account too (which is returned as entityEP):
			$verifiedAccount=$entityEP;
			
		}else{
			
			global $verifiedEntity,$verifiedEntityEndpoint;
			
			// Update verified entity:
			$verifiedEntity=$entityID;
			$verifiedEntityEndpoint=$entityEP;
			
		}
		
		if($keyInfo){
			
			return $publicKey;
			
		}
		
	}
	
}

/*
* Loads the posted JWS object from $posted into the $posted and $postedHeader variables.
* Provides the public key used to verify the signature.
* The public key can originate from an entity ID, so that is provided too (when possible).
* That entity might be a root node, in which case, $isRoot gets set to true.
*/
function loadJws(&$entityID,&$entityEP,&$isRoot,&$publicKey){
	
	global $posted,$postedHeader,$apiMode;
	
	// Get the fields of the JWS:
	$header=safe('header',VALID_ARRAY);
	$pHeaderB64=safe('protected',VALID_BASE64);
	$signature=safe('signature',VALID_BASE64);
	$payloadB64=safe('payload',VALID_BASE64);
	$sequence=null;
	$row=null;
	
	// Now we verify the JWS signature. Unprotected header either contains
	// 'entity' or 'pk' which we'll use.
	
	if( $apiMode=='bank' && isset($header['device']) ){
		
		// Bank API. We're receiving a device here.
		
		// We'll need the database to verify a device:
		global $dz;
		
		// Get the device (format is 'ID-PublicKey'):
		$device=safe('device',VALID_DEVICE,$header);
		
		// Split it at the - and lookup our device row.
		$parts=explode('-',$device);
		
		// Should be 2 parts and the 1st part must be numeric.
		// Note that it can't contain spaces due to VALID_DEVICE.
		if(!is_numeric($parts[0]) || count($parts)!=2){
			
			// Invalid device ID.
			error('field/invalid','device');
			
		}
		
		// Force it to be a number and treat it as the entity ID here:
		$entityID=(int)$parts[0];
		
		// Get the row:
		$row=$dz->get_row('select `Key`,`Account`,`Sequence`,`FailedAttempts`,`LastFailedAttempt` from `Bank.Devices` where ID='.$entityID.' and PublicID="'.$parts[1].'"');
		
		if(!$row){
			
			// Device not found! Error:
			error('device/notfound');
			
		}
		
		if($row['FailedAttempts']>=3){
			// Halt all further processing for this device - it's had too many failed attempts.
			// We need to check if they've been locked out for long enough (lockoutDuration seconds).
			
			// The current time:
			$currentTime=time();
			
			global $lockoutDuration;
			
			// The time this device 'unlocks' at:
			$unlockTime=($row['LastFailedAttempt'] + $lockoutDuration);
			
			// Is the unlock time in the future?
			if( $unlockTime > $currentTime ){
				
				// Yes - Locked out.
				error('device/locked');
				
			}else{
				
				// No - unlock it. We'll reduce failed attempts back to 0 so future requests don't repeat this check.
				$dz->query('update `Bank.Devices` set `FailedAttempts`=0 where ID='.$entityID);
				
			}
			
		}
		
		global $path;
		
		// Must always check for the sequence value:
		$sequence=$row['Sequence'];
		
		// Apply account ID as EP:
		$entityEP=$row['Account'];
		
		// Get the public key:
		$publicKey=$row['Key'];
		
	}else if( isset($header['entity']) ){
		
		// We'll need the database to verify an entity:
		global $dz;
		
		// Get the entity (a domain name):
		$entity=safe('entity',VALID_DOMAIN,$header);
		
		// Get the row:
		$row=$dz->get_row('select `ID`,`Key`,`Type` from `Root.Entities` where Endpoint="'.$entity.'"');
		
		if(!$row){
			
			// Entity not found! Error:
			error('entity/notfound');
			
		}
		
		// Get the entity ID:
		$entityID=$row['ID'];
		
		// Apply endpoint:
		$entityEP=$entity;
		
		// Get the public key:
		$publicKey=$row['Key'];
		
		// Is this a root nodes public key?
		// If it is, that potentially indicates that there's another JWS in the payload.
		if($row['Type']==5){
			
			// Yes - it's a root node. Update isRoot:
			$isRoot=true;
			
		}
		
	}else if( isset($header['pk']) ){
		
		// It's in hex:
		$publicKey=hex2bin(safe('pk',VALID_HEX,$header));
		
	}else{
		
		// Don't know which entity or key was used to sign the data.
		error('field/missing','header.entity');
		
	}
	
	// Ensure that the strings are actually base64 by decoding them:
	$decodedHeader=base64_decode($pHeaderB64,true);
	$decodedPayload=base64_decode($payloadB64,true);
	
	if($decodedHeader===false){
		
		// Invalid input.
		error('field/invalid','protected');
		
	}
	
	if($decodedPayload===false){
		
		// Invalid input.
		error('field/invalid','payload');
		
	}
	
	// Now we have the public key (bytes) to verify the signature (base64) with.
	if(!verify($signature,$pHeaderB64.'.'.$payloadB64,$publicKey)){
		
		// Verify failed.
		// Note that we don't need to clear isRoot, publicKey or entityID
		// because error() exits immediately.
		
		// Importantly, this is a strong indicator of attack attempts.
		// For example, in the Bank API, it indicates the user entered the wrong pin.
		// So, if we're using the bank API, we'll track the # of attempts for the device.
		
		if($apiMode=='bank' && $entityID){
			
			// Track the attempts for this device.
			// First we'll check if this failed attempt will 'stack' or not using the previously obtained
			// information about the failed attempts. Have those attempts expired yet?
			$attempts=$row['FailedAttempts'];
			
			// The current time:
			$currentTime=time();
			
			if($attempts!=0){
				
				global $lockoutDuration;
				
				// The time this device 'unlocks' at:
				$unlockTime=($row['LastFailedAttempt'] + $lockoutDuration);
				
				// Is the unlock time in the future?
				if( $unlockTime <= $currentTime ){
					
					// Nope, it's passed - Clear the attempts counter:
					$attempts=0;
				}
				
			}
			
			// Update the database now:
			$dz->query('update `Bank.Devices` set FailedAttempts='.($attempts+1).',`LastFailedAttempt`='.$currentTime.' where ID='.$entityID);
			
		}
		
		error('signature/invalid');
		
	}
	
	// It verified! JSON decode the payload and protected headers then set to the globals:
	$postedHeader=json_decode($decodedHeader,true);
	$posted=json_decode($decodedPayload,true);
	
	if($postedHeader==null && $decodedHeader!=''){
		
		// The protected header is not valid JSON (as it does actually have one).
		error('json/invalid','protected');
		
	}
	
	if($posted==null && $decodedPayload!=''){
		
		// The payload is not valid JSON (as it does actually have one).
		error('json/invalid','payload');
		
	}
	
	if($sequence){
		
		// Check that the user provided the correct sequence code.
		// They provide it in the protected header. It's there to protect
		// against replay attacks as well as protecting from 3rd party software
		// 'mimicking' a valid private key.
		$providedSequence=safe('seq',VALID_DEVICE,$postedHeader);
		
		if($sequence==$providedSequence){
			
			// Great they provided the right sequence code.
			
			// Time to generate a new one:
			$sequence=randomString(16);
			
			// Send the response header with the next sequence code in it:
			header('Sequence: '.$sequence);
			
			// Update it:
			$dz->query('update `Bank.Devices` set `Sequence`="'.$sequence.'" where ID='.$entityID);
			
		}else{
			
			// Invalid sequence. This can indicate an attacker has stolen a private key
			// or is otherwise trying to post requests.
			// It can also happen if a response containing the next sequence 
			// wasn't delivered to the user, so there is a legitimate case too.
			error('field/invalid','seq');
			
		}
		
	}
	
}

/*
* Subscriptions and transactions both provide product information
* to state what was being purchased. This function ensures it's safe to store
* and responds with a cleaned JSON string.
*/
function cleanProducts($products,$discounts,$notify){
	
	// The cleaned products:
	$cleanProducts=array();

	// Validate each product row.
	foreach($products as $product){
		
		// Check for name (any), quantity (decimal), total (number),upc (optional, number), id (optional, any)
		$name=escape( safe('name',true,$product) );
		$quantity=safe('quantity',VALID_DECIMAL,$product);
		$total=safe('total',VALID_NUMBER,$product);
		$upc=safe('upc',VALID_NUMBER,$product,true);
		$id=escape( safe('id',true,$product,true) );
		
		// Build the clean product:
		$cleanProduct=array(
			'name'=>$name,
			'quantity'=>$quantity,
			'total'=>$total
		);
		
		if($upc){
			// It's got a universal product code:
			$cleanProduct['upc']=$upc;
		}
		
		if($id){
			// It's got a store-specific ID:
			$cleanProduct['id']=$id;
		}
		
		// Add to clean products set:
		array_push($cleanProducts,$cleanProduct);
		
	}

	// The cleaned discounts:
	$cleanDiscounts=array();

	if($discounts){
		
		// Validate each discount next:
		foreach($discounts as $discount){
			
			// Check for name (any), total (number)
			$name=escape( safe('name',true,$discount) );
			$total=safe('total',VALID_NUMBER,$discount);
			
			// Build the clean discount:
			$cleanDiscount=array(
				'name'=>$name,
				'total'=>$total
			);
			
			// Add to clean discounts set:
			array_push($cleanDiscounts,$cleanDiscount);
			
		}

	}
	
	if(!$notify){
		// It's optional.
		$notify='';
	}

	// Encode item info into JSON:
	return json_encode(array('products'=>$cleanProducts,'discounts'=>$cleanDiscounts,'notify'=>$notify));
	
}

?>