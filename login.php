<?php
require_once 'includes/class.query.php';
$login = new Query;
$login->access_table ="dream_users"; 

$subscription = new Query;
$subscription->access_table ="dream_subscription"; 

$response = array();
$data = json_decode(file_get_contents('php://input'));
$user_email = $data->{"user_email"};
$device_token = $data->{"device_token"};
$user_password = base64_encode($data->{"user_password"});
$latitude = $data->{"user_latitude"};
$longitude = $data->{"user_longitude"};

$select = array("*");
$where = array("conditions"=>array("user_email="=>$user_email, "user_password="=>$user_password));
$login_status = $login->find_row($select, $where);
// print_r($login_status);die;
if(count($login_status))
{
	if($login_status['user_status']==1)
	{
		$user_id = $login_status['id'];
		$query = "SELECT * FROM dream_users WHERE id = '$user_id'";
		$getCustomer_data = $login->execute($query);
		
		#Update device_token
		$update = array("device_token"=>$device_token,'user_latitude'=>$latitude,'user_longitude'=>$longitude);
		$where = array("id"=>$user_id);
		$deviceTokenUpdate = $login->update($update ,$where);
		#End of Update device_token

		// Check whether the user is subscribed or not
		$select = array("*");
		$where = array("conditions"=>array("user_id="=>$user_id, "subscription_status="=>1));
		$subscription_result = $subscription->find_row($select, $where);
		$result['subscription'] = (!empty($subscription_result)) ? true : false;
		
		if(!empty($getCustomer_data))
		{
			$result['user_name'] =  $getCustomer_data['user_name'];
			$result['user_city'] =  $getCustomer_data['user_city'];
			$result['user_dob'] =  $getCustomer_data['user_dob'];
			$result['profile_image'] =  $getCustomer_data['profile_image'];
			$result['user_gender'] =  $getCustomer_data['user_gender'];
			$result['user_address'] =  $getCustomer_data['user_address'];
			$result['profile_description'] =  $getCustomer_data['profile_description'];
			$result['notification_status'] =  $getCustomer_data['notification_status'];
			$result['login_type'] =  $getCustomer_data['user_registration_type'];
			$result['password_exist'] =  (!empty($getCustomer_data['user_password'])) ? true : false;
		}
		
	$response = array("status"=>"true", "status_code"=>"200","user_id"=>$login_status['id'],"user_settings"=>$result, "message"=>"Login Successfully");
	}
	else
	{
		$response = array("status"=>"false", "status_code"=>"400","message"=>"Your Account Activation Is Pending!");
	}
	/* else if(!$login_status['status'] && count($login_status))
	{
		$response = array("status"=>"false", "status_code"=>"400", "user_id"=>$login_status['user_id'], "message"=>"Your Account Activation Is Pending!");
	} */
}
else
{
	$response = array("status"=>"false", "status_code"=>"400", "message"=>"Invalid Username Or Password!");
}

echo json_encode($response);

?>