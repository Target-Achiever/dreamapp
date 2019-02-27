<?php
/* ===========     Server details      ============= */
// Local Server
if($_SERVER['SERVER_NAME'] == 'localhost') {
	// define("BASE_URL","http://localhost/corners/");
	// define("UPLOADS","http://localhost/corners/uploads/");
}
else 
{
	define("BASE_URL","http://temp1.pickzy.com/dreamApp/api_new/");
	// define("UPLOADS","http://temp1.pickzy.com/corners/uploads/");
}
//-------------------------------set default time zone
// date_default_timezone_set("Asia/Kolkata");
//----------------------------------------------------
?>