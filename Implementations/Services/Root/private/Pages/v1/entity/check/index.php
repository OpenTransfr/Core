<?php

// Entity visibility API.

postedTo();

// Get the entity DNS address:
$entityEndpoint=safe('entity',VALID_DOMAIN);

// Make sure it's actually an entity:
$entity=$dz->get_row('select `ID` from `Root.Entities` where Endpoint="'.$entityEndpoint.'"');

// Exists?
if(!$entity){
	
	// Nope!
	error('entity/notfound');
	
}

echo '{"ips":[';

function startsWith($haystack, $needle) {
	// Search backwards starting from haystack length characters from the end
	return strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

// DNS lookup and respond with the results:
$records=dns_get_record($entityEndpoint, DNS_A | DNS_AAAA | DNS_TXT);

// How many records?
$count=count($records);

$sig='';

$first=true;

// For each one..
for($i=0;$i<$count;$i++){
	
	// Get the record:
	$record=$records[$i];
	
	$type=$record['type'];
	
	if($type=='TXT'){
		
		// Check if this is an OpenTX signature.
		// If it is, then we can directly verify the set of IP/ IPV6 records.
		$txt=$record['txt'];
		
		if(startsWith($txt,'opentx sig:')){
			
			// We have a base64 signature!
			$sig=substr($txt,11);
			
		}
		
		// Make sure commas don't show up:
		continue;
		
	}
	
	if($first){
		$first=false;
	}else{
		// Separate with commas:
		echo ',';
	}
	
	// Output the IP (either V4 or V6):
	if($type=='A'){
		echo '"'.$record['ip'].'"';
	}else{
		echo '"'.$record['ipv6'].'"';
	}
	
}

// This signature can be used to directly verify the set of IP addresses
// It's obtained from a TXT record at the endpoint.
echo '],"signature":"'.$sig.'"}';

?>