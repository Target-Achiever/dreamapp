<?php 
require_once 'includes/class.query.php';
$post = new Query;
$post->access_table = "dream_posts";
$max_post_count = 5;

date_default_timezone_set('Etc/GMT+5');

$response = array();
$data = (array)json_decode(file_get_contents("php://input"));



if(!$_POST['ads']) {

	$subscription_obj = new Query();
	$subscription_obj->access_table = "dream_subscription";	

	$subscription_select = array('user_id');
	$subscription['conditions'] = array('user_id='=>$_POST['user_id'],'subscription_status='=>1);
	$subscription_data = $subscription_obj->find_row($subscription_select,$subscription);

	if(empty($subscription_data)) {

		$post_count_select = array('count(*) as count');
		$post_count['conditions'] = array('DATE(created_est)='=>date('Y-m-d'),'user_id='=>$_POST['user_id']);
		$check_post_count = $post->find_row($post_count_select,$post_count);

		if($check_post_count['count'] >= $max_post_count) {

			$response = array("status"=>"false", "status_code"=>"400","message"=>"Maximum post count exceeded.");
			echo json_encode($response);
			exit;
		}
	}
}

if(isset($_FILES['post_image'])){
$extsn 			= explode('.',$_FILES['post_image']['name']);
$assImageName	= 'image_'.time().'.'.$extsn[1];
$filename2		= "uploads/posts";
$uploadFilePath	= $filename2.'/'.$assImageName;

$moveUpload		= move_uploaded_file($_FILES['post_image']['tmp_name'],$uploadFilePath);
}
$post_image = $uploadFilePath;

$user_id = isset($_POST['user_id']) ? $_POST['user_id']:'';
$post_category = isset($_POST['post_category']) ? $_POST['post_category']:'';
$post_date = isset($_POST['post_date']) ? $_POST['post_date']:'';
$post_detail = isset($_POST['post_detail']) ? $_POST['post_detail']:'';
$check_anonymous = isset($_POST['check_anonymous']) ? $_POST['check_anonymous']:'';
$est = date('Y-m-d H:i:s', strtotime('-30 minutes'));

$sql = array("user_id"=>"$user_id","post_category"=>"$post_category","post_date"=>"$post_date","post_detail"=>"$post_detail","check_anonymous"=>"$check_anonymous","post_image"=>"$post_image","created_est"=>$est);
  
  $add_post = $post->add_row($sql);

  $post_id =  $add_post['insert_id'];
  
  $response = array("status"=>"true", "status_code"=>"200","post_id"=>$post_id, "message"=>"post added Successfully");

echo json_encode($response);
  
 
?>