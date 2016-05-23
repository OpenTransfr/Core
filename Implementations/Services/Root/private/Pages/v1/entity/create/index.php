<?php

// Entity creation API.

// This endpoint receives posted JSON:
$public_key=postedTo(true);

// Get the domain, public key and entity type.

// Domain must be a valid domain name only:
$domain=safe('domain',VALID_DOMAIN);

if($domain==''){
	// Must be set:
	error('field/invalid','domain');
}

// The entity type is a word and must match exactly. Note that $entity_type will be a number:
$entity_type=safe('type',array('bank'=>4,'verifier'=>2,'merchant'=>1,'issuer'=>3,''=>0));

// The country is a 2 character country code:
$countryCode=safe('country',VALID_ALPHA);

// The name of the entity:
$name=safe('name',VALID_TITLE);

// Does it already exist? Two entities cannot share the same endpoint domain.
$exists=$dz->get_row('select ID from `Root.Entities` where Endpoint="'.$domain.'"');

if($exists){
	
	//It exists - stop there.
	error('entity/exists');
	
}

// Get the public key as hex:
$hexKey=bin2hex($public_key);

// Generate the challenge data:
$challenge=randomString(45);

// Get the ID of the country (errors if it fails):
$country=getCountry($countryCode);

// Delete the pending entity if it already exists:
$dz->query('delete from `Root.Entities.Pending` where `Endpoint`="'.$domain.'"');

// Create the pending entity now:
$dz->query('insert into `Root.Entities.Pending` (`Key`,`Type`,`Endpoint`,`Challenge`,`Time`,`Name`,`Country`,`RequestID`,`RequestTime`,`RequestSig`) values (UNHEX("'.
		$hexKey.'"),'.$entity_type.',"'.$domain.'","'.$challenge.'",'.time().',"'.$name.'",'.$country.',"'.
		$signedPublicID['id'].'","'.$signedPublicID['time'].'","'.$signedPublicID['signature'].'")');

// Build my challenge JSON:
$challenge='"'.$challenge.'"';

// Is this already forwarded? If so, respond at this point and stop there.
if($forwardedFromRoot!=0){
	
	// Some other root forwarded the request here. Just return my challenge:
	echo $challenge;
	exit();
	
}

// Forward this request to other root nodes, stating that this root node is leading it.

$results=forward();

// Add my result too:
$endPoint=$thisEntity['Endpoint'];
$results[$endPoint]=$challenge;

// Start outputting the ACME-style challenge. There's only one there at the moment.
// The user must first sign token using their private key.
// Then, they upload this partial JWS: {"payload":{the token},"signature":{their signature}}
// To this location:
$at='https://'.$domain.'/opentx/'.substr($hexKey,0,32).'.json';

// When they've done that, they GET the given uri.

echo '{"challenges":[{"type":"http","at":"'.$at.'","uri":"https://'.$endPoint.'/v1/entity/challenge/verify?domain='.$domain.'","token":"';

$token='{';

// For each one..
$first=true;

foreach($results as $endPoint=>$result){
	
	// Make sure commas are added between them:
	if($first){
		$first=false;
	}else{
		$token.=',';
	}
	
	// Output the end point plus the JSON response:
	$token.='"'.$endPoint.'":'.$result;
	
}

// The token itself is base64 encoded, so the user can easily sign it:
echo base64_encode($token.'}');

echo '"}]}';


?>