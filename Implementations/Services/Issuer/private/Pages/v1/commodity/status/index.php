<?php

// Commodity create API. Must be signed by the parent group if it is restricted.

// This endpoint receives posted JSON:
postedTo();

// Get the tag, name and description.

// Tag must be a valid tag only. Punycode (for internationalized tags) is supported:
$tag=strtolower( safe('tag',VALID_DOMAIN) );

// Get the status (lowercase only):
$status=strtolower( safe('status',VALID_ALPHA) );

// The reason (optional):
$reason=safe('reason',VALID_ARRAY,null,true); // These are sets.

// $verifiedEntity must match the parent commodity.

// Find the parent commodity and then check to see if its issuer signed the request.
// For example, if we're requesting 'currency.usd', then the issuer of 'currency'
// must have been the one signing this request.

// Find the parent:
$parentIssuer=findParent($tag);

if($parentIssuer==null){
	// No suitable parent commodity was not found.
	error('commodity/notfound');
}

// Was that parent issuer the same one who signed this request?
if($verifiedEntity!=$parentIssuer['ID']){
	// Nope! Only the parent can sign the request.
	error('entity/notparent');
}

// Ok! At this point, the parent entity has told us the status of our request to issue 'tag'.
// Do something with status (implementation specific) - e.g. send an email to server admin
// to say that you can now issue 'tag', or it got rejected (along with the reason).

// Must respond with a JSON OK, exactly like this:
echo '{"result":"OK"}';

?>