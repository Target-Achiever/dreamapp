<?php

require_once 'includes/class.query.php';
$follow = new Query;
$follow->access_table = "dream_user_following";

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));
$result = array(); 
$user_id = $data["user_id"];
$action =  $data["action"];

	if($action == 1)
	{
	 $query		= "SELECT T2.* FROM dream_user_following as T1 INNER JOIN dream_users as T2 ON T1.user_id = T2.id WHERE T1.following_id = '".$user_id."'";	
	
	}
	else{
		
	$query		= "SELECT T2.* FROM dream_user_following as T1 INNER JOIN dream_users as T2 ON T1.following_id = T2.id WHERE T1.user_id = '".$user_id."'";	
		
	}
	

	$getfollows = $follow->execute_array($query);
	$count =  count($getfollows);

if(!empty($getfollows))
{
for($i =0;$i<$count;)
{
	 foreach($getfollows as $getfollow)
    {
	$result[$i]['user_id'] 			=  $getfollow['id'];
	$result[$i]['user_name'] 		=  $getfollow['user_name'];
	$result[$i]['user_email'] 		=  $getfollow['user_email'];
	$result[$i]['profile_image']	=  $getfollow['profile_image'];
	$result[$i]['user_gender'] 		=  $getfollow['user_gender'];
	
	 $query		= "SELECT * FROM dream_user_following WHERE user_id = '".$user_id."' AND following_id = '".$getfollow['id']."'";
	 $getfollow = $follow->execute_array($query);
	 $countfollow =  count($getfollow);
	 if($countfollow > 0)
	 {
	 $result[$i]['ifFollowing'] 		=  "yes"; 
	 } else {
	 $result[$i]['ifFollowing'] 		=  "No";  
		 
	 }

	$response = array("status"=>"true", "followers_details"=>$result);

	 $i++;
	}	
}

}
else
{
	$response = array("status"=>"false");
}

echo json_encode($response);