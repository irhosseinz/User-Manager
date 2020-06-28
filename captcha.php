<?php
include(__DIR__.'/includes/captcha.php');
$ch = new Captcha(UM_CAPTCHA_SESSION,UM_CAPTCHA_LENGTH);
$ch->renderCaptchaImage();
?>