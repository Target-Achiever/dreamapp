<?php
require_once 'includes/class.query.php';
$response = array();
$getfav_events = array();
$data = (array)json_decode(file_get_contents("php://input"));
$followers = new Query;
$followers->access_table ="dream_user_following";
$user_id = $data["user_id"];
$following_id = $data["following_id"];

$result = array();
$params = array("conditions"=>array("user_id="=>$user_id,"following_id="=>$following_id));
$followers_exist = $followers->find_row_count($params,'followID');

if($followers_exist > 0) 
{
$delete = $followers->delete(array("user_id"=>$user_id,"following_id"=>$following_id));

$response = array("status"=>"true","message"=>"Successfully Unfollowed");
}
else
{
	#Get user device_token and Notification
	$dream_users = new Query;
	$dream_users->access_table ="dream_users";
	
	$select_notify = array("user_name");
	$where_notify = array("conditions"=>array("id="=>$user_id));
	$getUser = $dream_users->find_row($select_notify,$where_notify);
	
	$select_notify = array("device_token,notification_status");
	$where_notify = array("conditions"=>array("id="=>$following_id));
	$getDeviceToken = $dream_users->find_row($select_notify,$where_notify);
	
	$user_name = $getUser['user_name'];
	$notification_status = $getDeviceToken['notification_status'];
	$deviceToken = $getDeviceToken['device_token'];
	$message = "$user_name started following you!"; 

	// insert data
	$add_following = $followers->add_row($data);

	if($notification_status == 1 && $deviceToken !='') {

		$notify_result = $dream_users->sendPushNotificationIOS($deviceToken,$message,$user_id,'follow');	
	}
	else {
		$notify_result = FALSE;
	}
	
	if($notify_result)
		$notify_status = 'true';
	else
		$notify_status = 'false';
	#End of Get user device_token and Notification	
	
	$response = array("status"=>"true","type"=>"follow","notify"=>$notify_status,"user_id"=>$user_id,"message"=>"Successfully Followed");	

}	
echo json_encode($response);