<?php// Find a bank with a given name.if(!isset($_GET['name'])){		// Name is required.	error('field/required','name');	}// Get the name and strip any wierd characters that the user might have accidentally pressed:$name=preg_replace(VALID_TITLE,'',strtolower(trim($_GET['name'])));if($name==''){	// No name - no results.	echo '[]';	exit();}// Perform a search now:// where Match(`Name`) against ("'.escape($name).'") is unfortunately not very good!$results=$dz->get_list('select `Endpoint`,`Name` from `Root.Entities` where `Type`=4 and `Name` like "%'.escape($name).'%" limit 0,40');// Output a JSON list:echo json_encode($results);?>