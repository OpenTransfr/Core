<?php

/*
* This file contains functionality relating to commodity tags.
*/

/*
* Finds the ID of the entity which is the parent of the given tag.
* For example, 'currency' is the parent of 'currency.gbp'.
* In that case, this function returns the ID of whoever issues 'currency'.
*/
function findParent($tag){
	
	global $dz;
	
	$pieces=explode('.',$tag);
	
	// How many pieces are there? E.g. currency.usd has 2.
	$pieceCount=count($pieces);

	// For all but one piece..
	for($i=1;$i<$pieceCount;$i++){
		
		// Pop the last one:
		array_pop($pieces);
		
		// Build the tag:
		$parentTag=implode('.',$pieces);
		
		if($parentTag==''){
			break;
		}
		
		// Try and find it:
		$parentRow=$dz->get_row('select `Issuer`,`Policy` from `Root.Commodities` where Tag="'.$parentTag.'"');
		
		if($parentRow){
			
			// Found it!
			return array('ID'=>$parentRow['Issuer'],'Policy'=>$parentRow['Policy']);
			
		}
		
	}
	
	return NULL;
}

?>