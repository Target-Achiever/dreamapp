<?php 
include'header.php';

$user = new Query;
$user->access_table ="user";

if(isset($_SESSION['user_id']))
{
	//echo "Already login...";
}
else
{
	if(isset($_REQUEST['code'])){
		$gClient->authenticate();
		$_SESSION['token'] = $gClient->getAccessToken();
		header('Location: ' . filter_var($redirectUrl, FILTER_SANITIZE_URL));
	}

	if (isset($_SESSION['token'])) {
		$gClient->setAccessToken($_SESSION['token']);
	}
echo "gggg".$gClient->getAccessToken();
exit;
	if ($gClient->getAccessToken()) {
		$userProfile = $google_oauthV2->userinfo->get();
			
		#COUNT user email
		$where = array('conditions' => array("email="=>$userProfile['email']));
		$user_count = $user->find_count($where);
		#End of COUNT user email
		
		if(empty($user_count) && $user_count<1)
		{				
			//INSERT data to DB	
			$data = array();
			$data['profile_id'] = $userProfile['id'];
			$data['name'] = $userProfile['name'];
			$data['email'] = $userProfile['email'];
			$data['gender'] = $userProfile['gender'];
			$data['profile_link'] = $userProfile['link'];
			$data['profile_picture'] = $userProfile['picture'];				

			$user_add_row = $user->add_row($data);			
			if($user_add_row['status'])
			{
				$_SESSION['user_id'] = $user_add_row['insert_id'];		
				$_SESSION['user_name'] = $userProfile['name'];	
				#Get lat log and redirect		
				header('Location: http://temp1.pickzy.com/gps_tracker_pro/home');
				#End of Get lat log and redirect
			}
			else
			{
				echo "Error Occured!";
			}
			//End of INSERT data to DB	
		}
		else
		{
			#SELECT user details
			$select = array("user_id","name");
			$where = array("conditions"=>array("email="=>$userProfile['email']));
			$userDetail = $user->find_row($select, $where);
			#End of SELECT user details

			$_SESSION['user_id'] = $userDetail['user_id'];		
			$_SESSION['user_name'] = $userProfile['name'];	
			#Get lat log and redirect		
			header('Location: http://temp1.pickzy.com/gps_tracker_pro/home');
			#End of Get lat log and redirect
		}
	} 
	else {
		$authUrl = $gClient->createAuthUrl();
	}
}
?>


<section class="holder-section">
     <div class="container">
	       <div class="holder-section-input-backup">
			   <div class="holder-content-area">
					<p>Locate & Track Your <span>Family and Friends</span> With Ease</p>
			   </div>
			   <div class="phone-input-headback">
			   <div id="error_message" class ="error_message">Invalid Phone number / Given phone number is not yet registered!</div>
				   <div class="phone-input-area">
						 <!--<select class="country-code">
						   <option value="India(+91)">India(+91)</option>
						 </select>-->
						 <div class="search-btn-relative">
						 <?php $setCookie = (isset($_COOKIE['userPhone']))? $_COOKIE['userPhone'] : "" ; ?>
						   <input type="text" id="contact" class="number-code numericOnly" value ="<?php echo $setCookie ;?>" placeholder="Enter Mobile Number"/>
							<?php if(isset($_SESSION['user_id']))
							{ ?>
								<button onclick="searchLocation(); setphoneNo();"><i class="fa fa-search"></i></button>
							<?php
							}
							else
							{ ?>
								<button onclick="setphoneNo()" data-toggle="modal" data-target="#myModal"><i class="fa fa-search"></i></button>
							<?php } ?>					   
						 </div>
				   </div>
			   </div>
		   </div>
		   <div class="phone-input-headback">
			   <div class="phone-input-area">
				  <div class="gps-phone">
					  <img class="animate-tracks" src="img/gps-track.png"/>
					  <img src="img/phone.png"/>
				  </div>
				  <div class="gpsphone-content">
				      <div class="gpsphone-inner-content">
					      <h2>Download GPS Tracker Pro  on your Mobile</h2>
						  <ul class="list-content">
						      <li>Keep Track of your family and friends</li>
						      <li>View their location on the map</li>
						      <li>Get accurate search with Gps Tracker app</li>
						  </ul>
						  <a href="#"><img src="img/Playstore.png"/></a>
					  </div>
				  </div>
			   </div>
		   </div>
	 </div>
</section>


<?php include 'footer.php';?>
