<?php
require_once 'includes/class.query.php';

$user_table = new Query;
$user_table->access_table ="dream_users"; 

$response = array();
$data = json_decode(file_get_contents('php://input'));
$user_email = $data->{"user_email"};
$device_token = $data->{"device_token"};
$login_type = $data->{"login_type"};
$latitude = $data->{"user_latitude"};
$longitude = $data->{"user_longitude"};

$select = array("*");
$where = array("conditions"=>array("user_email="=>$user_email));
$user_data = $user_table->find_row($select, $where);

if(empty($user_email)) {
	$response = array("status"=>"false", "status_code"=>"400","message"=>"Email ID is empty");
	echo json_encode($response);
	exit;
}

if(!empty($user_data)) {

	$where = array("id"=>$user_data['id']);

	if($user_data['user_status']==2)
	{
		$update = array("user_status"=>1);
	}
	$update['device_token'] = $device_token;
	$update['user_latitude'] = $latitude;
	$update['user_longitude'] = $longitude;
	$devicetoken_update = $user_table->update($update ,$where);

	$result['user_name'] =  $user_data['user_name'];
	$result['user_city'] =  $user_data['user_city'];
	$result['user_dob'] =  $user_data['user_dob'];
	$result['profile_image'] =  $user_data['profile_image'];
	$result['user_gender'] =  $user_data['user_gender'];
	$result['user_address'] =  $user_data['user_address'];
	$result['profile_description'] =  $user_data['profile_description'];
	$result['notification_status'] =  $user_data['notification_status'];
	$result['login_type'] =  $user_data['user_registration_type'];
	$result['password_exist'] =  (!empty($user_data['user_password'])) ? true : false;
		
	$response = array("status"=>"true", "status_code"=>"200","user_id"=>$user_data['id'],"user_settings"=>$result, "message"=>"Login Successfully");
}
else
{

	$data = array("user_email"=>$user_email,"device_token"=>$device_token,"user_status" =>1,"user_registration_type"=>$login_type,'user_latitude'=>$latitude,'user_longitude'=>$longitude);			
	$register_result = $user_table->add_row($data);

	if($register_result['status'])
	{		
		$select = array("*");
		$where = array("conditions"=>array("id="=>$register_result['insert_id']));
		$user_data = $user_table->find_row($select, $where);

		$result['user_name'] =  $user_data['user_name'];
		$result['user_city'] =  $user_data['user_city'];
		$result['user_dob'] =  $user_data['user_dob'];
		$result['profile_image'] =  $user_data['profile_image'];
		$result['user_gender'] =  $user_data['user_gender'];
		$result['user_address'] =  $user_data['user_address'];
		$result['profile_description'] =  $user_data['profile_description'];
		$result['notification_status'] =  $user_data['notification_status'];
		$result['login_type'] =  $user_data['user_registration_type'];
		$result['password_exist'] =  (!empty($user_data['user_password'])) ? true : false;
			
		$response = array("status"=>"true", "status_code"=>"200","user_id"=>$user_data['id'],"user_settings"=>$result, "message"=>"Login Successfully");
	}
	else
	{
		$response = array("status"=>"false", "status_code"=>"404", "message"=>"Register Failed!");
	}
}

echo json_encode($response);

?>