<?php

// Creates a delivery address (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Name for this address (it can be blank):
$name=safe('name',VALID_TITLE);

// What type of address is it?
// Valid ones are:
$type=strtolower( safe('type',array('postal','gps','rel')) );

// Get the type-specific content for the location:
$content=safe('content',VALID_ARRAY);

if($type=='postal'){
	
	// Content contains at least:
	// - 'address' (a full textual string, typically including newlines or commas)
	safe('address',VALID_TEXT,$content);
	
	// Optional; a nicely parsed version containing any or all of the following:
	/*
	'apartment', 'property', 'po_box', 'floor', 'unit', 'building',
	'street', 'alley', 'dep_locality', 'locality', 'district',
	'neighbourhood',
	'postal_town', 'county', 'state',
	'zip', 'postcode', 'country',
	'box', 'block',
	'name', 'company', 'department', 'position'
	'info' (e.g. around the back)
	
	*/
}else if($type=='gps'){
	
	// GPS. Intended for automated deliveries by drone or road.
	// Contains at least:
	// - 'lat'
	// - 'lon'
	// - 'alt'
	
	safe('lat',VALID_DECIMAL,$content);
	safe('lon',VALID_DECIMAL,$content);
	safe('alt',VALID_DECIMAL,$content);
	
	// Optional:
	// - 'accuracy'. how accurate the location values are, in m.
	// - 'info'. Custom JSON formatted data. May contain, for example, a drone landing pad ID or
	// specific path guidance from the defined point onwards.
	// An automated delivery service is required to forward this info to 
	// e.g. a landing pad, if one is used, as well as delivery specific information.
	// For example: A drone delivers to a communal landing pad which is located at the given GPS coords.
	// The drone releases the package and forwards the information to the pad itself which can then further
	// route the delivery to e.g. the users safe deposit box.
	
}else{
	
	// Relative. A position relative to some other object (for example, on a moving boat).
	// Contains at least:
	// - 'x', 'y', 'z' (in meters); 'to', the moving object reference. Moving object references are 
	// intended to be the same as commodity tags in order for them to be issued by a variety of different
	// organisations. However, that would be operated by an entirely separate system.
	safe('x',VALID_DECIMAL,$content);
	safe('y',VALID_DECIMAL,$content);
	safe('z',VALID_DECIMAL,$content);
	safe('to',VALID_NAME,$content);
	
	// As with gps, 'accuracy' and 'info' are optional and have the same concept here.
	
}

// Turn content into a JSON formatted string:
$content=json_encode($content);

// Note: Newest locations always take top preference.
// They do this by adding 1 to all the other locations owned by this account like so:
$dz->query('update `Bank.Account.Locations` set `Preference`=`Preference`+1 where `Account`='.$verifiedAccount);

// Insert the row now (which will have a pref of 0 by default)
// Note that everything except $content has been escaped.
$dz->query('insert into `Bank.Account.Locations`(`Name`,`Account`,`Type`,`Content`) values("'.
			$name.'",'.$verifiedAccount.',"'.$type.'","'.escape($content).'")');

// Get the ID:
$id=$dz->insert_id();

// Ok!
echo '{"id":'.$id.'}';

?>