<?php

// This occurs when every root node has signed to say it believes the domain name belongs to the requester.
// A root node collected all those signatures and then sent the whole lot to every root node.
// This allows them to independently form a proof of majority.
// In this case, when the majority is successful, an entity is created.

// Always JSON posted here:
postedTo();

if($forwardedFromRoot==0){
	
	// Must have been forwarded by a root node.
	error('entity/notroot');
	
}

// Domain must be a valid domain name only:
$domain=safe('domain',VALID_DOMAIN);

// Get the challenges:
$challenges=safe('challenges',VALID_ARRAY);

// Get the pending request:
$row=$dz->get_row('select * from `Root.Entities.Pending` where `Endpoint`="'.$domain.'"');

if(!$row){
	
	// Not found!
	error('entity/notfound');
	
}

// Get the public key bytes:
$publicKey=$row['Key'];

// Get the public key in hex:
$hexPublicKey=bin2hex($publicKey);

// Test for a majority. This time we check all signatures including our own
// because the whole set came from somewhere else (we're not leading the majority process here).
testMajority($challenges,$hexPublicKey);

// Majority formed!

// Remove from pending:
$dz->query('delete from `Root.Entities.Pending` where `Endpoint`="'.$domain.'"');

// Transfer the entity to being an official entity:
$dz->query('insert into `Root.Entities` (`Key`,`Endpoint`,`Type`,`Group`,`Name`,`Country`) values (unhex("'.$hexPublicKey.'"),"'.$domain.'",'.$row['Type'].','.$thisEntity['Group'].',"'.$row['Name'].'",'.$row['Country'].')');

// Create the change event too:
changed('entity',array(
	'key'=>$hexPublicKey,
	'type'=>$row['Type'],
	'endpoint'=>$domain,
	'group'=>$thisEntity['Group'],
	'name'=>$row['Name'],
	'country'=>$row['Country']
));

?>