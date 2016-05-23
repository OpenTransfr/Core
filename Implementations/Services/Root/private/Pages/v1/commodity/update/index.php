<?php

// Commodity update API. Must be signed by the current issuer.

// Receives a JWS:
postedTo();

// Get the tag:
$tag=safe('tag',VALID_DOMAIN);

// Get the commodity:
$commodity=$dz->get_row('select ID,Issuer from `Root.Commodities` where Tag="'.$tag.'"');

// Does it exist?
if(!$commodity){
	
	// Commodity was not found.
	error('commodity/notfound');
	
}

// Get the ID of the issuer:
$issuerID=$commodity['Issuer'];

// Is the entity that signed this request the issuer of this commodity?
if($verifiedEntity!=$issuerID){
	
	// Not signed by the current issuer.
	error('entity/notissuer');
	
}

// Ok! Get the new values for the fields. Note that all are optional.

// Divisor must be a positive non-zero number:
$divisor=safe('divisor',VALID_NUMBER,null,true);

if($divisor!==NULL){
	
	$divisor=(int)$divisor;
	
	if($divisor==0){
		// They specified 0 - this isn't valid:
		error('field/invalid','divisor');
	}
	
}

// New issuer?
$newIssuer=safe('issuer',VALID_DOMAIN,null,true);
$newIssuerID=null;

if($newIssuer!=null){
	
	// Get the local ID for this entity:
	$entityRow=$dz->get_row('select ID from `Root.Entities` where Endpoint="'.$newIssuer.'"');
	
	if(!$entityRow){
		// Entity was not found.
		error('entity/notfound');
	}
	
	// Update the ID:
	$newIssuerID=$entityRow['ID'];
	
}

// Policy must be either 'public', 'closed' or 'reviewed'
$policy=safe('policy',array('closed'=>0,'public'=>1,'reviewed'=>2));

// New name/ description?
$name=safe('name',VALID_ARRAY,null,true); // These are sets and it's optional.
$description=safe('description',VALID_ARRAY,null,true); // These are sets and it's optional.

// Get the English name/ description.
$name_en=null;
$description_en=null;

if($name!=null){
	
	// En is required if they're declaring a new name.
	$name_en=safe('en',VALID_TITLE,$name);
	
}

if($description!=null){
	
	// En is required if they're declaring a new description.
	$description_en=safe('en',VALID_TEXT,$description);
	
}

// Get the SQL changes string (errors if there are none):
$changes=changes(array('Issuer'=>$newIssuerID,'Divisor'=>$divisor,'Name_en'=>$name_en,'Description_en'=>$description_en,'Policy'=>$policy));

// Run the query now:
$dz->query('update `Root.Commodities` set '.$changes.' where ID='.$commodity['ID']);

changed('com',array(
	'Tag'=>$tag,
	'Description'=>array('en'=>$description_en),
	'Name'=>array('en'=>$name_en),
	'Divisor'=>$divisor,
	'Issuer'=>$newIssuer,
	'Policy'=>$policy
));

?>