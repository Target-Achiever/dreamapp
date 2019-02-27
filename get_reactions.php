<?php
	require_once 'includes/class.query.php';
	$reaction = new Query;
	$reaction->access_table = "dream_post_like";
	$response = array();
	$data = (array)json_decode(file_get_contents("php://input"));
	$result = array();
	$post_id = $data["post_id"];
	$user_id = $data["user_id"];

	$query = "SELECT dream_users.id,dream_users.user_name,dream_users.profile_image,dream_post_like.reaction,dream_post_like.user_id
	FROM dream_post_like 
	LEFT JOIN dream_users ON dream_users.id=dream_post_like.user_id
	WHERE dream_post_like.post_id = '".$post_id."' ";

	$getreaction = $reaction->execute_array($query);
	$count =  count($getreaction);
	if(!empty($getreaction))
	{
		for($i =0;$i<$count;)
		{	$like_status = 0;
			foreach($getreaction as $reaction)
			{
				$result[$i]['user_id'] =  $reaction['id'];				
				$result[$i]['user_name'] =  $reaction['user_name'];
				$result[$i]['profile_image'] =  $reaction['profile_image'];
				$result[$i]['reaction'] =  $reaction['reaction'];
				$like_status = $reaction['user_id']==$user_id ? ++$like_status:"0";		
				if($like_status)			
					$result[$i]['post_like_status'] = "Yes";
				else
					$result[$i]['post_like_status'] = "No";
				$i++;
			}	
		}
			$response = array("status"=>"true", "reaction"=>$result);
	}
	else
	{
		$response = array("status"=>"false");
	}

	echo json_encode($response);