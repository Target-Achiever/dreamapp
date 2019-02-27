<?php
require_once 'includes/class.query.php';

$response = array();
//$data = (array)json_decode(file_get_contents("php://input"));
$settings = new Query;
$settings->access_table = "dream_users";
$user_id = isset($_POST['user_id']) ? $_POST['user_id']:'';

if($_POST['action'] == 1)
{
	if(isset($_FILES['profile_image'])){
		$extsn 			= explode('.',$_FILES['profile_image']['name']);
		$assImageName	= 'image_'.time().'.'.$extsn[1];
		$filename2		= "uploads/users";
		$uploadFilePath	= $filename2.'/'.$assImageName;
		$moveUpload		= move_uploaded_file($_FILES['profile_image']['tmp_name'],$uploadFilePath);
	}
	$profile_image = $uploadFilePath;
	$user_name = isset($_POST['user_name']) ? $_POST['user_name']:'';
	$user_city = isset($_POST['user_city']) ? $_POST['user_city']:'';
	$user_dob = isset($_POST['user_dob']) ? $_POST['user_dob']:'';
	$user_gender = isset($_POST['user_gender']) ? $_POST['user_gender']:'';
	$user_address = isset($_POST['user_address']) ? $_POST['user_address']:'';
	$profile_description = isset($_POST['profile_description']) ? $_POST['profile_description']:'';

	$query = "SELECT * FROM dream_users WHERE id = '$user_id'";
	$getCustomer_data = $settings->execute($query);

	$params = array("conditions"=>array("user_name="=>$user_name,"id!="=>$user_id));
	$userName_exist = $settings->find_row_count($params,'id');
	if($userName_exist == 0)
	{ 
		if(!empty($getCustomer_data))
		{
			$result['user_name'] 	 =  isset($user_name)? $user_name : $getCustomer_data['user_name'];
			$result['user_city'] 	 =  isset($user_city)? $user_city : $getCustomer_data['user_city'];
			$result['user_dob'] 	 =  isset($user_dob)? $user_dob : $getCustomer_data['user_dob'];
			$result['profile_image'] =  isset($profile_image)? $profile_image : $getCustomer_data['profile_image'];
			$result['user_gender'] 	 =  isset($user_gender)? $user_gender : $getCustomer_data['user_gender'];
			$result['user_address']  =  isset($user_address)? $user_address : $getCustomer_data['user_address'];
			$result['profile_description'] =  isset($profile_description)? $profile_description : $getCustomer_data['profile_description'];

			$where = array("id"=>$user_id);
			$register_result = $settings->update($result ,$where);

			if($register_result['status']==1)
			{
				$response = array("status"=>"true","user_settings"=>$result);		
			}
			else
			{
				$response = array("status"=>"false");	
			}
		}
	} else {

		$response = array("status"=>"false", "status_code"=>"404", "message"=>"Username Already Exist!");

	}
}
elseif($_POST['action'] == 2)
{
	$user_phone = isset($_POST['user_phone']) ? $_POST['user_phone']:'';
	$query = "SELECT * FROM dream_users WHERE id = '$user_id'";
	$getCustomer_data = $settings->execute($query);
	if(!empty($getCustomer_data))
	{
		$result['user_phone'] =  isset($user_phone)? $user_phone : $getCustomer_data['user_phone'];
		$where = array("id"=>$user_id);
		$register_result = $settings->update($result ,$where);
		if($register_result['status']==1)
		{
		  $response = array("status"=>"true","user_settings"=>$result);		
		} else  {
		  $response = array("status"=>"false");	
		}
	}
}
elseif($_POST['action'] == 3)
{
	$notification_status = isset($_POST['notification_status']) ? $_POST['notification_status']:'';
	$query = "SELECT * FROM dream_users WHERE id = '$user_id'";
	$getCustomer_data = $settings->execute($query);
	if(!empty($getCustomer_data))
	{
		$result['notification_status'] =  isset($notification_status)? $notification_status : $getCustomer_data['notification_status'];
		$where = array("id"=>$user_id);
		$register_result = $settings->update($result ,$where);
		if($register_result['status']==1)
		{
		  $response = array("status"=>"true","user_settings"=>$result);		
		} else  {
		  $response = array("status"=>"false");	
		}
	}
}
elseif($_POST['action'] == 4)
{
	$query = "SELECT * FROM dream_users WHERE id = '$user_id'";
	$getCustomer_data = $settings->execute($query);

	$result['user_name']	 =  isset($getCustomer_data['user_name'])? $getCustomer_data['user_name'] : "";
	$result['user_city'] 	 =  isset($getCustomer_data['user_city'])? $getCustomer_data['user_city'] : "";
	$result['user_dob'] 	 =  isset($getCustomer_data['user_dob'])? $getCustomer_data['user_dob'] : "";
	$result['profile_image'] =  isset($getCustomer_data['profile_image'])? $getCustomer_data['profile_image'] : "";
	$result['user_gender']	 =  isset($getCustomer_data['user_gender'])? $getCustomer_data['user_gender'] : "";
	$result['user_address']  =  isset($getCustomer_data['user_address'])? $getCustomer_data['user_address'] : "";
	$result['profile_description']  =  isset($getCustomer_data['profile_description'])? $getCustomer_data['profile_description'] : "";
	
	 $sql = "SELECT (SELECT COUNT(postId) AS good_posts FROM `dream_posts` WHERE post_category='Faith in Humanity' AND user_id='$user_id') as good_post,
				   (SELECT COUNT(postId) AS evil_posts FROM `dream_posts` WHERE post_category='Evil Lurks Here' AND user_id='$user_id') as evil_posts,
				   (SELECT COUNT(followID) AS followings FROM `dream_user_following` WHERE user_id='$user_id') as followings,
				   (SELECT COUNT(followID) AS followers FROM `dream_user_following` WHERE following_id='$user_id') as followers";
				   
	$result["userPostCounts"] = $settings->execute($sql);

	$response = array("status"=>"true","user_settings"=>$result);		
}
echo json_encode($response);