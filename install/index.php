<?php
include_once('../includes/umf.php');

ini_set('display_errors',false);
if(isset($_GET['error'])){
	$ERROR=$_GET['error'];
}

if($_POST){
	$data=file_get_contents('../includes/config.php');
	$data=preg_replace('%UM_DATA\*\/"TITLE"[\s\S]+\n%U','UM_DATA*/"TITLE"=>\''.$_POST['title'].'\''."\n",$data);
	$db="define('SQL_HOST','{$_POST['host']}');\ndefine('SQL_DB','{$_POST['name']}');\ndefine('SQL_USER','{$_POST['user']}');\ndefine('SQL_PASS','{$_POST['password']}');";
	if(@$_POST['captcha1']){
		$db.="\n\ndefine('UM_CAPTCHA_SITE','{$_POST['captcha1']}');\ndefine('UM_CAPTCHA_SECRET','{$_POST['captcha2']}');";
	}else{
		$db.="\n\ndefine('UM_CAPTCHA_SITE','');//edit this to activate recaptcha-v3\ndefine('UM_CAPTCHA_SECRET','');";
	}
	if(preg_match('%https?:\/\/[^/]+%i',$_POST['domain'],$m)){
		$d=strtolower($m[0]);
	}else if(preg_match('%^[^/]+%i',$_POST['domain'],$m)){
		$d='http://'.strtolower($m[0]);
	}else $d='';
	$db.="\n\ndefine('UM_DOMAIN','{$d}');";
	$db.="\ndefine('UM_EMAIL_FROM','{$_POST['email']}');";
	$data=preg_replace('%UM_CONFIG\*\/[\s\S]+\/\*UM_CONFIG%',"UM_CONFIG*/\n{$db}\n/*UM_CONFIG",$data);
	if(file_put_contents('../includes/config.php',$data)){
		header('Location: fields.php');
		exit;
	}else{
		$ERROR="there was an error! please check includes/config.php file permission. (make it 777)";
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Install</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="@irhosseinz">
<meta content="fa" http-equiv="Content-Language" />

<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/usermanager.css" rel="stylesheet">
<script type="text/javascript" src="../js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.bundle.js"></script>
<script src="../js/jquery.validate.min.js"></script>
<link rel="icon" href="/img/favicon.png">

<link rel="icon" href="/img/favicon.png">
<script type="text/javascript">
$( document ).ready(function(){
	$("#form").validate({
		focusInvalid: false,onkeyup: false,
		errorClass: "is-invalid",validClass: "is-valid",
		rules:{domain:{url:true},email:{email:true}}
		submitHandler: function(form) {
			form.submit();
		}
	});
});
</script>
</head>
<body>

<nav aria-label="breadcrumb">
	<a class="navbar-brand">Installing.. (1/3)</a>
  <ol class="breadcrumb">
    <li class="breadcrumb-item text-success">Database</li>
    <li class="breadcrumb-item text-secondary">Fields</li>
    <li class="breadcrumb-item text-secondary">Finish</li>
  </ol>
</nav>


<div class="container">
<form id="form" method="post" action="index.php">
	<p>Welcome to UserManager, to Get started fill below fields:</p>
  <h3>Website Configuration:</h3>
  <div class="form-group">
    <label for="input_title">Website Title:</label>
    <input type="text" class="form-control" name="title" required id="input_title" required/>
  </div>
  <div class="form-group">
    <label for="input_domain">Your Domain:</label>
    <input type="text" class="form-control" name="domain" required id="input_domain" required/>
  </div>
  <div class="form-group">
    <label for="input_email">An email from above domain for using when sending Email to Users:</label>
    <input type="email" class="form-control" name="email" required id="input_email" required placeholder="noreply@example.com"/>
  </div>
  <div class="form-group">
    <label for="input_captcha1">If you want Recaptcha to be used for login and Registration and.. get a <a href="https://www.google.com/recaptcha/admin" target="_blank">Recaptcha V3</a> and enter it here. (Don't forget to add your domain there!)</label>
    <input type="text" class="form-control" name="captcha1" required id="input_captcha1" placeholder="SITE KEY"/>
    <input type="text" class="form-control" name="captcha2" required id="input_email" placeholder="SECRET KEY"/>
  </div>
  <h3>Mysql Configuration:</h3>
  <div class="form-group">
    <label for="input_host">Database Host</label>
    <input type="text" class="form-control" name="host" required id="input_host" required/>
  </div>
  <div class="form-group">
    <label for="input_name">Database Name</label>
    <input type="text" class="form-control" name="name" required id="input_name" required/>
  </div>
  <div class="form-group">
    <label for="input_user">Database User</label>
    <input type="text" class="form-control" name="user" required id="input_user" required/>
  </div>
  <div class="form-group">
    <label for="input_user">Database Password</label>
    <input type="text" class="form-control" name="password" required id="input_password" required/>
  </div>
	<?php
	if(@$ERROR){
		echo '<div class="alert alert-danger" role="alert">'.$ERROR.'</div>';
	}
	?>
  <br/><button type="submit" class="btn btn-primary my-1 btn-lg">Submit</button>
</form>
</div>


<hr>
<footer>
<p class="text-center text-ltr engFont">&copy; <a href="https://irhosseinz.github.io/User-Manager/" target="_blank">UserManager</a></p>
</footer>
  </body>
</html>
