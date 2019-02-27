<?php
		require_once 'includes/class.query.php';

		$response = array();

		$data = (array)json_decode(file_get_contents("php://input"));

		$user 				= 	new Query;
		$user->access_table = 	"dream_users";
	
		$user_id 			= 	$data["user_id"];
		$user_password		= 	isset($data["user_password"]) ? $data["user_password"]:'';
		$newpassword		= 	isset($data["newpassword"]) ? $data["newpassword"]:'';
		//$confirmpassword	= 	isset($data["confirmpassword"]) ? $data["confirmpassword"]:'';
		$encpassword 	    = 	base64_encode($user_password);
		$encnewpassword 	= 	base64_encode($newpassword);
		/* $params = array("conditions"=>array("user_id="=>$user_id,"user_password="=>$encpassword));
		$sql_result = $settings->findCount($params); */

		if(!empty($user_password)) {

			$select 		= 	array("*");
			$where 			= 	array("conditions"=>array("id="=>$user_id));
			$users 			= 	$user->find_row($select, $where);
			if(!empty($users)) {
				
				
				if($users['user_password']==$encpassword){
			
						$result['user_password'] 	=  	$encnewpassword;
						$where 						= 	array("id"=>$user_id);
						$user_result 				= 	$user->update($result ,$where);
						if($user_result['status']==1)
						{
							$response = array("status"=>"true","message"=>"Password updated successfully!");		
						}
					
				}else{
					$response = array("status"=>"False","message"=>"Please enter correct old password");	
				}
			}
		}
		else {

			$result['user_password'] 	=  	$encnewpassword;
			$where 						= 	array("id"=>$user_id);
			$user_result 				= 	$user->update($result ,$where);
			if($user_result['status']==1)
			{
				$response = array("status"=>"true","message"=>"Password updated successfully!");		
			}
		}

		//$users['user_password'];
		//if($users['user_password'])
		echo json_encode($response);
?>