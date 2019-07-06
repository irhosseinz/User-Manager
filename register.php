<?php
include_once('includes/config.php');
include_once('includes/umf.php');
if(isset($_GET['exist'])){
	unset($_GET['exist']);
	reset($_GET);
	$k=strtolower(key($_GET));
	if(preg_match('%[^a-zA-Z_0-9]+%',$k)){
		$DB->close();
		die('false');
	}
	$st=$DB->prepare("select _id from users where LOWER({$k})=?");
	$st->bind_param('s',$_GET[$k]);
	if($st->execute() && !$st->fetch()){
		echo 'true';
	}else{
		echo 'false';
	}
	$DB->close();
	exit;
}else if(isset($_POST['email'])){
	$_POST['email']=strtolower($_POST['email']);
	$query="insert into users set email_temp=?,password=?";
	$st_type='ss';
	$st_values=array($_POST['email'],UM_PASSWORD($_POST['password']));
	$fs=json_decode($UM_CONFIG['FIELDS'],true);
	try{
		if(UM_CAPTCHA_SITE && !UM_VerifyCaptcha($_POST['captcha'])){
			throw new Exception('ARE YOU A BOT??');
		}
		foreach($fs as $k=>$v){
			if($v['required'] && !$_POST[$k]){
				throw new Exception("please fill {$v['name']}");
			}
			if($v['regex'] && !preg_match('%'.$v['regex'].'%',$_POST[$k])){
				throw new Exception($v['regexh']?$v['regexh']:'Invalid input');
			}
//			if($v['unique']){
//				$_POST[$k]=strtolower($_POST[$k]);
//			}
			$query.=",{$k}=?";
			$st_type.='s';
			$st_values[]=$_POST[$k];
		}
		$st=$DB->prepare($query);
		$params=array();
		$params[]= &$st_type;
		foreach($st_values as $k2=>$v2)
			$params[]= &$st_values[$k2];
		$res=call_user_func_array(array($st, 'bind_param'), $params);
		
		if($st->execute()){
			$_SESSION['UM_DATA']=array('_id'=>$DB->insert_id);
			header('Location: user.php?verify');
			exit;
			$SUCCESS='Registeration in Complete. please check your email!';
		}else{
//			echo $DB->error;
			$ERROR='There was a error on registeration';
		}
	}catch(Exception $e){
		$ERROR=$e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="@irhosseinz">
<meta content="fa" http-equiv="Content-Language" />

<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/usermanager.css" rel="stylesheet">
<script type="text/javascript" src="/js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.bundle.js"></script>
<script src="/js/jquery.validate.min.js"></script>

<link rel="shortcut icon" href="/img/favicon.png" />
<script type="text/javascript">
$( document ).ready(function(){
	jQuery.validator.addMethod(
		"regex",
		function(value, element, regexp) {
			var re = new RegExp(regexp);
			return this.optional(element) || re.test(value);
		},
		"Entered data does not match required format"
	);
	jQuery.validator.messages.remote = 'This value is already registered';
	var msgs=<?php echo $UM_CONFIG['MSGS2'];?>;
	msgs.accept='You should accept Terms of Service';
	msgs.email={
		remote:'This email is already registered'
	};
	var rules=<?php echo $UM_CONFIG['RULES'];?>;
	rules.email={
		required:true
		,email:true
		,remote:'register.php?exist'
	};
	rules.password={
		required:true
		,minlength:<?php echo UM_PASSWORD_MIN;?>
	};
	rules.password2={
		equalTo:"#input_password"
	};
	msgs.password2={
		equalTo:"Please enter same password again"
	}
	$("#regForm").validate({
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
		,rules:rules,messages:msgs
		,submitHandler: function(form) {
			form.submit();
		}
	});
	$('[data-toggle="popover"]').popover();
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
      <li class="nav-item active">
        <a class="nav-link" href="/">Home</a>
      </li>
      <?php 
      if(!isset($_SESSION['UM_DATA'])){
      ?>
      <li class="nav-item">
        <a class="nav-link">Register <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="login.php">Login</a>
      </li>
      <?php
      }
      ?>
    </ul>
  </div>
</nav>

<div class="container">
<h1>Registeration Form</h1>
<?php
if(@$ERROR){
	echo '<div class="alert alert-danger" role="alert">'.$ERROR.'</div>';
}else if(@$SUCCESS){
	echo '<div class="alert alert-success" role="alert">'.$SUCCESS.'</div>';
}
?>
<form id="regForm" action="register.php" method="post">
  <div class="form-group">
    <label for="input_email">Email address</label>
    <input type="email" class="form-control" name="email" required id="input_email"/>
  </div>
  <div class="form-group">
    <label for="input_password">Password</label>
    <input type="password" class="form-control" name="password" required id="input_password"/>
  </div>
  <div class="form-group">
    <label for="input_password2">Repeat Password</label>
    <input type="password" class="form-control" name="password2" required  id="input_password2"/>
  </div>
  <input type="hidden" name="captcha" id="captcha"/>
  <?php
	if(isset($_GET['return'])){
		echo '<input type="hidden" name="return" value="'.$_GET['return'].'">';
	}
	if(UM_CAPTCHA_SITE){
		echo '<script src="https://www.google.com/recaptcha/api.js?render='.UM_CAPTCHA_SITE.'"></script>
  <script>
  grecaptcha.ready(function() {
      grecaptcha.execute("'.UM_CAPTCHA_SITE.'", {action: "REGISTER"}).then(function(token) {
         $("#captcha").val(token);
      });
  });
  </script>';
	}
	$fs=json_decode($UM_CONFIG['FIELDS'],true);
	$msgs=json_decode($UM_CONFIG['MSGS'],true);
	foreach($fs as $k=>$v){
		if($v['nget'])
			continue;
		echo '<div class="form-group">';
		$hint0=(isset($msgs[$k])?$msgs[$k]:'');
		$hint=(isset($msgs[$k])?'<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="'.$msgs[$k].'">?</a>':'');
		switch($v['type']){
			case 'email':
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<input type="email" class="form-control" name="'.$k.'" id="input_'.$k.'"/>';
				break;
			case 'number':
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<input type="number" class="form-control" name="'.$k.'" id="input_'.$k.'"/>';
				break;
			case 'checkbox':
				echo '<div class="form-check"><input type="checkbox" class="form-check-input" name="'.$k.'" id="input_'.$k.'"/><label for="input_'.$k.'" class="form-check-label">'.$v['name'].'</label>'.$hint.'</div>';
				break;
			case 'select':
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<select class="form-control" name="'.$k.'" id="input_'.$k.'">';
				foreach($v['params'] as $p){
					if(!$p)
						continue;
					echo '<option value="'.$p.'">'.$p.'</option>';
				}
				echo '</select>';
				break;
			default:
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<input type="text" class="form-control" name="'.$k.'" id="input_'.$k.'"/>';
				break;
		}
		echo '</div>';
	}
  ?>

  <div class="custom-control custom-checkbox my-1 mr-sm-2" id="accept2">
    <input type="checkbox" class="custom-control-input" name="accept" id="accept" required>
    <label class="custom-control-label" for="accept">I have read and agree <a target="_blank" href="terms.html">Terms of Service</a></label>
  </div>

  <br/><button type="submit" class="btn btn-primary my-1">Submit</button>
</form>
</div>


<hr>
<footer>
<p class="text-center text-ltr engFont">&copy; <a href="http://h.sandbad.biz/User-Manager/" target="_blank">UserManager</a></p>
</footer>
  </body>
</html>
<?php
$DB->close();
?>
