<?php

// Commodity request API. This is the consumer facing part of the issue API.
// They submit a request to the parent issuer. The parent issuer then either immediately
// accepts it (unrestricted ones) or performs some custom verification.

// This endpoint receives posted JSON:
postedTo();

// Was the request signed by an entity?
if($verifiedEntity==0){
	
	// Nope! Must be signed by an entity (the one that wants to issue this commodity).
	error('entity/required');
	
}

// Get the tag, name and description.

// Tag must be a valid tag only. Punycode (for internationalized tags) is supported:
$tag=strtolower( safe('tag',VALID_DOMAIN) );

// Policy must be either 'public', 'closed' or 'reviewed'
$policy=safe('policy',array('closed'=>0,'public'=>1,'reviewed'=>2));

// Divisor must be a positive non-zero number:
$divisor=(int)safe('divisor',VALID_NUMBER);

if($divisor==0){
	// They specified 0 - this isn't valid:
	error('field/invalid','divisor');
}

$name=safe('name',VALID_ARRAY);
$description=safe('description',VALID_ARRAY);

// Check the English name/ description exist.
safe('en',VALID_TITLE,$name);
safe('en',VALID_TEXT,$description);

// Does it already exist? Two commodities can't use the same tag.
$exists=$dz->get_row('select ID from `Root.Commodities` where Tag="'.$tag.'"');

if(isset($exists['ID'])){
	
	// It exists - stop there.
	error('commodity/exists');
	
}

// Find the parent commodity and then check if 'this' is actually its issuer.
// For example, if we're requesting 'currency.usd', then the issuer of 'currency'
// must have been the one receiving this request.

$parentIssuer=findParent($tag);

if($parentIssuer==null || $parentIssuer['ID']!=$thisEntity['ID']){
	
	// I'm not the issuer of that!
	error('entity/notissuer');
	
}

// Get its policy. It's either 0 (closed), 1 (public) or 2 (reviewed).
// The policy defines how a particular parent commodity allocates sub-commodities.
// For example, 'currency' is reviewed (2) because we don't want just anyone allocating a fiat currency.
// 'x' however is public (1) because it's a testing tag and is designed to be open for all.
$parentPolicy=$parentIssuer['Policy'];

if($parentPolicy!=1 && $parentPolicy!=2){
	
	// Closed. Can't register a sub-commodity using this API for the given parent tag.
	error('commodity/closed');
	
}

// Valid request so far! At this point, we now need to make sure the future issuer
// actually supports this issue API. We do that by requesting the commodity/status API with 'pending'.

// Get it's endpoint (note: the request contains this endpoint in the JWS header, 
// but we'll get it from the database for now):
$entity=$dz->get_row('select Endpoint from `Root.Entities` where ID='.$verifiedEntity);

if(!$entity){
	
	// Not possible - it wouldn't have verified a few ms ago, but just in case!
	error('entity/notfound');
	
}

// Get their endpoint:
$futureIssuer=$entity['Endpoint'];

// Time to request the status API and see what we get back:
$success=issuerStatus($futureIssuer,$tag,'pending');

if(!$success){
	
	// Issuer API not supported.
	error('entity/noissuerapi');
	
}

// The future issuer supports the issuer API and the request is valid.
// This is where this API becomes implementation specific. You can either
// instantly accept the request, which this particular implementation will
// focus on, or delay the request and do e.g. a manual or automated verification
// to make sure a particular entity is suitable/ trusted to issue a commodity.

if($parentPolicy==1){
	
	// Instant accept. The policy for the parent tag is 'public' meaning
	// it automatically accepts anything.
	
	// ----------------------------
	// Accepting of a valid request
	// ----------------------------
	
	// - Run this same code when accepting reviewed requests.
	
	// Get the policy name:
	$policyName='closed';
	
	if($policy==1){
		
		// Public policy.
		$policyName='public';
		
	}else if($policy==2){
		
		// Reviewed.
		$policyName='reviewed';
		
	}
	
	// Now we call the root commodity/create API:
	$rootCreate='{"tag":"'.$tag.'","policy":"'.$policyName.'","description":'.json_encode($description)
		.',"name":'.json_encode($name).',"divisor":'.$divisor.',"issuer":"'.$futureIssuer.'"}';

	// Send the request to root:
	$error;
	$response=callRoot('commodity/create',$rootCreate,$error);
	
	if($error){
		// Failed to request root.
		error('root/failed',$error);
	}
	
	// It was successful! futureIssuer is now the issuer of $tag.
	// Let's tell them about it using the status API again:
	issuerStatus($futureIssuer,$tag,'success');

	// -----------------------------
	
	// Let the requester know that the request got here:
	echo '{"status":"OK"}';
	
}else{
	
	// Reviewed request (2). Note that it's not anything else (i.e. closed)
	// because we do a check for 1 or 2 further up.
	
	// Add it to a pending table instead, 
	// review it however you wish, then call the above with success/reject.
	$dz->query('insert into `Issuer.Commodities.Pending` '
	 		.'(`Tag`,`Description`,`Name`,`Divisor`,`Issuer`,`Policy`) values ("'
			.$tag.'","'.escape( json_encode($description),false).'","'.escape( json_encode($name),false).'",'.$divisor.','.$verifiedEntity.','.$policy.')');
	
	// Let the requester know that the request got here:
	echo '{"status":"REVIEW"}';
	
}

?>