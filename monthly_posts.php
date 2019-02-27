<?php
require_once 'includes/class.query.php';
$post = new Query;
$post->access_table = "dream_posts";

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));

 $result = array();
 //$user_id = $data["user_id"];

		$month = $data["month"];
		$year  = $data["year"];

			$params = array("conditions"=>array("Month(post_date)="=>$month,"YEAR(post_date)="=>$year,"post_category="=>"Good Feed"));
			$goodcount = $post->find_row_count($params,'postId');
				
			
			$params2= array("conditions"=>array("Month(post_date)="=>$month,"YEAR(post_date)="=>$year,"post_category="=>"Evil Feed"));
			$evilcount = $post->find_row_count($params2,'postId');



$response = array("status"=>"true", "good_posts"=>$goodcount, "evil_posts"=>$evilcount);

echo json_encode($response);