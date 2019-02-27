<?php
require_once 'includes/class.query.php';
$post = new Query;
$post->access_table = "dream_posts";


$deviceToken= 'bb55c4c40eff6423be63c9a0031ee2ebd7256ba059440f449a2a8bb9a17e3ab8';
$message= 'Testing!';

$result = $post->sendPushNotificationIOS($deviceToken,$message);

echo $result;
