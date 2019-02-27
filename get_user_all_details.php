<?php
require_once 'includes/class.query.php';

$response = array();

$data = (array)json_decode(file_get_contents("php://input"));

$settings = new Query;
$user_id = $data["user_id"];

#get COUNT of following & followers & user good & bad post.
$sql = "SELECT (SELECT COUNT(postId) AS good_posts FROM `dream_posts` WHERE (post_category='Faith in Humanity' OR post_category='Good Feed') AND user_id='$user_id') as good_post,
			   (SELECT COUNT(postId) AS evil_posts FROM `dream_posts` WHERE (post_category='Evil Lurks Here' OR post_category='Evil Feed') AND user_id='$user_id') as evil_posts,
			   (SELECT COUNT(followID) AS followings FROM `dream_user_following` WHERE user_id='$user_id') as followings,
			   (SELECT COUNT(followID) AS followers FROM `dream_user_following` WHERE following_id='$user_id') as followers";			   
$result = $settings->execute($sql);	

#User details
$sql_user_details = "SELECT user_name,user_email,profile_image,profile_description,user_dob,user_gender,user_city,user_address,device_token,notification_status FROM `dream_users` WHERE id='$user_id'";
$result_user_details = $settings->execute($sql_user_details);	

// echo "<pre>";
// print_r($result);
// print_r($result_user_details);
// echo "</pre>";


$response = array("status"=>"true","counts"=> $result,"user_details"=>$result_user_details);		

echo json_encode($response);