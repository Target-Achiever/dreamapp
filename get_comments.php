<?php
require_once 'includes/class.query.php';
$comment = new Query;
$comment->access_table = "dream_comment";

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));

$result = array();
$post_id = $data["post_id"];
$query = "SELECT b.id,b.user_name,b.profile_image,a.comment,a.check_anonymous
FROM dream_comment as a
INNER JOIN dream_users as b ON b.id = a.user_id
WHERE a.post_id = '".$post_id."' ";
$getcomments = $comment->execute_array($query);

if(count($getcomments))
{
	for($i =0;$i<count($getcomments);)
	{
		foreach($getcomments as $getcomment)
		{
			$result[$i]['user_id'] =  $getcomment['id'];
			$result[$i]['user_name'] =  $getcomment['user_name'];
			$result[$i]['profile_image'] =  $getcomment['profile_image'];
			$result[$i]['comment'] =  $getcomment['comment'];
			$result[$i]['check_anonymous'] =  $getcomment['check_anonymous'];
			$i++;
		}	
	}
	$response = array("status"=>"true", "Comments"=>$result);
}
else
{
	$response = array("status"=>"false");
}

echo json_encode($response);