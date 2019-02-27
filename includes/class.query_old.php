<?php
require_once 'class.db.php';

class Query extends Database
{	
	/**
	* General insert function
	*/	
	public function add($table_name, $data)
	{		
		$sql = "INSERT INTO $table_name (";
		$sql_values='';	
		
		foreach($data as $key => $values)
		{
			$sql = $sql . $key . ', ';	
			$sql_values = $sql_values . "'" . $values . "', "; 
		}
		$sql = trim($sql, ", "). ") VALUES (" . trim($sql_values, ", ") . ")";		
		
		$sql_result = mysqli_query($this->con, $sql);

		if($sql_result)
		{
			return array("status"=>1, "insert_id"=>mysqli_insert_id($this->con));  
		}
		else
		{
			return array("status"=>0);  
		}
	}

	/**
	*General insert function with MD5 for password encrypt. $md5_key->Means which field is convert to MD5.
	*/
	public function addMD5($table_name, $data, $md5_key)
	{			
		$sql = "INSERT INTO $table_name (";
		$sql_values='';	
		
		foreach($data as $key => $values)
		{
			$sql = $sql . $key . ', ';	
			if($md5_key != $key)
				$sql_values = $sql_values . "'" . $values . "', "; 
			else
				$sql_values = $sql_values . "MD5(
			" . $values . "), "; 
		}
		$sql = trim($sql, ", "). ") VALUES (" . trim($sql_values, ", ") . ")";		
		
		$sql_result = mysqli_query($this->con, $sql);

		if($sql_result)
		{
			return array("status"=>1, "insert_id"=>mysqli_insert_id($this->con));
		}
		else
		{
			return array("status"=>0);
		}
	}

	/**
	*Generaly execute the all type of query
	*/
	public function execute($sql)
	{		
		$sql_result = mysqli_query($this->con, $sql);
		
		if($sql_result)
		{
			return array("status"=>1, "affected_rows"=>mysqli_affected_rows($this->con));
		}
		else
		{
			return array("status"=>0);
		}	
	}

	/**
	*Get Selected data based on query
	*/
	public function getData($sql)
	{					
		$sql_result = mysqli_query($this->con, $sql);
		while($row[] = mysqli_fetch_assoc($sql_result))
		{	
		}	
		return array_filter($row);	
	}
}//End of query class