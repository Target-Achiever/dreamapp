<?php
require_once 'includes/class.query.php';


$data = (array)json_decode(file_get_contents("php://input"));

$suggest = new Query;
$suggest->access_table = "dream_users";
$phone_no = $data["user_id"];

$phone_nos = explode(",",$phone_no);

$count= count($phone_nos);


for($i =0;$i<$count;)
{ 
	 foreach($phone_nos as $phone_num)
    {
    $params = array("conditions"=>array("user_phone="=>$phone_num));
	$sql_result = $suggest->find_count($params);
	print_r($sql_result);
	exit;
	}	
}
echo json_encode($response);
	 ?>