<?php
require_once 'class.db.php';
require_once 'constants.php';
class Query extends Database
{
public $access_table; 
	
	public function add_row($data)
	{		
		$sql = "INSERT INTO ".$this->access_table."
					(".implode(',', array_keys($data)).")
                    VALUES ("."'".implode("','", $data)."')";
					

		$sql = trim($sql,",");
		
		//echo "<br>Add sql== " . $sql;
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

	public function add_row_like($data)
	{		

		$insert_values = '"'.implode('","', $data).'"';
		$sql = "INSERT INTO ".$this->access_table."
					(".implode(',', array_keys($data)).")
                    VALUES ($insert_values)";
					

		$sql = trim($sql,",");
		
		//echo "<br>Add sql== " . $sql;
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
	

public function add_row_picture($tablename, $data, $profile_image)
{		
		$sql = "INSERT INTO $tablename (";
		$sql_values='';	
		
		foreach($data as $key => $values)
		{
			$sql = $sql . $key . ', ';	
			if($profile_image != $key)
				$sql_values = $sql_values . "'" . $values . "', "; 
			else
				
			
			
				$sql_values = $sql_values . "'" . $imageName . "', "; 
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
    
	public function update($tablename, $data, $where = array(1 => 1)) {
		
		foreach ($data as $k => $v ) $update_data[] = "$k = '".$v."'";	
		foreach ($where as $k => $v ) $where_condition[] = "$k = '".$v."'";
		
		$sql = "UPDATE ".$tablename." SET ".implode(', ', $update_data)." WHERE ".implode(' AND ', $where_condition);
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
	}*/
	
		/**
    * Method used to UPDATE records in the database
    */
	public function update($data, $where = array(1 => 1)) {
		
		foreach ($data as $k => $v ) $update_data[] = "$k = '".$v."'";	
		foreach ($where as $k => $v ) $where_condition[] = "$k = '".$v."'";
		
		$sql = "UPDATE ".$this->access_table." SET ".implode(', ', $update_data)." WHERE ".implode(' AND ', $where_condition);
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


 	public function find_count($user_email)
	{
	
	$query = "SELECT * FROM dream_users WHERE user_email = '$user_email'";
    $result = mysqli_query($this->con, $query);
		if (mysqli_num_rows($result) > 0) 
		{
			return array("status"=>1);  
		}
		else
		{
			return array("status"=>0);  
		}
		
	}
	
	
	/*
	* Method used for COUNT() query operations
	*/
	public function find_row_count($params = array('conditions' => array()),$id) {

		$where_condition = array();
		$params['conditions'] = (count($params['conditions']) > 0) ? $params['conditions'] : array('1 = ' => '1');
		foreach ($params['conditions'] as $k => $v ) $where_condition[] = "$k '".$v."'";
		
		// echo $sql = "SELECT COUNT(".$this->access_table."_id) AS count FROM "
											// .$this->access_table."
											// WHERE
											// ".implode(" AND ", $where_condition);
											
		$sql = "SELECT COUNT($id) AS count FROM "
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
	* Method used for Customize SELECT query.
	*/
	public function execute($sql)
	{
		
	
		$sql_result = mysqli_query($this->con, $sql);
		while($row = mysqli_fetch_assoc($sql_result))
		{	
			$row_data= $row;
		}		
		if(!empty($row_data))
			{

			return $row_data;

			}
			else{


			}		
	}
	
	
		public function execute_array($sql)
	{
			$sql_result = mysqli_query($this->con, $sql);
			while($row = mysqli_fetch_assoc($sql_result))
			{	
			$row_data[]= $row;
			}		

			if(!empty($row_data))
			{

			return $row_data;

			}
			else{


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
	
		
	public function update_execute($sql)
	
	{
		
	$sql_result = mysqli_query($this->con, $sql);
		/* while($row = mysqli_fetch_assoc($sql_result))
		{	
			$row_data= $row;
		} */		
		return $sql_result;		
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
    public function find_rows($tablename, $select = array('*'), $params = array('conditions' => array(), 'sort' => '', 'limit' => '', 'group' => '')) {
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
					'.$tablename.'
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
	
	/** 
	* push notification - IOS
	**/
    public function sendPushNotificationIOS($deviceToken,$message,$id,$type) //id-Post or User ID
    {		
		
		$passphrase = '';
		$cert = __DIR__.'/pushcert.pem';
		
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $cert);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		// Open a connection to the APNS server
		// Dev
		$fp = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		// Production
		/* $fp = stream_socket_client(
			'ssl://gateway.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx); */

		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		//echo 'Connected to APNS' . PHP_EOL;

		// Create the payload body
		$body['aps'] = array(
			'type'	=> $type,
			'id'=> $id,
			'alert' => $message,
			'sound' => 'default'
			);

		// Encode the payload as JSON
		$payload = json_encode($body);

		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));

		 if (!$result)
			 $JSONsms = array("status"=>"0","Message"=>'Message not delivered');
		 else
			 $JSONsms = array("status"=>"0","Message"=>'Message successfully delivered');
			//print_r($JSONsms);
		// Close the connection to the server
		fclose($fp);
		
		return $result;
    }	
}

?>