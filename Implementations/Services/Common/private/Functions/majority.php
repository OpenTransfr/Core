<?php

/*
* This file contains functionality relating to 
* obtaining a majority during a decision.
*/

/*
* Tests the given set of challenge/signature pairs (JSON formatted strings)
* to see if a majority within the given group has been obtained. Optionally provide the JSON challenge/signature
* pair for 'this' root node ($myPair), which indicates that this root is leading the majority process.
* When that happens, this function responds with a block of JSON which can then be forwarded
* on to every other node for them to agree with the leader that majority has been obtained.
*/
function testMajority($results,$signedData,$myPair=null,$inGroup=null,$error=true){
	
	// Get the set of root public keys, indexed by endpoint. Note that they are raw bytes:
	$publicKeys=getRootKeys($inGroup);
	
	// How many are there?
	$nodesInRoot=count($publicKeys);
	
	if($nodesInRoot<3){
		// This root isn't big enough. It's not able to obtain a majority.
		return false;
	}
	
	
	// The number of root nodes that we have successfully verified a response from.
	
	// The JSON to forward to other nodes. This only occurs when myPair is not null (which indicates
	// that this server is 'leading' the majority process).
	$fullSet='';
	
	if($myPair==NULL){
		
		// We start with 0 - we have to verify all signatures, including our own.
		$verifiedCount=0;
		
	}else{
		
		global $thisEntity;
		
		// It starts with 1 because we know at least this ones signature was successful.
		$verifiedCount=1;
		
		// This route also generates JSON to forward to other nodes too.
		$fullSet='{"'.$thisEntity['Endpoint'].'":'.$myPair;
		
	}
	
	foreach($results as $endPoint=>$result){
		
		// Get this endpoints public key (bytes):
		if(!isset($publicKeys[$endPoint])){
			continue;
		}
		
		// Get the key:
		$endpointKey=$publicKeys[$endPoint];
		
		if(is_array($result)){
			
			// JSON is already loaded.
			$resultJson=&$result;
			
			// Encode it to a string:
			$result=json_encode($resultJson);
			
		}else{
			
			// Load the JSON:
			$resultJson=json_decode($result,true);
			
		}
		
		// Get the signature (base64):
		$resultSig=$resultJson['signature'];
		
		// Verify it:
		if(verify($resultSig,$resultJson['challenge'].$signedData,$endpointKey)){
			
			// Increase the count!
			$verifiedCount++;
			
			if($fullSet!=''){
				// Output the end point plus the JSON response:
				$fullSet.=',"'.$endPoint.'":'.$result;
			}
			
		}
		
	}
	
	// Finish the full set:
	if($fullSet!=''){
		$fullSet.='}';
	}

	// The important check occurs here - do we have a majority? Must be greater than half.
	if($verifiedCount > ($nodesInRoot/2)){
		
		// Majority formed!
		if($fullSet==''){
			return true;
		}
		
		return $fullSet;
		
	}else if($error){
		
		// No majority formed.
		error('majority/notformed');
		
	}
	
	// No majority formed (silent error).
	return false;

}

?>