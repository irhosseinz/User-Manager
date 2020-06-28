<?php
include_once(__DIR__.'/config.php');
include_once(__DIR__.'/captcha.php');
function UM_randomString($len){
	$string = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
	$length=strlen($string);
	$out='';
	if(function_exists('random_bytes')){
		$r=random_bytes($len);
		for($i=0;$i<$len;$i++){
			$k=intval(floor(ord($r[$i])*($length-1)/255));
			$out.=$string[$k];
		}
	}else{
		for($i=0;$i<$len;$i++){
			$rand=rand(0,$length-1);
			$out.=$string[$rand];
		}
	}
	return $out;
}
function UM_PASSWORD($password){
	return password_hash($password,PASSWORD_DEFAULT);
}

function UM_PASSWORD_VERIFY($password,$hash){
	return password_verify($password,$hash);
}
function UM_VerifyRecaptcha($response){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,'secret='.urlencode(UM_CAPTCHA_SECRET).'&response='.urlencode($response));
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($ch);
	$json=json_decode($result,true);
	curl_close($ch);
	return (@$json['success'] && $json['score']>0.1);
}
function UM_VerifyCaptcha($response){
	$ch = new Captcha(UM_CAPTCHA_SESSION,UM_CAPTCHA_LENGTH);
	return $ch->validateCaptcha($response);
}
function decodeConfig($c){
	$c=intval($c);
	return array(
		'admin'=>((($c&1)>>0)==1)
		,'edit_admin'=>((($c&2)>>1)==1)
		,'edit_password'=>((($c&4)>>2)==1)
		,'payout'=>((($c&8)>>3)==1)
	);
}
function encodeConfig($setting){
	if(!$setting)
		return 0;
	$o=0;
	if($setting['admin'])
		$o+=(1);
	if($setting['edit_admin'])
		$o+=(2);
	if($setting['edit_password'])
		$o+=(4);
	if($setting['payout'])
		$o+=(8);
	return $o;
}
?>
