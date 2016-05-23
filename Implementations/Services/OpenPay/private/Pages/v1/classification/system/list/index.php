<?php

// Number system listing API.

// Get the systems:
$systems=$dz->get_list('select `Merchant.ProductClassSystems`.`Code`,`Merchant.ProductClassSystems`.`Endpoint`,`Merchant.ProductClassSystems`.`Name`,`Root.Entities`.`Endpoint` as `RegisteredBy` from `Merchant.ProductClassSystems` left join `Root.Entities` on `Merchant.ProductClassSystems`.`RegisteredBy`=`Root.Entities`.`ID`');

// Show the list now!
showList($systems,array('Code','Endpoint','Name','RegisteredBy'));

?>