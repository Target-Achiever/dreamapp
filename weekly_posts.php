<?php
require_once 'includes/class.query.php';
$post = new Query;
$post->access_table = "dream_posts";

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));

 $result = array();
 //$user_id = $data["user_id"];

		$previous_week = strtotime("-1 week +1 day");
		$start_week = strtotime("this sunday midnight",$previous_week);
		$end_week = strtotime("next saturday",$start_week);
		$start_week = date("Y-m-d",$start_week);
		$end_week = date("Y-m-d",$end_week);
		
		$next_wdates = date_range($start_week,$end_week);
		
		function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) 
		{
			//$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);
			while( $current <= $last ) {
			$dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
			}
			return $dates;
		}
		
		foreach($next_wdates as $ndates)
		{
			$count = 0;
			$evilcount = 0;
			$params = array("conditions"=>array("DATE(post_date)="=>$ndates,"post_category="=>"Good Feed"));
			$count = $post->find_row_count($params,'postId');
				
			
			$params2= array("conditions"=>array("DATE(post_date)="=>$ndates,"post_category="=>"Evil Feed"));
			$evilcount = $post->find_row_count($params2,'postId');
			
			$timestamp = strtotime($ndates);
			$schday = date('l', $timestamp);

			$schdetails[$schday]['good_post'] =  $count ;
			$schdetails[$schday]['bad_post'] =  $evilcount;
		}

$response = array("status"=>"true", "post_details"=>$schdetails);

echo json_encode($response);