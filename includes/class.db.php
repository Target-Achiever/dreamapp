<?php
/*
* Mysql database class - only one connection alowed
*/
class Database {
	public $con;	
		
	public function __construct() {
		if($_SERVER['SERVER_NAME'] == 'localhost'){
			$dbhost = 'localhost';
			$dbuser = 'root';
			$dbpass = '';
			$dbname = 'pzy_dreamdb';
			$this->con = mysqli_connect ($dbhost, $dbuser, $dbpass, $dbname) or die ('I cannot connect to the database because: ' . mysqli_error());					
		}
		else{
			$dbhost = 'localhost';
			$dbuser = 'client_dreamApp';
			$dbpass = 'client_dreamApp';
			$dbname = 'client_dreamApp';
			$this->con = mysqli_connect ($dbhost, $dbuser, $dbpass, $dbname) or die ('I cannot connect to the database because: ' . mysqli_error());  
		}	
	}
}
?>