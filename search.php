<?php
require_once 'includes/class.query.php';

$response = array();

$data = (array)json_decode(file_get_contents("php://input"));

$settings = new Query;
$settings->access_table = "dream_users";
$user_id = $data["user_id"];

//print_r($data); exit;

$query = "SELECT * FROM dream_users WHERE id = '$user_id'";
$getCustomer_data = $settings->execute($query);

if(!empty($getCustomer_data)) {

	$latitude = $getCustomer_data['user_latitude'];
	$longitude = $getCustomer_data['user_longitude'];

	/*$user_list_query = "SELECT user_name,profile_image,user_email,(SELECT followID FROM dream_user_following WHERE following_id = id AND user_id ='$user_id') as Is_follow FROM `dream_users` WHERE id != '$user_id' AND user_status = 1 AND user_email LIKE '%".$data['user_text']."%' ";*/

	// $user_list_query = "SELECT u.id as user_id,u.user_name,u.user_email,u.profile_image,(CASE WHEN f.followId!='' THEN 'yes' ELSE 'no' END) as ifFollowing FROM dream_users as u left join dream_user_following as f on following_id = id AND user_id ='$user_id'   WHERE id != '$user_id' AND user_status = 1 AND (user_email LIKE '%".$data['user_text']."%' OR user_name LIKE '%".$data['user_text']."%')";

	$user_list_query = "SELECT u.id as user_id,u.user_name,u.user_email,u.profile_image,(CASE WHEN f.followId!='' THEN 'yes' ELSE 'no' END) as ifFollowing FROM dream_users as u left join dream_user_following as f on following_id = id AND user_id ='$user_id'   WHERE id != '$user_id' AND user_status = 1 AND user_name LIKE '%".$data['user_text']."%'";

	    
	$suggested_users = $settings->execute_array($user_list_query);

    //print_r($suggested_users); exit;

	if(!empty($suggested_users))
	{
		/*foreach ($suggested_users as $key => $value) {
			
			if($value['Is_follow'] != "")
			{
               $suggested_users[$key]['Is_follow']  = 'yes';
			}
			else
			{
				$suggested_users[$key]['Is_follow']  = 'no';
			}
			
		}*/

		/*$user_list = array_map(function($arr) {
						unset($arr['user_latitude'],$arr['user_longitude'],$arr['followers_count'],$arr['post_count']);
						return $arr;
						}, $suggested_users);*/
		$response = array("status"=>"true","suggested_users"=>$suggested_users);	
	}
	else
	{
		$response = array("status"=>"false","suggested_users"=>"Not found");	
	}
}
else {
	$response = array("status"=>"false","suggested_users"=>"The user doesn't exist");
}

echo json_encode($response);