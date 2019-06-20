<?php
ini_set("log_errors", 1);
ini_set("error_log", "errors.log");
/*UM_CONFIG*/
define('SQL_HOST','localhost');
define('SQL_DB','pool_mpool');
define('SQL_USER','pool_mpool');
define('SQL_PASS','lFz9Tmpp8c');

define('UM_CAPTCHA_SITE','6Lejf6kUAAAAAKHpy37byUNy95qpiSWQIBHK9_mk');
define('UM_CAPTCHA_SECRET','6Lejf6kUAAAAAEYpOo7fX5Z0G6wFqFnKAvj7uABM');

define('UM_DOMAIN','http://mining.asiapool.org');
define('UM_EMAIL_FROM','noreply@asiapool.org');
/*UM_CONFIG*/

$DB=new mysqli(SQL_HOST,SQL_USER,SQL_PASS,SQL_DB);
$DB->set_charset('utf8');
session_start();
if(!isset($_SESSION['UM_DATA']) && isset($_COOKIE['UM_LOGIN'])){
	$c=explode('_',$_COOKIE['UM_LOGIN']);
	$r=$DB->query("select *,UNIX_TIMESTAMP(expire) as e from login_log where _id=".intval($c[0]))->fetch_assoc();
	if($r['e']>time() && $r['secret']==$c[1]){
		$_SESSION['UM_DATA']=array('_id'=>$r['user_id']);
	}else{
		setcookie('UM_LOGIN','',-1);
	}
}


$UM_CONFIG=array(
	/*UM_DATA*/"TITLE"=>'Asiapool'
	,/*UM_DATA*/"LANG"=>'EN'//msg me if you want another language
	,/*UM_DATA*/"MSGS"=>'{"1_username":"is Used while configuring Miner","2_mobile":"If Your mobile Number is: +123456789 Enter 123456789"}'
	,/*UM_DATA*/"RULES"=>'{"1_username":{"required":true,"remote":"register.php?exist","minlength":5},"2_mobile":{"required":true,"minlength":10,"maxlength":20,"number":true},"3_bitcoinw":{"required":true}}'
	,/*UM_DATA*/"FIELDS"=>'{"1_username":{"name":"Username","type":"text","unique":true,"uneditable":true,"nget":false},"2_mobile":{"name":"Mobile","type":"number","unique":false,"uneditable":false,"nget":false},"3_bitcoinw":{"name":"Bitcoin Wallet","type":"text","unique":false,"uneditable":false,"nget":true}}'
);

define(/*UM_DATA*/"UM_PASSWORD_HASH","JEnwNGCVIrPgBgCwNPRD");//DONT TOUCH THIS!!!!

define('UM_VERIFY_EMAIL_EXPIRE',4);//hours
define('UM_LOGIN_EXPIRE',10);//days. enter 0 for unlimited
define('UM_PASSWORD_MIN',8);

function UM_PASSWORD($password){
	return sha1(UM_PASSWORD_HASH.$password);
}
