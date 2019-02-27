<?php
require_once 'includes/class.query.php';

$register = new Query;
$register->access_table = "dream_users";

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));

$user_email = $data["user_email"];
$user_password = base64_encode($data["user_password"]);
//$md5_field_name = "user_password";
		

$params = array("conditions"=>array("user_email="=>$user_email));
$email_exist = $register->find_count($user_email);


if(($user_email!='') && ($user_password!=''))
{
	if($email_exist["status"] == 1)
	{
		$response = array("status"=>"false", "status_code"=>"400", "message"=>"Email Already Exist!");
	}
	else
	{				
								
		//$register_result = $register->addMD5($data, $md5_field_name);
		

			//-------------------------mail token byN
			$random = mt_rand(000,999);
			$time = time();
			$mail_token = md5("C-$random-$time");

			//-------------------------

			$data = array_merge($data, array("user_password"=>$user_password,"mail_token"=>$mail_token,"mail_token_send_date" => date('Y-m-d H:i:s'),"user_status" => 2,"user_registration_type"=>1));
			
		
			$register_result = $register->add_row($data);

		if($register_result['status'])
		{								

			//---------------------send emmail verification mail-byN
			$mail_sub = "Verify your email address.";
			$variables['mail_token'] = $mail_token;

			$mail_msg = file_get_contents("confirmation_template.php");
			
			foreach($variables as $key => $value)
			{
			    $mail_msg = str_replace('{{'.$key.'}}', $value, $mail_msg);
			}

			$mail_status = $register->send_to_mail($user_email,$mail_sub,$mail_msg);

			//-------------------------------------------------------
			$response = array("status"=>"true", "status_code"=>"200", "message"=>"Registered Successfully","UserId"=>$register_result['insert_id']);
				
		}
		else
		{
			$response = array("status"=>"false", "status_code"=>"404", "message"=>"Register Failed!");
		}
	}
}
else
{
	$response = array("status"=>"false", "status_code"=>"400", "message"=>"Email And Password Cannot Be Empty");
}

echo json_encode($response);