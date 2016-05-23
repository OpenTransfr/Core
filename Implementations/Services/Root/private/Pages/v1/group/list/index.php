<?php

// Group listing API.

$groups=$dz->get_list('select `ID`,`Code`,`Name_en`,`Planet` from `Root.Groups`');

// Show the list now!
showList($groups,array('ID','Code','Name_en','Planet'));

?>