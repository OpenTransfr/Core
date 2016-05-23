<?php

// Classification listing API.

// Get the classifications:
$classifications=$dz->get_list('select * from `Merchant.OPC`');

// Show the list now!
showList($classifications,array('OPC','Name_en','RawData','Parent'));

?>