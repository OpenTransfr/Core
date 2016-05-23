<?php

// Extended API. Gets information about a particular commodity.

postedTo();

// Tracks if we've already outputted a commodity.
// It's used to output commas between them.
$firstCommodity=true;

/*
* Outputs all information about the given commodity.
*/
function outputCommodity($tag){
	
	global $firstCommodity,$dz;
	
	// Get the commodity info. Might not exist; in that case, we just respond with the parent:
	$row=$dz->get_row('select `Tag`,`Description_en`,`Name_en`,`Divisor`,`Policy`,`Root.Entities`.`Endpoint` as `Issuer`,`Root.Entities`.`Name` as `IssuerName` from `Root.Commodities` left join `Root.Entities` on `Root.Entities`.`ID` = `Root.Commodities`.`Issuer` where `Root.Commodities`.`Tag`="'.$tag.'"');
	
	if(!$row){
		return false;
	}
	
	if($firstCommodity){
		$firstCommodity=false;
	}else{
		echo ',';
	}
	
	// Output the header:
	echo '"'.$row['Tag'].'":{';
		
		$firstColumn=true;
		
		// For each column in the row..
		foreach($row as $field=>$value){
			
			// Add commas between the columns:
			if($firstColumn){
				$firstColumn=false;
			}else{
				echo ',';
			}
			
			echo '"'.$field.'":"'.escape($value).'"';
			
		}
	
	echo '}';
	
	return true;
	
}

// Get the commodity tag:
$tag=safe('tag',VALID_DOMAIN);

echo '{';

// Output it:
outputCommodity($tag);

// Find the parents and output those:
$pieces=explode('.',$tag);

// How many pieces are there? E.g. currency.usd has 2.
$pieceCount=count($pieces);

// For all but one piece..
for($i=1;$i<$pieceCount;$i++){
	
	// Pop the last one:
	array_pop($pieces);
	
	// Build the tag:
	$parentTag=implode('.',$pieces);
	
	// Try and output it too:
	outputCommodity($parentTag);
	
}

echo '}';

?>