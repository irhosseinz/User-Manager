<?php
/*UM_DB_CONFIG*/
define('SQL_HOST','localhost');
define('SQL_DB','um');
define('SQL_USER','um');
define('SQL_PASS','3HaE8VXMblIxsHIl');
/*UM_DB_CONFIG*/

define('UM_CAPTCHA_SITE','6Lejf6kUAAAAAKHpy37byUNy95qpiSWQIBHK9_mk');
define('UM_CAPTCHA_SECRET','6Lejf6kUAAAAAEYpOo7fX5Z0G6wFqFnKAvj7uABM');

$DB=new mysqli(SQL_HOST,SQL_USER,SQL_PASS,SQL_DB);
$DB->set_charset('utf8');
session_start();


$UM_CONFIG=array(
	/*UM_DATA*/"TITLE"=>'User Manager'
	,/*UM_DATA*/"LANG"=>'EN'//msg me if you want another language
	,/*UM_DATA*/"MSGS"=>'{"2_mobile":""}'
	,/*UM_DATA*/"RULES"=>'{"1_username":{"required":true,"remote":"register.php?exist","minlength":6},"2_mobile":{"required":true,"minlength":10,"number":true},"3_newsletter":[]}'
	,/*UM_DATA*/"FIELDS"=>'{"1_username":{"name":"username","type":"text","unique":true,"uneditable":true},"2_mobile":{"name":"mobile","type":"number","unique":false,"uneditable":true},"3_newsletter":{"name":"newsletter","type":"checkbox","unique":false,"uneditable":false}}'
);

define('UM_EMAIL_FROM','noreply@example.com');//email to be used while sending email for verification and .. (email's domain should be same as website domain)
define('UM_VERIFY_EMAIL_EXPIRE',4);//hours
define('UM_LOGIN_EXPIRE',10);//days. enter 0 for unlimited
define('UM_PASSWORD_MIN',8);
define(/*UM_DATA*/"UM_PASSWORD_HASH","6FcAnEobdHmrFomxl3QN");
define(/*UM_DATA*/"UM_DOMAIN","http://UserManager.example");

function UM_PASSWORD($password){
	return sha1(UM_PASSWORD_HASH.$password);
}
