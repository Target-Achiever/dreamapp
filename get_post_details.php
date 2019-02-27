<?php
require_once 'includes/class.query.php';

$response = array();

$data = (array)json_decode(file_get_contents("php://input"));

$settings = new Query;
$settings->access_table = "dream_users";
$user_id = $data["user_id"];



 $sql = "SELECT (SELECT COUNT(postId) AS good_posts FROM `dream_posts` WHERE (post_category='Faith in Humanity' OR post_category='Good Feed') AND user_id='$user_id') as good_post,
				   (SELECT COUNT(postId) AS evil_posts FROM `dream_posts` WHERE (post_category='Evil Lurks Here' OR post_category='Evil Feed') AND user_id='$user_id') as evil_posts,
				   (SELECT COUNT(followID) AS followings FROM `dream_user_following` WHERE user_id='$user_id') as followings,
				   (SELECT COUNT(followID) AS followers FROM `dream_user_following` WHERE following_id='$user_id') as followers";
				   
 $result = $settings->execute($sql);	


$response = array("status"=>"true","counts"=> $result);		

echo json_encode($response);