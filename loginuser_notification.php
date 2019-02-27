<?php
require_once 'includes/class.query.php';
$post = new Query;
$post->access_table = "dream_user_following";

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));

$result = array();
$user_id = $data["user_id"];	
$query = "SELECT user_id,created_on FROM `dream_user_following` where following_id =  '$user_id'";
$getfollowers = $post->execute_array($query);
$count =  count($getfollowers);
$userfollowings = array();
$postlikeDetails = array();
$postcommentDetails = array();

	 foreach($getfollowers as $getfollower)
    {  
			$query2 = "SELECT * FROM dream_users where id =  '".$getfollower['user_id']."'";
			
			$getfollowlists = $post->execute_array($query2);
						
			$followers ="";
			
			if(!empty($getfollowlists)){
			 foreach($getfollowlists as $getfollowerlist)
				{   
						$notifytype = array("type"=>"following");
						$created_date = array("created_date"=>$getfollower['created_on']);
						$frndsfollowings = array_merge($getfollowerlist,$notifytype,$created_date);
					}				
				}
			$userfollowings[] = $frndsfollowings;	
			
	}	

	$query2 = "SELECT * FROM `dream_posts` where user_id = '".$user_id."'";
		
	 $getposts = $post->execute_array($query2);
	if($getposts)
	{  	
		$likeDetails = "";
		// $arr = Array();
		foreach($getposts as $getpost)
		{ 		
			$query3 = "SELECT *,A.created_on as created_date FROM `dream_post_like` as A inner JOIN dream_users as B ON A.user_id = B.id INNER JOIN  dream_posts As C ON C.postId = A.post_id where post_id = '".$getpost['postId']."' AND A.user_id != '".$user_id."'";
			$like_arr = $post->execute_array($query3);
			$like_count = count($like_arr); // Like count
			
			$query4 = "SELECT *,A.created_on as created_date FROM `dream_comment` as A inner JOIN dream_users as B ON A.user_id = B.id INNER JOIN  dream_posts As C ON C.postId = A.post_id where post_id = '".$getpost['postId']."' AND A.user_id != '".$user_id."'";

			$com_arr = $post->execute_array($query4);
			$com_count = count($com_arr);  // Comment count

			$sql = "SELECT * FROM `dream_post_like` WHERE post_id='".$getpost['postId']."' AND user_id='".$user_id."' ";
		    $getlikecount = $post->execute_array($sql);
			$count = count($getlikecount);
			$isliked = array();
			if($count > 0)
			{
				$is_like = "yes";
			} 
			else{
				$is_like = "no";
			}

			if(!empty($like_arr)) {
				$like_arr = array_map(function($like_arr) use ($like_count,$com_count,$is_like){
			    	return $like_arr + ['likescount' => $like_count, 'commentscount' => $com_count,'type' => "like",'islike' => $is_like];
				}, $like_arr);
				$postlikeDetails[] = $like_arr[0];
			}

			if(!empty($com_arr)) {
				$com_arr = array_map(function($com_arr) use ($like_count,$com_count,$is_like){
			    	return $com_arr + ['likescount' => $like_count, 'commentscount' => $com_count,'type' => "comment",'islike' => $is_like];
				}, $com_arr);
				$postcommentDetails[] = $com_arr[0];	
			}

			// $likeDetails[] = $like_arr; // Like array
			// $commentDetails[] = $com_arr; // Comment array
		}
		// $postlikeDetails[] = $likeDetails;
		// $postcommentDetails[] = $commentDetails;
	}
	$finalArr = array_merge($userfollowings,$postlikeDetails,$postcommentDetails);
	$arr = uasort($finalArr, function($a, $b) {
					return $a['created_date'] <= $b['created_date'];
				});
	$response = array("status"=>"true","server_data"=>array_values($finalArr));

	// $response = array("status"=>"true","Followers"=>$userfollowings,"likes"=>$postlikeDetails,"comments"=>$postcommentDetails);

echo json_encode($response);