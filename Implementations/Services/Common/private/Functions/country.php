<?php

/*
* This file contains functionality relating to country codes.
*/

/*
* Finds the ID of the country which uses the given country code (ISO2).
* Note that the code must already be escaped (i.e. it should originate from safe()).
*/
function getCountry($code){
	
	global $dz;
	
	if(strlen($code)!=2){
		
		// Invalid input.
		error('field/invalid','country');
		
	}
	
	// Make sure it's uppercase:
	$code=strtoupper($code);
	
	// Get the row:
	$row=$dz->get_row('select country_id from countries where iso2="'.$code.'"');
	
	if(!$row){
		
		// Country was not found.
		error('country/notfound');
		
	}
	
	return $row['country_id'];
}

?>