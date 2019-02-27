<?php
require_once 'includes/class.query.php';
$response = array();
$getfav_events = array();
$data = json_decode(file_get_contents("php://input"),true);
$likes = new Query;
$likes->access_table ="dream_post_like";
$dream_users = new Query;
$dream_users->access_table ="dream_users";
$dream_posts = new Query;
$dream_posts->access_table = "dream_posts";

$user_id = $data["user_id"];
$post_id = $data["post_id"];
$reaction = $data["reaction"];
$add_reaction_status = $data["add_reaction"];
unset($data['add_reaction']);

$result = array();
$params = array("conditions"=>array("user_id="=>$user_id,"post_id="=>$post_id));
$like_exist = $likes->find_row_count($params,'ID');

#get user_id
$select_post = array("user_id");
$where_post = array("conditions"=>array("postId="=>$post_id));
$getPost = $dream_posts->find_row($select_post,$where_post);
$post_user_id = $getPost['user_id']; // SELECT user_id FROM `dream_posts` WHERE postId='19'
#End of user_id	

#Get user device_token and Notification
#get user_name
$select_user = array("user_name");
$where_user = array("conditions"=>array("id="=>$user_id));
$getUser = $dream_users->find_row($select_user,$where_user);
$user_name = $getUser['user_name']; // SELECT user_id FROM `dream_posts` WHERE postId='19'
#End of user_name

$select_notify = array("device_token,notification_status");
$where_notify = array("conditions"=>array("id="=>$post_user_id));
$getDeviceToken = $dream_users->find_row($select_notify,$where_notify);
$deviceToken = $getDeviceToken['device_token'];
$notification_status = $getDeviceToken['notification_status'];
$message = "$user_name reacted on your post!";
#End of Get user device_token and Notification	

if($like_exist > 0) 
{
	if( !empty($add_reaction_status) && $add_reaction_status=='yes' )
	{
		$data_update = array("reaction"=>$reaction); 
		$where = array("user_id="=>$user_id,"post_id="=>$post_id);
		$update_like_data = $likes->update($data_update, $where);	
		
		/* Get total count of post */
		$params = array("conditions"=>array("post_id="=>$post_id));
		$total_like_exist = $likes->find_row_count($params,'ID');
		/* End of total count of post */

		if($notification_status == 1 && $user_id != $post_user_id) {
			$notify_result = $dream_users->sendPushNotificationIOS($deviceToken,$message,$post_id,'like');
		}
		else {
			$notify_result = FALSE;
		}
				
		if($notify_result)
			$notify_status = 'true';
		else
			$notify_status = 'false';
		
		$response = array("status"=>"true","type"=>"like","notify"=>$notify_status,"total_like_exist"=>$total_like_exist,"reaction"=>$reaction,"notify"=>$notify_result,"post_id"=>$post_id,"message"=>"Reaction posted successfully");				
	}
	else
	{
		$delete = $likes->delete(array("user_id"=>$user_id,"post_id"=>$post_id));
		/* Get total count of post */
		$params = array("conditions"=>array("post_id="=>$post_id));
		$total_like_exist = $likes->find_row_count($params,'ID');
		/* End of total count of post */
		$response = array("status"=>"true","total_like_exist"=>$total_like_exist,"reaction"=>$reaction,"message"=>"Successfully Unliked");		
	}	
}
else
{	
	$add_likes = $likes->add_row_like($data);
	/* Get total count of post */
	$params = array("conditions"=>array("post_id="=>$post_id));
	$total_like_exist = $likes->find_row_count($params,'ID');
	/* End of total count of post */
	
	if($notification_status == 1 && $user_id != $post_user_id) {
		$notify_result = $dream_users->sendPushNotificationIOS($deviceToken,$message,$post_id,'like');
	}
	else {
		$notify_result = FALSE;
	}
	
	if($notify_result)
		$notify_status = 'true';
	else
		$notify_status = 'false';
	
	$response = array("status"=>"true","type"=>"like","notify"=>$notify_status,"total_like_exist"=>$total_like_exist,"reaction"=>$reaction,"notify"=>$notify_result,"post_id"=>$post_id,"message"=>"Successfully Liked");				
}

echo json_encode($response);