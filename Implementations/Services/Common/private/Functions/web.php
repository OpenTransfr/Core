<?php

/*
* Functions in this file are for perfoming HTTP requests.
*/

/*
* POST's some given JSON data to the given URL.
*/
function post($url,$json,&$error){
	
	// Start a curl request:
	$ch=curl_init();

	// Set the url and the JSON data:
	curl_setopt($ch,CURLOPT_URL, $url);
	
	// Don't echo. Instead, return the response from curl_exec.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	
	// JSON post body so it needs to be custom:
	curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');
	
	// The HTTP headers:
	curl_setopt($ch, CURLOPT_HTTPHEADER,
		array('Content-Type: application/json','Content-Length: '.strlen($json))
	);
	
	// The JSON itself.
	curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
	
	// POST now:
	$result=curl_exec($ch);
	
	// Did it error?
	$status_code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
	
	// Tidy up:
	curl_close($ch);
	
	if($status_code!=200){
		
		// It failed - the specification only allows 200 and 400 - return null and set $error.
		$error=$result;
		return null;
		
	}
	
	// Return the result as-is:
	return $result;
	
}

/*
* Performs a HTTP GET request.
*/
function get($url){
	
	// Start a curl request:
	$ch=curl_init();

	// Set the url:
	curl_setopt($ch,CURLOPT_URL, $url);
	
	// Don't echo. Instead, return the response from curl_exec.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	
	// GET now:
	$result=curl_exec($ch);
	
	// Did it error?
	$status_code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
	
	// Tidy up:
	curl_close($ch);
	
	if($status_code!=200){
		
		// It failed! 200 only.
		return null;
		
	}
	
	// Return the result as-is:
	return $result;
	
}

?>