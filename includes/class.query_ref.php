<?php
require_once 'class.db.php';

class Query extends Database
{		
	/**
	* Method used to INSERT records in the database
	*/		
	public $access_table; 
	
	public function add_row($data)
	{		
		$sql = "INSERT INTO ".$this->access_table."
					(".implode(',', array_keys($data)).")
                    VALUES ("."'".implode("','", $data)."')";
		$sql = trim($sql,",");
		
		// echo "<br>Add sql== " . $sql;
		// exit;		
		
		
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
	* Method used to Multi INSERT records in the database
	*/	
	public function add_rows($key, $data)
	{	
		$sql = "INSERT INTO
					".$this->access_table."
					(".implode(',', $key).") VALUES ";
					
					foreach($data as $data_array)
					{	$sql .=  "('".implode("','", $data_array)."'),";	}
					
		$sql = trim($sql,",");	
		
		//echo "<br>add_rows sql== " . $sql;		
		
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
	* Method used to INSERT records in the database with MD5 for password encrypt. $md5_key->Convert to MD5.
	*/
	public function addMD5($data, $md5_key)
	{		
		$sql = "INSERT INTO $this->access_table (";
		$sql_values='';	
		
		foreach($data as $key => $values)
		{
			$sql = $sql . $key . ', ';	
			if($md5_key != $key)
				$sql_values = $sql_values . "'" . $values . "', "; 
			else
				$sql_values = $sql_values . "MD5('" . $values . "'), "; 
		}
		$sql = trim($sql, ", "). ") VALUES (" . trim($sql_values, ", ") . ")";		
		
		 // echo "<br>sql== " . $sql;
		 // exit;
		
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
    * Method used to UPDATE records in the database
    */
	public function save($data, $where = array(1 => 1)) {
		
		foreach ($data as $k => $v ) $update_data[] = "$k = '".$v."'";	
		foreach ($where as $k => $v ) $where_condition[] = "$k = '".$v."'";
		
		$sql = "UPDATE ".$this->access_table." SET ".implode(', ', $update_data)." WHERE ".implode(' AND ', $where_condition);
		// echo "<br><br>sql== " . $sql; 
		// exit;
		
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
	* Method used to DELETE records from the database
	*/
    public function delete($where) { 
		$this->access_table;
		foreach ($where as $k => $v )  $where_condition[] = "$k IN ('".$v."')";
		$sql = "DELETE FROM ".$this->access_table." WHERE ".implode(' AND ', $where_condition);	
		// echo "<br><br>sql== " . $sql; 
		// exit;
		
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
	
	/*
	* method used for SELECT query operations
	*/
    public function find_row($select = array('*'), $params = array('conditions' => array(), 'sort' => '', 'limit' => '', 'group' => '')) {
		$row_data = array();
        $where_condition = array();
        $sort_order = '';
        $limit = '';
        $group_by = '';
        $params['conditions'] = (count($params['conditions']) > 0) ? $params['conditions'] : array('1 = ' => '1');

        foreach ($params['conditions'] as $k => $v ) $where_condition[] = "$k '".$v."'";

        if(!empty($params['sort'])) {
            $sort_order .= 'ORDER BY ' . $params['sort'];
        }

        if(!empty($params['limit'])) {
            $limit .= 'LIMIT ' . $params['limit'];
        }

        if(!empty($params['group'])) {
            $group_by .= 'GROUP BY ' . $params['group'];
        }

        /* echo 'SELECT '.implode(', ', $select).' FROM '.$this->access_table.' WHERE '.implode(' AND ', $where_condition).' '.$sort_order .' '.$limit .' '.$group_by; */
				
        $sql = 'SELECT '
					.implode(', ', $select).'
					FROM
					'.$this->access_table.'
					WHERE
					'.implode(' AND ', $where_condition)
					.' '.$sort_order
					.' '.$limit
					.' '.$group_by;
		
		// echo "find_row== " . $sql;
		// exit;
		
		$sql_result = mysqli_query($this->con, $sql);
		
		if($sql_result->num_rows)	
		{			
			while($row = mysqli_fetch_assoc($sql_result))
			{	
				$row_data = $row;
			}
		}			
		return $row_data;			
    }
	
	/*
	* method used for SELECT query operations
	*/
    public function find_rows($select = array('*'), $params = array('conditions' => array(), 'sort' => '', 'limit' => '', 'group' => '')) {
		$row_data = array();
        $where_condition = array();
        $sort_order = '';
        $limit = '';
        $group_by = '';
        $params['conditions'] = (count($params['conditions']) > 0) ? $params['conditions'] : array('1 = ' => '1');

        foreach ($params['conditions'] as $k => $v ) $where_condition[] = "$k '".$v."'";

        if(!empty($params['sort'])) {
            $sort_order .= 'ORDER BY ' . $params['sort'];
        }

        if(!empty($params['limit'])) {
            $limit .= 'LIMIT ' . $params['limit'];
        }

        if(!empty($params['group'])) {
            $group_by .= 'GROUP BY ' . $params['group'];
        }

        /* echo 'SELECT '.implode(', ', $select).' FROM '.$this->access_table.' WHERE '.implode(' AND ', $where_condition).' '.$sort_order .' '.$limit .' '.$group_by; */
				
        $sql = 'SELECT '
					.implode(', ', $select).'
					FROM
					'.$this->access_table.'
					WHERE
					'.implode(' AND ', $where_condition)
					.' '.$sort_order
					.' '.$limit
					.' '.$group_by;
						
		$sql_result = mysqli_query($this->con, $sql);
		if($sql_result->num_rows)
		{
			while($row = mysqli_fetch_assoc($sql_result))
			{	
				$row_data[] = $row;
			}	
		}		
		return $row_data;			
    }
	
	/*
	* Method used for COUNT() query operations
	*/
	public function find_count($params = array('conditions' => array())) {

		$where_condition = array();
		$params['conditions'] = (count($params['conditions']) > 0) ? $params['conditions'] : array('1 = ' => '1');
		foreach ($params['conditions'] as $k => $v ) $where_condition[] = "$k '".$v."'";
		
		// echo $sql = "SELECT COUNT(".$this->access_table."_id) AS count FROM "
											// .$this->access_table."
											// WHERE
											// ".implode(" AND ", $where_condition);
											
		$sql = "SELECT COUNT(".$this->access_table."_id) AS count FROM "
											.$this->access_table."
											WHERE
											".implode(" AND ", $where_condition);
		// echo "find_count== " . $sql;
		// exit;
		
		
		$sql_result = mysqli_query($this->con, $sql);
		while($row = mysqli_fetch_assoc($sql_result))
		{	
			$row_data[] = $row;
		}	
		return $row_data[0]['count'];	
	}	
	
				/**
				* ######################## Other Functions ###################### 
				*/
	
	/**
	* Method used for PHP Mail_function()
	*/	
	public function send_to_mail($to,$subject,$message)
	{
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <noreply@example.com>' . "\r\n";

		if(mail($to,$subject,$message,$headers))
		{
			return array("status"=>1);
		}
		else
		{
			return array("status"=>0);
		}
	}
	
	/**
	* Method used for Generate key
	*/
	public function generate_key($key_len)
	{
		$string = "123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtWwXxYyZz";
		$string_shuffle = str_shuffle($string);
		$session_key = str_shuffle(substr($string_shuffle, 3, $key_len));
		return $session_key;
	}
		
	/**
	* Method used for Customize SELECT query.
	*/
	public function execute($sql)
	{
		$sql_result = mysqli_query($this->con, $sql);
		while($row = mysqli_fetch_assoc($sql_result))
		{	
			$row_data[] = $row;
		}		
		return $row_data;	
	}
	
}//End of Query class