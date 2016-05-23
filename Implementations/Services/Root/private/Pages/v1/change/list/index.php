<?php

// Changes listing API. Includes all transactions and issues.

$entities=$dz->get_list('select * from `Root.Changes`');

// Show the list now!
showList($entities,array('RequestID','Signature','Time','Type','Content'));

?>