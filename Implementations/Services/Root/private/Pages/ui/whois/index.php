<?php

if (!isset($_GET['query'])){
	exit();
}

// Get the query:
$query = $_GET['query'];

// Get the whois helpers:
include_once('whois.php');

// Create the whois helper:
$whois = new Whois();

// Lookup and get a nice formatted array:
$result=$whois->Formatted($query);

// Got any results?
if(!$result){
	echo '{}';
	exit();
}

if(empty($result)){
	
	// Try again, this time 'hinted' that the second level part of the domain
	// is likely just a short domain name:
	$result=$whois->Formatted($query,true);
	
	if(!$result){
		echo '{}';
		exit();
	}
	
}

// Encode the result!
echo json_encode($result);

?>