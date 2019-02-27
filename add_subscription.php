<?php 
require_once 'includes/class.query.php';
$subscription = new Query;
$subscription->access_table = "dream_subscription";

$response = array();
$data = json_decode(file_get_contents("php://input"),true);

$post_image = $uploadFilePath;

$user_id = isset($data['user_id']) ? $data['user_id']:'';
$sql = array("user_id"=>"$user_id","subscription_status"=>1);
  
$add_subscription = $subscription->add_row($sql);

if(!empty($add_subscription) && $add_subscription['status'] == 1) {
	$response = array("status"=>"true", "status_code"=>"200","message"=>"Subscription added successfully");
}
else {
	$response = array("status"=>"false", "status_code"=>"400","message"=>"Server Error");
}

echo json_encode($response);
  
 
?>