<?php
include_once('includes/config.php');
include_once('includes/umf.php');
if(isset($_GET['logout'])){
	unset($_SESSION['UM_DATA']);
	setcookie('UM_LOGIN','',-1);
}
if(isset($_SESSION['UM_DATA'])){
	header('Location: user.php');
	exit;
}
$SUCCESS=false;
$ERROR=false;
if(isset($_POST['forget'])){
	$st=$DB->prepare("select * from users where email=? or email_temp=?");
	$st->bind_param('ss',$_POST['forget'],$_POST['forget']);
	if($st->execute() && $r=$st->get_result()->fetch_assoc()){
		$st=$DB->prepare("insert into verify set user_id=?,email=?,secret=?,action=?");
		$p=UM_randomString(rand(30,50));
		$action='forget';
		$email=($r['email']?$r['email']:$r['email_temp']);
		$st->bind_param('isss',$r['_id'],$email,$p,$action);
		if($st->execute()){
			$link=UM_DOMAIN.'/verify.php?verify='.$DB->insert_id.'&email='.urlencode($email).'&code='.$p;
			$mailC=file_get_contents('html/forgot_password.html');
			$mailC=str_replace('<===UM_TITLE===>',$UM_CONFIG['TITLE'],$mailC);
			$mailC=str_replace('<===UM_URL===>',$link,$mailC);
			if(mail($email,"Reset Your Password In {$UM_CONFIG['TITLE']}",$mailC,"MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: ".UM_EMAIL_FROM))
				$SUCCESS="An Link containing Password-Reset link sent to Your Email ({$email}).";
			else
				$ERROR="An error occured on sending Email";
		}
	}else
		$SUCCESS="This email is not a registered Email";
}else if(isset($_POST['email'])){
	try{
		$e=(UM_LOGIN_EXPIRE>0?",expire=TIMESTAMPADD(DAY,".UM_LOGIN_EXPIRE.",NOW())":'');
		$p=UM_randomString(rand(30,40));
		$st=$DB->prepare("select * from users where email=? or email_temp=?");
		$st->bind_param('ss',$_POST['email'],$_POST['email']);
		if($st->execute()){
			$data=$st->get_result()->fetch_assoc();
			if($data['password']==UM_PASSWORD($_POST['password'])){
				$SUCCESS=true;
				$DB->query("insert into login_log set user_id={$data['_id']}{$e},secret='{$p}'");
				$cookie="{$DB->insert_id}_{$p}";
				$_SESSION['UM_DATA']=array('_id'=>$data['_id'],'cookie'=>$cookie);
				if(isset($_POST['remember'])){
					setcookie('UM_LOGIN',$cookie,time()+((UM_LOGIN_EXPIRE>0?UM_LOGIN_EXPIRE:365)*24*3600));
				}
				if(isset($_POST['return']) && $_POST['return'][0]=='/'){
					$return=$_POST['return'];
				}else{
					$return='user.php';
				}
				header("Location: {$return}");
				exit;
			}
		}else{
			
		}
	}catch(Exception $e){
		$ERROR=$e->getMessage();
	}
	if(!$SUCCESS && !$ERROR)
		$ERROR='Invalid username or password!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="@irhosseinz">
<meta content="fa" http-equiv="Content-Language" />

<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/usermanager.css" rel="stylesheet">
<script type="text/javascript" src="/js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script src="/js/jquery.validate.min.js"></script>

<link rel="shortcut icon" href="/img/favicon.png" />
<script type="text/javascript">
$( document ).ready(function(){
	$("#form").validate({
		focusInvalid: false,onkeyup: false,
		errorClass: "is-invalid",validClass: "is-valid",
		errorPlacement: function(error, element) {
			if (element.parent().hasClass('input-group')) {
				error.insertAfter(element.parent());
			} else if (element.attr("name") == "accept"){
				error.insertAfter("#accept2");
			}else{
				error.insertAfter(element);
			}
		}
		,rules:{email:{required:true},password:{required:true}}
		,submitHandler: function(form) {
			form.submit();
		}
	});

});
</script>

</head>
<body>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#"><?php echo $UM_CONFIG['TITLE'];?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="/">Home</a>
      </li>
      <?php 
      if(!isset($_SESSION['UM_DATA'])){
      ?>
      <li class="nav-item">
        <a class="nav-link" href="register.php">Register</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link">Login <span class="sr-only">(current)</span></a>
      </li>
      <?php
      }
      ?>
    </ul>
  </div>
</nav>

<div class="container">
<?php
if(@$ERROR){
	echo '<div class="alert alert-danger" role="alert">'.$ERROR.'</div>';
}else if(@$SUCCESS){
	echo '<div class="alert alert-success" role="alert">'.$SUCCESS.'</div>';
}
if(isset($_GET['forget'])){
?>
<form id="form" action="login.php" method="post">
  <div class="form-group">
    <label for="input_email">Email address</label>
    <input type="email" class="form-control" name="forget" required id="input_email"/>
  </div>
  <br/><button type="submit" class="btn btn-primary my-1">Submit</button>
</form>
<?php
}else{
?>
<form id="form" action="login.php" method="post">
  <div class="form-group">
    <label for="input_email">Email address</label>
    <input type="email" class="form-control" name="email" required id="input_email" value="<?php echo @$_POST['email'];?>"/>
  </div>
  <div class="form-group">
    <label for="input_password">Password</label>
    <input type="password" class="form-control" name="password" required id="input_password"/>
  </div>
  <div class="form-check">
    <input type="checkbox" class="form-check-input" name="remember" id="input_remember"/>
    <label for="input_remember" class="form-check-label">Remember Me</label>
  </div>
  <?php
//	if(isset($_GET['return'])){
//		echo '<input type="hidden" name="return" value="'.$_GET['return'].'">';
//	}
//	if(UM_CAPTCHA_SITE){
//		echo '<script src="https://www.google.com/recaptcha/api.js?render='.UM_CAPTCHA_SITE.'"></script>
//  <script>
//  grecaptcha.ready(function() {
//      grecaptcha.execute("'.UM_CAPTCHA_SITE.'", {action: "'.home.'"}).then(function(token) {
//         alert(token);
//      });
//  });
//  </script>';
//	}
  ?>
  <br/><button type="submit" class="btn btn-primary my-1">Submit</button>
  <br/><a href="login.php?forget"><span class="badge badge-warning">I Forgot my Password</span></a>
</form>
<?php
}
?>
</div>


<hr>
<footer>
<p class="text-center text-ltr engFont">&copy; <a href="https://irhosseinz.github.io/User-Manager/" target="_blank">UserManager</a></p>
</footer>
  </body>
</html>
<?php
$DB->close();
?>
