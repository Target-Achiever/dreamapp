<?php
require_once 'includes/class.query.php';
$post = new Query;
$post->access_table = "dream_posts";

// error_reporting(0);


$response = array();
$data = json_decode(file_get_contents("php://input"),true);

$result = array();
$user_id = $data["user_id"];  //$query = "SELECT * FROM dream_posts WHERE user_id = '$user_id'";

#COUNT POST and FOLLOW
$sql_count_post = "SELECT COUNT(postId) as count_post FROM `dream_posts` WHERE user_id='".$user_id."'";
$sql_count_follow = "SELECT COUNT(followID) as count_follow FROM `dream_user_following` WHERE user_id='".$user_id."'";
$count_post = $post->execute($sql_count_post);
$count_follow = $post->execute($sql_count_follow);
#End of COUNT
if(empty($count_post['count_post']) && empty($count_follow['count_follow']))
{
	$response = array("status"=>"false", "message"=>"No data found.");	
	echo json_encode($response);
	die();
}

/* Get followers and following user id */
$sql_followers = "SELECT GROUP_CONCAT(id) AS id FROM dream_user_following as T1 INNER JOIN dream_users as T2 ON T1.user_id = T2.id WHERE T1.following_id = '".$user_id."'";
$sql_following = "SELECT GROUP_CONCAT(id) AS id FROM dream_user_following as T1 INNER JOIN dream_users as T2 ON T1.following_id = T2.id WHERE T1.user_id ='".$user_id."'";
$list_followers = $post->execute($sql_followers);
$list_following = $post->execute($sql_following);

$list_followers_str = array_filter($list_followers); 
$list_following_str = array_filter($list_following); 
$following_followers_id = '';
if(!empty($list_followers_str))
{
	$following_followers_id .= $list_followers_str['id'];
}
else{
	
	$following_followers_id = 0;
}
if(!empty($list_following_str))
{
	$following_followers_id .= $list_following_str['id'];
}else{
	$following_followers_id = 0;
}
//$following_followers_id = $list_followers['id'] . "," . $list_following['id'];
/* End of followers and following user id */

$query = "SELECT dream_users.user_name,dream_users.profile_image,dream_posts.postId,dream_posts.user_id,dream_posts.post_image,dream_posts.post_category,dream_posts.post_date,dream_posts.post_detail,dream_posts.check_anonymous 
FROM dream_posts 
LEFT JOIN dream_users ON dream_users.id=dream_posts.user_id
WHERE user_id IN(".$user_id.",".$following_followers_id.") ";

$getposts = $post->execute_array($query);
$count =  count($getposts);

if(!empty($getposts))
{
	$i = 0;
	
	foreach($getposts as $getpost)
    {		
				
	if($getpost['postId']!='')
	{	
		
		$post_id = $getpost['postId'];
		
		#get post report count
		$dream_report = new Query;
		$dream_report->access_table = "dream_report";
		
		$sql = "SELECT user_id FROM dream_report WHERE post_id=$post_id";
		$getReport = $dream_report->execute_array($sql);
		#End of post report count	
		
		#Check user report to this post
		$sql = "SELECT * FROM `dream_report` WHERE post_id=$post_id AND user_id=$user_id";
		$getUserReport = $dream_report->execute_array($sql);
		#End of Check user report to this post 

		//print_r($getReport);		
		
		if(count($getReport) < 10)
		{			
			
			#Check Post have report - post_report_status
			if(count($getUserReport))
			{
				$post_report_status = "Yes";			
			}
			else
			{				
				$post_report_status = "No";					
			}	
			#End of check Post have report - post_report_status
		
		
			$post_like = new Query;
			$post_like->access_table = "dream_post_like";
			$sql = "SELECT user_id,reaction FROM `dream_post_like` WHERE post_id='".$post_id."' ";
			$post_like_result = $post_like->execute_array($sql);		
					 
			if(count($post_like_result))
			{
				//$post_like_user_id_array = array_column($post_like_result,"user_id");
				$like_status = 0;
				
				foreach($post_like_result as $post_like_result_values)
				{
					$like_status = $post_like_result_values['user_id']==$user_id ? ++$like_status:"0";			 
				}						
				if($like_status)			
					$post_like_status = "Yes";
				else
					$post_like_status = "No";
			}
			else
			{
				$post_like_status = "No";
			}			
			$com_user_id = $getpost['user_id'];
			$com_sql = "SELECT * FROM `dream_comment` WHERE post_id='".$post_id."' AND user_id = '".$com_user_id."'";
			$com_user_id = $post_like->execute_array($com_sql);
			if(!empty($com_user_id))
			{
				$post_comment_status = "Yes";			
			}
			else
			{
				
			$post_comment_status = "No";	
				
			}
			
			/* Get comments and comments status */
			$comment_sql = "SELECT a.comment,b.user_name FROM `dream_comment` as a INNER JOIN dream_users as b ON a.user_id = b.id  WHERE post_id= '".$post_id."' ORDER BY a.id DESC LIMIT 1 ";	
			$post_comments_result = $post_like->execute_array($comment_sql);
					
			$comment_count_sql = "SELECT COUNT(id) AS count FROM `dream_comment` WHERE post_id='".$post_id."'";
			$comment_count_result = $post_like->execute($comment_count_sql);
						
			if(!empty($post_comments_result))
			{			
				$result[$i]['comments'] =  $post_comments_result;	
			}
			else{			
				$result[$i]['comments'] =  "";	
			}
			/* end comments and comments status */
			$result[$i]['user_name'] =  $getpost['user_name'];
			$result[$i]['profile_image'] =  $getpost['profile_image'];
			$result[$i]['post_id'] =  $getpost['postId'];
			$result[$i]['user_id'] =  $getpost['user_id'];	
			$result[$i]['post_like_count'] =  count($post_like_result);
			$result[$i]['post_like_status'] =  $post_like_status;
			$result[$i]['post_comment_count'] =  $comment_count_result['count'];
			$result[$i]['post_comment_status'] =  $post_comment_status;
			$result[$i]['post_like_reaction'] =  (isset($post_like_result)) ? $post_like_result:"NIL";
			$result[$i]['post_category'] =  $getpost['post_category'];
			$result[$i]['post_date'] =  $getpost['post_date'];
			$result[$i]['post_image'] =  $getpost['post_image'];
			$result[$i]['post_detail'] =  $getpost['post_detail'];
			$result[$i]['check_anonymous'] =  $getpost['check_anonymous'];
			$result[$i]['post_report_status'] =  $post_report_status;
			
			$i++;
		}
	}
	}
	
	$response = array("status"=>"true", "post_details"=>$result);	
}
else
{
	$response = array("status"=>"false");
}

echo json_encode($response);