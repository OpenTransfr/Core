<?php

// Full commodity information. This API is specific to particular issuers.

// Get the commodity:
$tag=safe('tag',VALID_DOMAIN,$_GET);

// Is this tag something we issue?
$parent=findParent($tag);

if($parent['ID']!=$thisEntity['ID']){
	
	// I'm not the issuer of that!
	error('entity/notissuer');
	
}

// Get the row, if it exists:
$row=$dz->get_row('select * from `Issuer.Commodities` where `Tag`="'.$tag.'"');

$content=null;

if($row){
	
	// Extra content is potentially available.
	$content=$row['Content'];
	
}

if(!$content){
	
	// No extra content available.
	$content='null';
	
}

// Instead of 'content', you may instead respond with 'api' and a URL, like so:
// {"tag":"land.earth.wwks.57834","api":"https://land.gov.uk/api/warwickshire/57834"}

echo '{"tag":"'.$tag.'","content":'.$content.'}';

?>