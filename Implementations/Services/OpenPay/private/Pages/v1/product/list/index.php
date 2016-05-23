<?php

// Product listing API.

// Get the products:
$products=$dz->get_list('select * from `Merchant.OPN`');

// Show the list now!
showList($products,array('OPN','Name_en','RawData','Category'));

?>