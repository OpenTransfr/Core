<?php

// Commodity listing API.

$coms=$dz->get_list('select `Tag`,`Description_en`,`Name_en`,`Divisor`,`Policy`,`Root.Entities`.`Endpoint` as `Issuer` from `Root.Commodities` left join `Root.Entities` on `Root.Entities`.`ID` = `Root.Commodities`.`Issuer`');

// Show the list now!
showList($coms,array('Tag','Description_en','Name_en','Divisor','Issuer','Policy'));

?>