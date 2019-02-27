<?php
require_once 'includes/class.query.php';

$response = array();

$data = (array)json_decode(file_get_contents("php://input"));

$settings = new Query;
$settings->access_table = "dream_users";
$user_id = $data["user_id"];

$query = "SELECT * FROM dream_users WHERE id = '$user_id'";
$getCustomer_data = $settings->execute($query);

if(!empty($getCustomer_data)) {

	$latitude = $getCustomer_data['user_latitude'];
	$longitude = $getCustomer_data['user_longitude'];
	$distance = 250;
	$user_list_query = "SELECT *,id as user_id,(SELECT count(*) FROM dream_user_following as fc WHERE fc.user_id=id) as followers_count,(SELECT count(*) FROM dream_posts as pc WHERE pc.user_id=id) as post_count,3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude - user_latitude) * pi()/180 / 2), 2) + COS($latitude * pi()/180) * COS(user_latitude * pi()/180) *POWER(SIN(($longitude - 	user_longitude) * pi()/180 / 2), 2) )) as distance FROM dream_users HAVING distance<=250 AND id!='$user_id' AND user_status=1 AND id NOT IN (SELECT f.following_id FROM dream_user_following as f WHERE f.user_id='$user_id') ORDER BY followers_count desc,post_count desc";
	$suggested_users = $settings->execute_array($user_list_query);

	if(!empty($suggested_users))
	{
		$user_list = array_map(function($arr) {
						unset($arr['user_latitude'],$arr['user_longitude'],$arr['followers_count'],$arr['post_count'],$arr['distance']);
						return $arr;
						}, $suggested_users);
		$response = array("status"=>"true","suggested_users"=>$user_list);	
	}
	else
	{
		$response = array("status"=>"false","suggested_users"=>"No found");	
	}
}
else {
	$response = array("status"=>"false","suggested_users"=>"The user doesn't exist");
}

echo json_encode($response);