 <?php
	require_once 'includes/class.query.php';
	$forgot_password = new Query;
	$forgot_password->access_table = "dream_users";
$data = (array)json_decode(file_get_contents("php://input"));

$user_email = $data["user_email"];
$select = array("*");
$where = array("conditions"=>array("user_email="=>$user_email));
$login_status = $forgot_password->find_row($select, $where);

	if(empty($login_status)) 
	    {
		$response = array("success"=>"false", "success_code"=>"404","message"=>"Email not exist");
			
		}
		else{
			
		    $user_password = base64_decode ($login_status['user_password']);	
			$user_email = $user_email;
			
			$subject = "Guilty Pleasure - Your password";
			$message = "<html>
								<body>
									Hello,<br /><br />
									Your password for Guilty Pleasure : $user_password <br /><br />
									Please use it to login and enjoy with Guilty Pleasure.<br /><br />
									Yours,<br />
									Guilty Pleasure.
								</body>
							</html>";
			$mail = $forgot_password->send_to_mail($user_email,$subject,$message);	
			if($mail['status'] == 1)
			{
			$response = array("success"=>"True", "success_code"=>"200","message"=>"Password sent to your email address.");	
				
			}
		     
		}
	 
	echo json_encode($response);
?>