<?

/*
* This file provides functionality for handling a MySQL database.
*/

/*
* Escapes database values to prevent SQL injection.
*/
function escape($str,$nohtml=true){
	
	if($nohtml){
		$str=htmlentities($str);
	}
	
	return str_replace(
		array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),
		array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'),$str
	);
	
}

/*
* An instance of a link to a MySQL database.
*/
class databaseLink{
	
	/* The location the database is at (typically localhost). */
	var $path;
	/* The username we're connecting with. */
	var $user;
	/* The MySQLi link itself. */
	var $link;
	/* The database being used. */
	var $schema;
	/* The password to connect with. */
	var $password;
	
	/* 
	* Creates a new link but does not connect it yet.
	*/
	function __construct($user,$password,$schema,$path='localhost'){
		$this->path=$path;
		$this->user=$user;
		$this->schema=$schema;
		$this->password=$password;
	}
	
	/*
	* Connects to the database. Note that this is called internally by the first query.
	*/
	function connect(){
		
		// Already connected?
		if($this->link){
			// Yes - just do nothing.
			return;
		}
		
		// Create the link:
		$this->link=new mysqli($this->path,$this->user,$this->password,$this->schema);
		
		// Set the charset to UTF8:
		$this->link->set_charset('utf8');
		
		// Clear the password as it's no longer necessary:
		$this->password='';
		
	}
	
	/*
	* Runs a query on the database
	*/
	function query($query){
		
		// Try connecting if needed:
		$this->connect();
		
		// Run the query now:
		$result = @$this->link->query($query);
		
		if($result){
			// Got a result:
			return $result;
		}
		
		// Error occured - throw an exception:
		throw new Exception('MySQL '.$this->link->errno.': "'.$this->link->error.'".');
		
	}

	/*
	* Gets a single row of data from the database.
	*/
	function get_row($query){
		
		// Run the query:
		$result=$this->query($query);
		
		// Did we get anything?
		if($result===false){
			
			// No - respond with null:
			return null;
			
		}
		
		// Get the complete row:
		$returned=$result->fetch_assoc();
		
		// Tidy up:
		$result->close();
		
		// Return the complete row:
		return $returned;
		
	}
	
	/*
	* Gets a list of data from the database.
	*/
	function get_list($query){
		
		// Run the query:
		$result = $this->query($query);
		
		if($result===false){
			// No results (not an error).
			return array();
		}
		
		// Start building the results array:
		$returned=array();
		
		// For each one..
		while($row = $result->fetch_assoc()){
			
			// Add it to the results:
			$returned[]=$row;
			
		}
		
		// Close the result reader:
		$result->close();
		
		// Return the result:
		return $returned;
		
	}
	
	/*
	* Gets the latest auto increment ID.
	*/
	function insert_id(){
		return $this->link->insert_id;
	}
	
}

?>
