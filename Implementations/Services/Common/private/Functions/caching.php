<?php

/*
* Non-essential. Some responses (read-only ones such as /list)
* are always the same, so they can be cached as gzipped files for speed.
* The functionality here performs the caching process.
*/

/*
* Caches everything that was echoed into a file at 'this' path.
*/
function cacheResponse($file='index.json'){
	
	// Grab the page contents:
	$content=ob_get_contents();

	// Clear the buffer:
	ob_end_clean();
	
	// Write response for this user:
	echo $content;
	
	cache($content,$file);
	
	// Quit:
	exit();
	
}

/*
* Caches the given content text into a file at 'this' path.
*/
function cache($content,$file){
	
	global $path;
	
	if($path!=''){
		
		// Add a slash in there:
		$path=$path.'/';
		
	}

	// Create the directory now:
	if(!file_exists($path)){
		
		// Create it now:
		mkdir($path,0777,true);
		
	}

	// And write there:
	file_put_contents($path.$file,$content);
	
	// Next, we'll even precompress it to save the server from compressing it hastily.
	$precompressed=gzencode($content,5);

	// Write out the compressed form:
	file_put_contents($path.$file.'.gz',$precompressed);

}

?>