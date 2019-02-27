<?php 
require_once 'includes/class.query.php';

$comment = new Query;
$comment->access_table = "dream_comment";

$dream_posts = new Query;
$dream_posts->access_table = "dream_posts";

$data = json_decode(file_get_contents("php://input"),true);
$response = array();

if( !empty($data['user_id']) && !empty($data['post_id']) && !empty($data['comment']) )
{		
	#get user_id
	$select_post = array("user_id,check_anonymous");
	$where_post = array("conditions"=>array("postId="=>$data['post_id']));
	$getPost = $dream_posts->find_row($select_post,$where_post);
	$user_id = $getPost['user_id']; // SELECT user_id FROM `dream_posts` WHERE postId='19'
	#End of user_id	
	$is_anonymous = $getPost['check_anonymous'];

	#Get user device_token and Notification
	$dream_users = new Query;
	$dream_users->access_table ="dream_users";
	
	#get user_name
	$select_post = array("user_name");
	$where_post = array("conditions"=>array("id="=>$data['user_id']));
	$getUser = $dream_users->find_row($select_post,$where_post);
	$user_name = $getUser['user_name']; // SELECT user_id FROM `dream_posts` WHERE postId='19'
	#End of user_name

	if($data['user_id'] == $user_id) {
		$data['check_anonymous'] = ($is_anonymous == 1) ? "1" : '0';
		$notify_result = FALSE;
	}
	else {
		$select_notify = array("device_token,notification_status");
		$where_notify = array("conditions"=>array("id="=>$user_id));
		$getDeviceToken = $dream_users->find_row($select_notify,$where_notify);
		$deviceToken = $getDeviceToken['device_token'];
		$notification_status = $getDeviceToken['notification_status'];
		$message = "$user_name commented on your post!";
		
		if($notification_status == 1 && $deviceToken !='') {
			$notify_result = $dream_users->sendPushNotificationIOS($deviceToken,$message,$data['post_id'],'comment');
		}
		else {
			$notify_result = FALSE;
		}	
	}

	$add_comment = $comment->add_row($data);
	
	if($notify_result)
		$notify_status = 'true';
	else
		$notify_status = 'false';
	#End of Get user device_token and Notification	
	
	$response = array("status"=>"true", "status_code"=>"200", "type"=>"comment","notify"=>$notify_status,"post_id"=>$data['post_id'],"message"=>"Comment Added Successfully");
}
else
{
	$response = array("status"=>"false", "status_code"=>"400", "message"=>"Invalid Details!");
}

echo json_encode($response);