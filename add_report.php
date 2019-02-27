<?php 
require_once 'includes/class.query.php';

$dream_report = new Query;
$dream_report->access_table = "dream_report";

$data = json_decode(file_get_contents("php://input"),true);
$response = array();

if( !empty($data['user_id']) && !empty($data['post_id']) && !empty($data['comment']) )
{		
	$add_comment = $dream_report->add_row($data);
	$response = array("status"=>"true", "status_code"=>"200", "message"=>"Reported Successfully");
}
else
{
	$response = array("status"=>"false", "status_code"=>"400", "message"=>"Invalid Details!");
}

echo json_encode($response); 