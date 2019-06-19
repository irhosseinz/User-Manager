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
?>
