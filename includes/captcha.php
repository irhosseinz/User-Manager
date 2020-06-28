<?php
define('UM_CAPTCHA_LENGTH',5);
define('UM_CAPTCHA_SESSION','captcha');
class Captcha
{
    public function __construct($session_key,$length)
    {
        $this->session_key=$session_key;
        $this->length=$length;
    }

    function getCaptchaCode()
    {
        $random_alpha = md5(random_bytes(64));
        $captcha_code = substr($random_alpha, 1, $this->length);
        $this->setSession($captcha_code);
        return $captcha_code;
    }
    
    function setSession($value) {
        @session_start();
        $_SESSION[$this->session_key] = $value;
    }
    
    function getSession() {
        @session_start();
        $value = "";
        if(!empty($this->session_key) && !empty($_SESSION[$this->session_key]))
        {            
            $value = $_SESSION[$this->session_key];
        }
        return $value;
    }

    function createCaptchaImage($captcha_code)
    {
        $target_layer = imagecreatetruecolor(72,28);
        $captcha_background = imagecolorallocate($target_layer, 204, 204, 204);
        imagefill($target_layer,0,0,$captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        imagestring($target_layer, 5, 10, 5, $captcha_code, $captcha_text_color);
        
        return $target_layer;
    }

    function renderCaptchaImage()
    {
        $imageData=$this->createCaptchaImage($this->getCaptchaCode());
        header("Content-type: image/jpeg");
        imagejpeg($imageData);
    }
    
    
    function validateCaptcha($formData) {
        $isValid = false;
        $capchaSessionData = $this-> getSession();
        
        if(strtolower($capchaSessionData) == strtolower($formData)) 
        {
            $isValid = true;
        }
        return $isValid;
    }
}