<?php

/*
* Functions in this file help with outputting responses to the user.
*/

/*
* Responds with an error message. All handling halts when this is called.
* Usage is e.g. error('field/missing','public_key'); (referring to a required JSON
* field called 'public_key' not being given by the user).
*/
function error($name){
	
	// Get all the args given to this function:
	$arg_list=func_get_args();
	
	// Get the number of args:
	$numargs=count($arg_list);
	
	// Client error header:
	header('HTTP/1.0 400 Bad Request');
	
	// Start outputting the JSON:
	echo '{"type":"error/'.$name.'","args":[';
	
	// For each arg (if there are any), starting from index 1 as we don't want to include the name itself.
    for($i=1;$i<$numargs;$i++){
        
		if($i!=1){
			// Make sure the args are separated by commas:
			echo ',';
		}
		
		// Echo the arg, ensuring that it's escaped (i.e. " will be correctly escaped)
		echo '"'.escape( $arg_list[$i] ).'"';
		
    }
	
	// Finish the args and the JSON message:
	echo ']}';
	
	// Quit:
	exit();
	
}

/*
* Used when the server has failed.
*/
function serverError(){
	header("HTTP/1.0 500 Server Error");
	exit();
}

/*
* Logs an exception. This implementation outputs it to the user.
*/
function logException($str,$backtrace=true){
	
	// Generic error handler
	$result='<div style="padding:4px;border:2px solid #cc3300;color:#ffffff;background:#FF9797;">'.$str;
	
	if($backtrace){
		
		$backtrace=debug_backtrace();
		$result.='<br><br><b>Stack trace:</b><br>';
		
		foreach($backtrace as $i=>$trace){
			
			if($i==0){
				continue;
			}
			
			$result.=$trace['function'].'(';
			
			foreach($trace['args'] as $x=>$arg){
				if($x!=0){
					$result.=',';
				}
				
				if(is_string($arg)){
					$result.=$arg;
				}else{
					$result.=gettype($arg);
				}
			}
			
			$line=isset($trace['line'])?$trace['line']:'unknown';
			$result.=') on line '.$line.' in '.basename($trace['file']).'<br>';
		}
		
	}
	
	$result.='</div>';
	
	echo $result;
}

/*
* Used when an exception has been thrown.
*/
function exceptionHandler($errno, $errstr, $errfile, $errline) {
	
	// Log the exception:
	logException('<b>Error '.$errno.' on line '.$errline.' in '.basename($errfile).'</b>: '.$errstr);
	
	// Quit:
	return true;
	
}

/*
* Outputs a JSON list using the given fields of the given set.
*/
function showList($set,$fields,$rowFunction=NULL){
	
	// First the fields header:
	$data='{"fields":{';
	
	// How many fields?
	$fieldCount=count($fields);
	
	for($i=0;$i<$fieldCount;$i++){
		
		if($i!=0){
			$data.=',';
		}
		
		$data.='"'.$fields[$i].'":'.$i;
		
	}
	
	// Secondary fields header (indexed):
	$data.='},"fieldNames":[';
	
	for($i=0;$i<$fieldCount;$i++){
		
		if($i!=0){
			$data.=',';
		}
		
		$data.='"'.$fields[$i].'"';
		
	}
	
	// Rows header:
	$data.='],"rows":[[';
	
	// Size tracks the current size of the output.
	// This is so we can generate some convenient checkpoints for when
	// the output gets awkwardly large.
	$size=strlen($data);
	echo $data;
	$data='';
	
	// Start outputting the rows now!
	$checkpoints=array();
	$first=true;

	foreach($set as $entity){
		
		if($first){
			$first=false;
			
			// First checkpoint. Add a checkpoint and reset size.
			// The -1 is because size already includes the first open bracket (but we want it to point before that).
			// It can't be outputted in here because if we have no rows then the result is broken JSON.
			array_push($checkpoints,$size-1);
			$size=1;
			
		}else{
			
			$size++;
			
			if($size>1000000){
				// 1MB. Add a checkpoint and reset size.
				array_push($checkpoints,$size+1);
				
				$size=1;
				echo '],[';
				
			}else{
				
				echo ',';
			
			}
			
		}
		
		// Call the row function if there is one:
		if($rowFunction!=null){
			
			// Call it:
			$rowFunction($entity);
			
		}
		
		$row='[';
		
		for($i=0;$i<$fieldCount;$i++){
			
			if($i!=0){
				$row.=',';
			}
			
			$value=$entity[$fields[$i]];
			
			if(is_string($value)){
				$row.='"'.str_replace('"','\"',$value).'"';
			}else if(is_array($value)){
				// JSON encode it:
				$row.=json_encode($value);
			}else{
				$row.=$value;
			}
			
		}
		
		// Finish the row:
		$row.=']';
		
		// Increase size:
		$size+=strlen($row);
		
		echo $row;
		
	}

	echo ']],"checkpoints":'; // Length 17
	
	// We want to know where the checkpoints start at
	// for our 'cpat' value. It's the total of all checkpoints plus $size
	// Plus the size of the small "checkpoints" header echoed above (constant).
	// We'll add the total on once we know what it is - we need it anyway.
	$checkpointsAt=$size+17;
	
	// How many checkpoints?
	$checkCount=count($checkpoints);
	
	// Each checkpoint is stored relative to the previous one
	// So, in order to output something more useful, we track
	// the current total of checkpoint locations.
	$total=0;
	
	$data='[';
	
	// For each one..
	for($i=0;$i<$checkCount;$i++){
		
		// Output a comma:
		if($i!=0){
			$data.=',';
		}
		
		// Get the checkpoint:
		$point=$checkpoints[$i];
		
		// Add it to the total:
		$total+=$point;
		
		// Output it:
		$data.=$total;
		
	}
	
	// This is where we add the total on:
	$checkpointsAt+=$total;
	
	// Output the checkpoints:
	echo $data;
	
	// Output the locations:
	echo '],"locations":{"f":10,"cp":'.$checkpointsAt.'}}';

}

// Register our exception handler:
set_error_handler('exceptionHandler',E_ALL);

?>