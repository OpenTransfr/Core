<?php

// Include the index functionality:
include('../private/Functions/index.php');

// The index consists of functions and types. Build a set to collect them into.
$set=array(
	'functions'=>array(),
	'types'=>array()
);

// The sub-directory we're on is $path. Find the functions for this path and put them into $set:
buildApiIndex($path,$set);

echo '{';

// Output the functions:
outputIndex('functions',$set['functions']);

echo ',';

// Output the types:
outputIndex('types',$set['types']);

echo '}';

// These results should be cached - the response is always the same so:
// cache();

?>