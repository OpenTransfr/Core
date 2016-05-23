<?php

// List all contracts within a given range.

// Get all the contract data:
$contracts=$dz->get_list('select * from `Root.Contracts`');
$contractActions=$dz->get_list('select * from `Root.Contracts.Actions`');

// Group actions into their contract:
$actionMap=array();

foreach($contractActions as $action){
	
	// Get the contract ID:
	$contractID=$action['Contract'];
	
	// Does the set of actions exist yet?
	if(!isset($actionMap[$contractID])){
		
		// Create the set of actions for a given contract:
		$actionMap[$contractID]=array();
		
	}
	
	// Add the action to the array:
	array_push($actionMap[$contractID],array('Type'=>$action['Type'],'Values'=>$action['Values']));
	
}

// Build the list now!
showList($contracts,array('Key','Name_en','Description_en','Created','Expires','ApplyOn','CanVote','Status','Actions'),function(&$row){
	
	$row['Key']=base64_encode($row['Key']);
	
	// Get the contract ID:
	$contractID=$row['ID'];
	
	// Get it's action set from the map, if there is one:
	if(isset($actionMap[$contractID])){
		
		// Set it to the actions column:
		$row['Actions']=$actionMap[$contractID];
		
	}else{
		
		// Otherwise it's just an empty array:
		$row['Actions']=array();
		
	}
	
});

?>