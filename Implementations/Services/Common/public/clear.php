<?php

/*
* ----------------------------------------
*
*           Website by Kulestar
*
*  This source is provided "as is" with 
*  no warranty. Note that this PHP operates 
*  ahead-of-time for top performance. For 
*  information on how it works, see the 
*       FullDocumentation folder.
*
* ----------------------------------------
*/

/*
* This file clears the cache, if it's active.
* The cache is a rapid HTML cache which prevents 
* PHP getting involved at all in virtually every request.
*/

/**
* This function deletes the named page from the cache, e.g. 'home'.
*/
function deletePage($page){	
	
	// Get the full file:
	$file=$page.'/index.html';
	
	// Does it exist?
	if(file_exists($file)){
		
		// Yep! Delete it:
		unlink($file);
		
		echo 'Cleared '.$page.' from the cache.<br>';
		
	}
	
	if(file_exists($file.'.gz')){
		
		// Delete the precompressed version too:
		unlink($file.'.gz');
		
	}
	
}

// Clear a particular page from the cache.
$specificPage='';

// Got a page?
if(isset($_GET['page'])){
	
	// Yep! Clear it.
	$specificPage=$_GET['page'];
	
	// Safety check next - is this actually a page? We don't want a hacker deleting something they shouldn't!
	
	if(strstr($specificPage,'.')  || $specificPage=='/'){
	
		// Fail it:
		$specificPage='';
		
	}else if(!file_exists('FullSource/Pages/'.$specificPage.'/index.php')){
		
		// Fail it:
		$specificPage='';
		
	}else if($specificPage!=''){
		
		// Start with a forward slash?
		if($specificPage{0}=='/'){
			
			// Chop it off:
			$specificPage=substr($specificPage,1);
			
		}
		
		// Does it end with a slash?
		if($specificPage{strlen($specificPage)-1}=='/'){
			
			// Chop it off:
			$specificPage=substr($specificPage,0,-1);
			
		}
		
	}
	
}

// Delete index.html for homepage:
if($specificPage=='home' || $specificPage==''){
	
	// Delete index.html:
	if(file_exists('index.html')){
		unlink('index.html');
	}
	
}

if($specificPage!=''){
	// E.g. 'home'. We know it's a page too.
	
	deletePage($specificPage);
	
}

// For each page, check if it has a directory.
// We can find all the pages at..
$pagesPath='FullSource/Pages/';

// Grab the pages now:
$pages=scandir($pagesPath);

// For each one..
foreach($pages as $page){
	
	if($page=='.' || $page=='..' || strstr($page,'/') || strstr($page,'\\')){
		// Skip all these - not valid.
		continue;
	}
	
	// Delete the page now:
	deletePage($page);
	
}

echo 'OK';
?>