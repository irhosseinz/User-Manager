/*
http://h.sandbad.biz/User-Manager/
*/

<?php
if(file_exists('install')){
	header('Location: install/index.php');
}else{
	header('Location: login.php');
}
?>
