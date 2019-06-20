<?php
function UM_randomString($len){
	$string = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
	$lenght=strlen($string);
	$out='';
	for($i=0;$i<$len;$i++){
		$rand=rand(0,$lenght-1);
		$out.=$string[$rand];
	}
	return $out;
}
function UM_VerifyCaptcha($response){
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
	return @$json['success'];
}
?>
