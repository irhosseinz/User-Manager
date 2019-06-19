/*
https://irhosseinz.github.io/User-Manager/
*/

<?php
include_once('includes/config.php');

if($DB->connect_errno){
	header('Location: install/index.php');
}else{
	$DB->close();
	header('Location: login.php');
}
?>
