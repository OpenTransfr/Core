<?php

/*
* Functionality that handles balances.
*/

/*
* Receives the given amount into the given hex address.
*/
function receiveLocked($hexAddress,$amount,$commodity,$entityID=0){
	
	global $dz;
	
	// Find that address - if it exists, it must be the same commodity. If it doesn't exist, create it now.
	$com=$dz->get_row('select `Commodity`,`Entity` from `Root.Balances` where Key=UNHEX("'.$hexAddress.'")');

	if($com){
		
		// Make sure it matches and so does the entity:
		if($com['Commodity']!=$commodity){
			
			// Wrong commodity!
			error('balance/wrongcommodity');
			
		}
		
		if($entityID!=0 && $com['Entity']!=$entityID){
			
			// Wrong owner!
			error('balance/wrongowner');
			
		}
		
		// Lock the amount now. This is because if the balance goes empty during this issue request
		// the balance row is deleted. If we lock the amount, that can't happen.
		$dz->query('update `Root.Balances` set LockedAmount=LockedAmount+'.$amount.' where Key=UNHEX("'.$hexAddress.'")');
		
	}else{
		
		// Create it now, with the locked amount:
		$dz->query('insert into `Root.Balances`(`Commodity`,`Key`,`Entity`,`LockedAmount`) values ("'.$commodity.'",UNHEX("'.$hexAddress.'"),'.$entityID.','.$amount.')');
		
	}
	
}


?>