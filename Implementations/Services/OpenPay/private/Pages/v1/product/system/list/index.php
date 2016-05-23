<?php

// Number system listing API.

// Get the systems:
$systems=$dz->get_list('select `Merchant.ProductNumberSystems`.`Code`,`Merchant.ProductNumberSystems`.`Endpoint`,`Merchant.ProductNumberSystems`.`Name`,`Root.Entities`.`Endpoint` as `RegisteredBy` from `Merchant.ProductNumberSystems` left join `Root.Entities` on `Merchant.ProductNumberSystems`.`RegisteredBy`=`Root.Entities`.`ID`');

// Show the list now!
showList($systems,array('Code','Endpoint','Name','RegisteredBy'));

?>