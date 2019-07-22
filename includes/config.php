<?php
ini_set("log_errors", 0);
ini_set("error_log", "errors.log");
/*UM_CONFIG*/
define('SQL_HOST','localhost');
define('SQL_DB','um');
define('SQL_USER','um');
define('SQL_PASS','3HaE8VXMblIxsHIl');

define('UM_CAPTCHA_SITE','6Lejf6kUAAAAAKHpy37byUNy95qpiSWQIBHK9_mk');
define('UM_CAPTCHA_SECRET','6Lejf6kUAAAAAEYpOo7fX5Z0G6wFqFnKAvj7uABM');

define('UM_DOMAIN','http://UserManager.example');
define('UM_EMAIL_FROM','noreply@example.com');

define('UM_REFERRAL_ACTIVE',true);//activates referral system in user dashboard
/*UM_CONFIG*/
define('UM_REFERRAL_FORMAT','/register.php?ref=%d');//you can change this to whatever page that config.php is loaded

$DB=new mysqli(SQL_HOST,SQL_USER,SQL_PASS,SQL_DB);
$DB->set_charset('utf8');
session_start();
if(!isset($_SESSION['ref']) && isset($_GET['ref'])){
	$_SESSION['ref']=$_GET['ref'];
}
if(!isset($_SESSION['UM_DATA']) && isset($_COOKIE['UM_LOGIN'])){
	$c=explode('_',$_COOKIE['UM_LOGIN']);
	$r=$DB->query("select *,UNIX_TIMESTAMP(expire) as e from login_log where _id=".intval($c[0]))->fetch_assoc();
	if($r['e']>time() && $r['secret']==$c[1]){
		$_SESSION['UM_DATA']=array('_id'=>$r['user_id']);
		$r=$DB->query("select perm from users where _id=".intval($r['user_id']))->fetch_assoc();
		$_SESSION['UM_DATA']['perm']=decodeConfig($r['perm']);
	}else{
		setcookie('UM_LOGIN','',-1);
	}
}


$UM_PERM=array(//special permissions (add anything and it will be in $_SESSION['UM_DATA']['perm'] as a boolean with the key you specify here)
	'admin'
	,'edit_admin'
	,'edit_password'
);

$UM_CONFIG=array(
	/*UM_DATA*/"TITLE"=>'User Manager'
	,/*UM_DATA*/"LANG"=>'EN'//msg me if you want another language
	,/*UM_DATA*/"MSGS"=>'{"2_mobile":""}'
	,/*UM_DATA*/"RULES"=>'{"1_username":{"required":true,"remote":"register.php?exist","minlength":6},"2_mobile":{"required":true,"minlength":10,"number":true},"3_newsletter":[]}'
	,/*UM_DATA*/"FIELDS"=>'{"1_username":{"name":"username","type":"text","unique":true,"uneditable":true},"2_mobile":{"name":"mobile","type":"number","unique":false,"uneditable":true},"3_newsletter":{"name":"newsletter","type":"checkbox","unique":false,"uneditable":false}}'
);

define('UM_VERIFY_EMAIL_EXPIRE',4);//hours
define('UM_LOGIN_EXPIRE',10);//days. enter 0 for unlimited
define('UM_PASSWORD_MIN',8);

