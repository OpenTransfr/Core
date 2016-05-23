<?php

// Entity listing API.

$entities=$dz->get_list('select `Key`,`Type`,`Endpoint`,`Group`,`Name`,`Country` from `Root.Entities`');

// Show the list now!
showList($entities,array('Key','Type','Endpoint','Group','Name','Country'),function(&$row){
	
	// Base 64 encode the key:
	$row['Key']=base64_encode($row['Key']);
	
});

?>