<?php

// Include everything we need:
include('../private/includes.php');

// Settings:
include('../private/settings.php');

// Grab the path:
$path=$_SERVER['REQUEST_URI'];

// Split at ? to remove the query string if there is one:
$path=explode('?',$path,2);

// Get the first part only:
$path=$path[0];

// If the path is not "blank"..
if($path!=''&&$path!='/'){
	
	// Start with a forward slash?
	if($path{0}=='/'){
		
		// Chop it off:
		$path=substr($path,1);
		
	}
	
	// Does it end with a slash?
	if($path{strlen($path)-1}=='/'){
		
		// Chop it off:
		$path=substr($path,0,-1);
		
	}
	
	// Must now be a dir/path which can only contain lowercase a-z or forward slash.
	$path=preg_replace("/[^a-z0-9\/]+/", "", $path);
	
}else{
	// No path:
	$path='';
}

// Does the page we're after exist?
if(!file_exists($basePath.$path.'/index.php')){
	
	// 404.
	header("HTTP/1.0 404 Not Found");
	exit();
	
}

// Assume JSON content-type:
header("Content-Type: application/json");

// CORS:
header("Access-Control-Allow-Origin: *");

// Include the page now:
include($basePath.$path.'/index.php');

if($cachingDisabled){
	exit();
}

// Next, write page content to a file.
// cache($content);
?>