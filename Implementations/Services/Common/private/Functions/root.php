<?php

/*
* The functions in this file deal with sending a message to root nodes
* within the same group as this node.
*/


/*
* If root nodes errored, the messages are added here.
*/
$rootErrors=null;

/*
* Forwards the message sent to this root to other root nodes in the same group.
*/
function forward($decodeJson=false){
	
	// Base64 the payload:
	$payload=base64_encode( file_get_contents('php://input') );
	
	// Base64 the protected header, which contains only the fwd field:
	$pHeader='eyJmd2QiOjF9'; // base64_encode('{"fwd":1}');
	
	return sendToRoot($payload,$pHeader,$decodeJson);
	
}

/*
* A mapping of root node (in this group, unless an ID is given) to it's public key. Includes this node too.
* Note that you should cache this rather than reloading it on each request.
* This set is created by getRootKeys().
*/
$rootKeys=NULL;

function getRootKeys($inGroup=null){
	
	// Get the db etc:
	global $rootKeys,$thisEntity,$dz;
	
	// Create a new array:
	$keySet=array();
	
	if($inGroup==null){
		
		// Already set?
		if($rootKeys!=null){
			return $rootKeys;
		}
		
		// Update in group to this one:
		$inGroup=$thisEntity['Group'];
	
	}
	
	// Get all the entities in this same group:
	$all=$dz->get_list('select `Key`,`Endpoint` from `Root.Entities` where `Group`='.$inGroup.' and `Type`=5');
	
	// For each one..
	foreach($all as $entity){
		
		// Add the entity to the keySet set, indexed by endpoint:
		$keySet[$entity['Endpoint']]=$entity['Key'];
		
	}
	
	if($inGroup==$thisEntity['Group']){
		
		// Set the root keys to the set:
		$rootKeys=$keySet;
		
	}
	
	// Return the set:
	return $keySet;
	
}

/*
* Calls a root API. Typically used by banks and issuers.
*/
function callRoot($api,$payload,&$error){
	
	// Get a random root node:
	$root=randomRoot();
	
	$sendError;
	// Send now:
	$result=sendTo($root['Endpoint'],$api,$payload,$sendError);
	
	// Update error if there was one:
	$error=$sendError;
	
	return $result;
}

/*
* Gets a random root node within the given group (or 'this' group if none is given).
*/
function randomRoot($rootGroup=null){
	
	global $dz;
	
	// Got a group?
	if(!$rootGroup){
		
		global $thisEntity;
		
		// Use the group that this node is in:
		$rootGroup=$thisEntity['Group'];
	}
	
	// Grab a random row:
	return $dz->get_row('select `Key`,`Endpoint` from `Root.Entities` where `Group`='.$rootGroup.' and `Type`=5 order by rand() limit 1');
	
}

/*
* Sends the given message to other root nodes in the same group as this one (unless
* rootGroup is set to the ID of another group). If you set a specific root group,
* the request is sent to a random node in the given group.
*/
function sendToRoot($payload,$pHeader,$decodeJson=false,$location=null,$rootGroup=null){
	
	global $thisEntity,$path,$dz,$rootKeys;
	
	if($location==null){
		// Use the current path as the location (used by forwarding):
		$location=$path;
	}
	
	// Build the message:
	$message='{"header":{"entity":"'.$thisEntity['Endpoint'].'"},"protected":"'.
		$pHeader.'","payload":"'.$payload.'","signature":"'.base64_encode( sign($pHeader.'.'.$payload) ).'"}';
	
	// Forward to all other root nodes in my group.
	$group=$thisEntity['Group'];
	
	// The responses:
	$responses=array();
	
	// Are we using this group or another one?
	if($rootGroup==null){
		
		// Get the local root keys:
		if($rootKeys==null){
			// Root keys holds a mapping of endpoint to key.
			// We'll use it here to get those endpoints.
			getRootKeys();
		}
		
		// Use this group:
		$keySet=$rootKeys;
		
	}else{
		
		// Sending to some other root group.
		// Only need to send to one node here.
		// (If we send to all of them, it defeats the object of having groups!)
		// Get a random node:
		$randomRow=randomRoot($rootGroup);
		
		// Create the set:
		$keySet=array();
		
		// Add our single entry to it:
		$keySet[$randomRow['Endpoint']]=$randomRow['Key'];
		
	}
	
	global $rootErrors;
	
	// Clear root errors:
	$rootErrors=null;
	
	// For each one..
	foreach($keySet as $endPoint=>$rootKey){
		
		if($endPoint==$thisEntity['Endpoint']){
			// Don't send to myself!
			continue;
		}
		
		// Send it a message:
		// Warning! This will take a while with crowded roots 
		// as it does the requests one after another. Use parallel requests instead.
		$error;
		$response=post('https://'.$endPoint.'/'.$location,$message,$error);
		
		// Got a response?
		if($error){
			
			// The remote node emitted an error.
			
			if(!$rootErrors){
				$rootErrors=array();
			}
			
			// Add the error:
			array_push($rootErrors,$error);
			
		}else{
			
			if($decodeJson){
				// Add to result:
				$responses[$endPoint]=json_decode($response,true);
			}else{
				// Add to result as-is (default):
				$responses[$endPoint]=$response;
			}
			
		}
		
	}
	
	// Return the responses:
	return $responses;
	
}

/*
* Sends the given JSON message to the API at the given location.
*/
function sendTo($endpoint,$api,$payload,&$error){
	
	global $thisEntity;
	
	// Encode the payload:
	$payload=base64_encode($payload);
	
	// Create a request ID:
	$id=randomHex(20).'@'.time().'000';
	
	// Build the protected header:
	$pHeader=base64_encode('{"id":"'.$id.'","pubsig":"'.base64_encode(sign($id)).'"}');
	
	// Build the message:
	$message='{"header":{"entity":"'.$thisEntity['Endpoint'].'"},"protected":"'.$pHeader.'","payload":"'.
		$payload.'","signature":"'.base64_encode( sign($pHeader.'.'.$payload) ).'"}';
	
	// Post it off:
	$postError;
	$response=post('https://'.$endpoint.'/v1/'.$api,$message,$postError);
	
	if($postError){
		$error=$postError;
		return null;
	}
	
	return $response;
	
}

?>