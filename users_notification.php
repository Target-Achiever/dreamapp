<?php
require_once 'includes/class.query.php';
$post = new Query;
$post->access_table = "dream_user_following";

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));

$result = array();
 $user_id = $data["user_id"];
 	$query = "SELECT following_id FROM `dream_user_following` where user_id =  '$user_id'";
	$getfollowings = $post->execute_array($query);
	$count =  count($getfollowings);

$userfollowings = array();
$frndzfavposts = array();
$frndzcommentposts = array();

	if(!empty($getfollowings))
{
	 foreach($getfollowings as $getfollowing)
    {  
			$query2 = "SELECT A.created_on,A.following_id,B.user_name,B.profile_image as follower_image FROM `dream_user_following` as A INNER JOIN `dream_users` as B ON A.following_id = B.id  where A.user_id =  '".$getfollowing['following_id']."'";
			$getfollowlists = $post->execute_array($query2);
			
			$sqluser = "SELECT user_name,profile_image FROM `dream_users` WHERE id= '".$getfollowing['following_id']."'";
			$myfav = $post->execute_array($sqluser);
						
			$myfavdet = array("myfollow"=>$myfav[0]['user_name'],"profile_image"=>$myfav[0]['profile_image'],"myfollowid"=>$getfollowing['following_id']);
			
			$frndsfollowings ="";
			
			if(!empty($getfollowlists)){
			 foreach($getfollowlists as $getfollowlist)
				{   
						$notifytype = array("type"=>"following");
						// $frndsfollowings[] = array_merge($getfollowlist,$myfavdet,$notifytype);
						$userfollowings[] = array_merge($getfollowlist,$myfavdet,$notifytype);
					}				
				}
			// $userfollowings[] = $frndsfollowings;	

			$query3 = "SELECT *,dream_users.profile_image as frnd_profile FROM `dream_post_like` INNER JOIN `dream_posts` ON dream_post_like.post_id = dream_posts.postId INNER JOIN dream_users ON dream_users.id = dream_posts.user_id where dream_post_like.user_id = '".$getfollowing['following_id']."'";
			$getlikelists = $post->execute_array($query3);
			$favposts ="";
			 foreach($getlikelists as $getlikelist)
				{    
				  
					$sql = "SELECT * FROM `dream_post_like` WHERE post_id='".$getlikelist['post_id']."' AND user_id='".$user_id."' ";
				    $getlikecount = $post->execute_array($sql);
					$count = count($getlikecount);
					$isliked = array();
					if($count > 0)
					{
						 $isliked = array("islike"=>"yes");
					} else{
						
						$isliked = array("islike"=>"No");
					}
					
					$comment_count_sql = "SELECT COUNT(id) AS commentcount FROM `dream_comment` WHERE post_id='".$getlikelist['post_id']."'";
					$comment_count_result = $post->execute($comment_count_sql);
				
					$likesql = "SELECT COUNT(ID) AS likecount FROM `dream_post_like` WHERE post_id='".$getlikelist['post_id']."' ";
					$post_like_result = $post->execute($likesql);
					
					
					$username  = array("username"=>$getlikelist['user_name']);
					
					$notifytype = array("type"=>"like");
					// $favposts[]  = array_merge($getlikelist,$isliked,$myfavdet,$notifytype,$comment_count_result,$post_like_result,$username);
					$frndzfavposts[]  = array_merge($getlikelist,$isliked,$myfavdet,$notifytype,$comment_count_result,$post_like_result,$username);
				}
			// $frndzfavposts[] = (!empty($favposts))?$favposts:""; 
			
			$query4 = "SELECT *,dream_users.profile_image as frnd_profile FROM `dream_comment` INNER JOIN `dream_posts` ON dream_comment.post_id = dream_posts.postId
			INNER JOIN dream_users ON dream_users.id = dream_posts.user_id where dream_comment.user_id = '".$getfollowing['following_id']."'";
			$getcommentLists = $post->execute_array($query4);
			$commentposts ="";
			 foreach($getcommentLists as $getcommentList)
				{    
				
					$sql = "SELECT * FROM `dream_post_like` WHERE post_id='".$getcommentList['post_id']."' AND user_id='".$user_id."' ";
				    $getlikecount = $post->execute_array($sql);
					$count = count($getlikecount);
					$isliked = array();
					if($count > 0)
					{
						 $isliked = array("islike"=>"yes");
					} else{
						
						$isliked = array("islike"=>"No");
					}
					
					$comment_count_sql = "SELECT COUNT(id) AS count FROM `dream_comment` WHERE post_id='".$getcommentList['post_id']."'";
					$comment_count_result = $post->execute($comment_count_sql);
					
					
					$likesql = "SELECT COUNT(ID) AS likecount FROM `dream_post_like` WHERE post_id='".$getcommentList['post_id']."' ";
					$post_like_result = $post->execute($likesql);
										
					$username  = array("username"=>$getcommentList['user_name']);
					
					$notifytype = array("type"=>"comments");
					// $commentposts[]  = array_merge($getcommentList,$isliked,$myfavdet,$notifytype,$username,$comment_count_result,$post_like_result);
					$frndzcommentposts[]  = array_merge($getcommentList,$isliked,$myfavdet,$notifytype,$username,$comment_count_result,$post_like_result);
				}
			// $frndzcommentposts[] = (!empty($commentposts))?$commentposts:""; 
			
	}

	$finalArr = array_merge($userfollowings,$frndzfavposts,$frndzcommentposts);

	$arr = uasort($finalArr, function($a, $b) {
					return $a['created_on'] <= $b['created_on'];
				});
	$response = array("status"=>"true","server_data"=>array_values($finalArr));
	// $response = array("status"=>"true","Following"=>$userfollowings,"likeLists"=>$frndzfavposts,"commentposts"=>$frndzcommentposts);
}
else
{
	$response = array("status"=>"false");
}

echo json_encode($response);