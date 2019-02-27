<?php
require_once 'includes/class.query.php';
$verify = new Query;
$verify->access_table = "dream_users";
?>

<style>
.message_container
{
	width:500px;
	height:300px;
	margin:150px auto;
	border:1px solid #ddd;
    box-shadow:2px 5px 10px 3px #888888;
}
.container_box
{
	padding:30px 10px;
}
.head_success
{
	background-color: #32bea6;
    padding: 39px 10px 17px;
    color: #fff;
}
.head_error
{
	background-color: #d75a4a;
    padding: 39px 10px 17px;
    color: #fff;
}
.head_warning
{
	background-color: #e84849;
    padding: 39px 10px 17px;
    color: #fff;
}
.icon
{
	width:100px;
	margin:0 auto;
	height: auto;
}
.icon img
{
	display: block;
    width: 80px;
    margin: -66px auto;
    background: #fff;
    border-radius: 50px;
    border: 5px solid #fff;   
    position: absolute;
}
h3
{
	text-align: center;
    font-size: 49px;
    font-family: roboto;
	margin: 33px 0px 0px 0px;
}
</style>

<?php



if(!empty($_GET['token'])) {
	$select=array("*");
	$where = array("conditions"=>array("mail_token="=>$_GET['token']));
	$verify_count = $verify->find_row($select, $where);
	if(count($verify_count) && count($verify_count) > 0)
	{
		$update = array("user_status"=>1);
		$where = array("mail_token"=>$_GET['token']);
		$update_status = $verify->update($update ,$where);
		if($update_status) {
			echo "<div class='message_container'> <div class='head_success'> 
					<div class='icon'> <img src='img/check.png' alt='Success'>
					</div> <h3> Success !</h3>
					</div> <div class=container_box>				
					<h2>You have been successfully registered! Please sign in to the application. </h2>
				</div> </div>";
		}
		else {
			echo "<div class='message_container'>  <div class='head_error'> 
					<div class='icon'> <img src='img/warning.png' alt='Success'>
					</div> <h3> Error ! </h3> </div> 
					<div class='container_box'> <h2>Error occur in updation process. Please try again later </h2> </div> </div>";
		}
	}
	else {
		echo "<div class='message_container'>  <div class='head_error'>  
				<div class='icon'> <img src='img/error.png' alt='Success'>
				</div> <h3> Invalid ! </h3> </div> 
				<div class='container_box'> <h2> Token is invalid. Please try again with correct link </h2>
				</div> </div>";
	}
}
else {
	echo "<div class='message_container'> <div class='head_warning'> 
			<div class='icon'> <img src='img/warning.png' alt='Success'>
			</div> <h3> Wrong !</h3> </div> <div class='container_box'>
			<h2> Something went wrong. Please try again later </h2>
			</div> </div>";
}

?>