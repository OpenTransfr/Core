<?php

/*
* Functions in this file are for cryptographic tasks such as signing data.
*/

/*
* Signs the given data, returning the signature (bytes). Lower-S only.
*/
function sign($data,$privateKeyRaw=NULL){
	
	if($privateKeyRaw==null){
		
		global $thisEntity;
		
		// Load the private key if needed (from a hex string):
		if(!isset($thisEntity['PrivateKeyBytes'])){
			
			// Load it now:
			$thisEntity['PrivateKeyBytes']=hex2bin($thisEntity['PrivateKey']);
		
		}
		
		// Grab the raw private key (bytes):
		$privateKeyRaw=$thisEntity['PrivateKeyBytes'];
		
	}
	
	// Get the double hash of the data:
	$msg32 = hash('sha256', hash('sha256', $data, true), true);
	
	// Create a context:
	$ctx=secp256k1_context_create(SECP256K1_CONTEXT_SIGN);
	
	// Sign:
	$signature;
	
	if(secp256k1_ecdsa_sign($ctx, $msg32, $privateKeyRaw, $signature)!=1){
		
		// This is a 500 error. Unable to sign.
		serverError();
		
	}
	
	// Serialize the signature:
	$serialized = '';
	secp256k1_ecdsa_signature_serialize_der($ctx,$signature,$serialized);
	
	return $serialized;
}

/*
* Verifies the given signature (Base64) for the given data using the given public key (bytes). Lower-S only.
*/
function verify($signatureB64,$data,$publicKeyRaw){
	
	if($publicKeyRaw==null){
		
		global $thisEntity;
		
		// Load the public key if needed (from a hex string):
		if(!isset($thisEntity['PublicKeyBytes'])){
			
			// Load it now:
			$thisEntity['PublicKeyBytes']=hex2bin($thisEntity['Key']);
		
		}
		
		// Grab the raw public key (bytes):
		$publicKeyRaw=$thisEntity['PublicKeyBytes'];
		
	}
	
	// Decode the signature from base64:
	$decodedSignature=base64_decode($signatureB64,true);
	
	if($decodedSignature===false){
		
		// Invalid signature.
		error('field/invalid','signature');
		
	}
	
	// Get the double hash of the data:
	$msg32 = hash('sha256', hash('sha256', $data, true), true);
	
	// Create a context:
	$ctx=secp256k1_context_create(SECP256K1_CONTEXT_VERIFY);

	// Load up the public key from its bytes (into $publicKey):
	$publicKey;
	secp256k1_ec_pubkey_parse($ctx,$publicKeyRaw,$publicKey);
	
	// Load up the signature from its bytes (into $signature):
	$signature;
	secp256k1_ecdsa_signature_parse_der($ctx,$decodedSignature,$signature);
	
	if($signature==null){
		// Not a valid signature.
		error('field/invalid','signature');
	}
	
	// Verify:
	return secp256k1_ecdsa_verify($ctx, $msg32, $signature, $publicKey);
	
}

/*
* Generates a new keypair.
*/
function generateKeyPair(){

	// Create a context:
	$ctx=secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

	do{
		
		// Generate random str:
		$privkey = mcrypt_create_iv(32,\MCRYPT_DEV_URANDOM);
	
	// Attempt to verify that it's a valid private key:
	}while(!(bool)secp256k1_ec_seckey_verify($ctx,$privkey));

	$pubkey=null;
	
	$pubkeyRef=null;
	// Create the public key (note: For additional safety, check this equals 1):
	secp256k1_ec_pubkey_create($ctx,$privkey,$pubkeyRef);
	
	// Serialise it:
	secp256k1_ec_pubkey_serialize($ctx,$pubkeyRef,false,$pubkey);
	
	// Done:
	return array('private'=>$privkey,'public'=>$pubkey);
	
}

/*
* Returns a random string of the given length.
*/
function randomString($length){
	$key='';
	$pattern='1234567890abcdefghijklmnopqrstuvwxyz';
	
	for($i=0;$i<$length;$i++){
		$key.=$pattern[rand(0,35)];
	}
	
	return $key;
}

/*
* Returns a random string of hex suitable characters of the given length.
*/
function randomHex($length){
	$key='';
	$pattern='1234567890abcdef';
	
	for($i=0;$i<$length;$i++){
		$key.=$pattern[rand(0,15)];
	}
	
	return $key;
}

?>