<?php

// Commodity create API. Must be signed by the parent group if it is restricted.

// This endpoint receives posted JSON:
postedTo();

// Get the tag, name and description.

// Tag must be a valid tag only. Punycode (for internationalized tags) is supported:
$tag=safe('tag',VALID_DOMAIN);

// Policy must be either 'public', 'closed' or 'reviewed'
$policy=safe('policy',array('closed'=>0,'public'=>1,'reviewed'=>2));

// Divisor must be a positive non-zero number:
$divisor=(int)safe('divisor',VALID_NUMBER);

if($divisor==0){
	// They specified 0 - this isn't valid:
	error('field/invalid','divisor');
}

// Get the issuers endpoint:
$issuer=safe('issuer',VALID_DOMAIN);

$name=safe('name',VALID_ARRAY); // These are sets/ we want them as-is.
$description=safe('description',VALID_ARRAY); // These are sets/ we want them as-is.

// Get the English name/ description.
$name_en=safe('en',VALID_TITLE,$name);
$description_en=safe('en',VALID_TEXT,$description);

// Does it already exist? Two commodities can't use the same tag.
$exists=$dz->get_row('select ID from `Root.Commodities` where Tag="'.$tag.'"');

if($exists){
	
	// It exists - stop there.
	error('commodity/exists');
	
}

// Does the future issuer exist?
$exists=$dz->get_row('select ID from `Root.Entities` where Endpoint="'.$issuer.'"');

if(!$exists){
	
	// Didn't find the entity that wants to issue it.
	error('entity/notfound');
	
}

// Update issuer with the (local) ID:
$issuerID=$exists['ID'];

// $verifiedEntity must match the parent commodity.

// Find the parent commodity and then check to see if its issuer signed the request.
// For example, if we're requesting 'currency.usd', then the issuer of 'currency'
// must have been the one signing this request.

$parentIssuer=findParent($tag);

if(!$parentIssuer){
	// No suitable parent commodity was not found.
	error('commodity/notfound');
}

// Was that parent issuer the same one who signed this request?
if($verifiedEntity!=$parentIssuer['ID']){
	// Nope! Only the parent can sign the request.
	error('entity/notparent');
}

// Create the commodity now:
$dz->query('insert into `Root.Commodities` (`Tag`,`Description_en`,`Name_en`,`Divisor`,`Issuer`,`Policy`) values ("'.$tag.'","'.$description_en.'","'.$name_en.'",'.$divisor.','.$issuerID.','.$policy.')');

changed('com',array(
	'tag'=>$tag,
	'description'=>array('en'=>$description_en),
	'name'=>array('en'=>$name_en),
	'divisor'=>$divisor,
	'issuer'=>$issuer,
	'policy'=>$policy
));

// Is this already forwarded? If so, stop there.
if($forwardedFromRoot!=0){
	
	// Some other root forwarded the request here. Just quit:
	exit();
	
}

// Forward this request to other root nodes.
forward();

?>