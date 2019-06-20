<?php
include_once('includes/config.php');
$SUCCESS=false;
$ERROR=false;
$AUTHORIZED=false;
if(isset($_POST['change']) && isset($_SESSION['UM_DATA']['forget'])){
	$DB->query("update users set password='".UM_PASSWORD($_POST['password'])."' where _id={$_SESSION['UM_DATA']['forget']}");
	unset($_SESSION['UM_DATA']['forget']);
	$SUCCESS="Your Password has been Changed";
}else if(!isset($_GET['verify'])){
	header('Location: user.php');
	exit;
}
if(isset($_GET['verify'])){
	$r=$DB->query("select *,UNIX_TIMESTAMP(date) as date from verify where _id=".intval($_GET['verify']))->fetch_assoc();
	try{
		if(!$r || $r['used'])
			throw new Exception('Link is Invalid');
		if($r['email']!=$_GET['email'] || $r['secret']!=$_GET['code']){
			$DB->query("update verify set wrong=wrong+1 where _id={$r['_id']}");
			throw new Exception('Link is Invalid');
		}
		if($r['wrong']>10 || time()>($r['date']+(UM_VERIFY_EMAIL_EXPIRE*3600))){
			throw new Exception('Link is Expired! please repeat your Request.');
		}
		$DB->query("update verify set used=1 where _id={$r['_id']}");
		if($r['action']=='verify'){
			$st=$DB->prepare('update users set email=? where _id=?');
			$st->bind_param('si',$r['email'],$r['user_id']);
			$st->execute();
			$SUCCESS="Your email Verified";
		}else if($r['action']=='forget'){
			$_SESSION['UM_DATA']=array('_id'=>$r['user_id'],'forget'=>$r['user_id']);
			$AUTHORIZED=true;
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
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script src="/js/jquery.validate.min.js"></script>

<link rel="shortcut icon" href="/img/favicon.png" />
<script type="text/javascript">
$( document ).ready(function(){
	var rules={password0:{required:true}},msgs={};
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
	$("#form").validate({
		focusInvalid: false,onkeyup: false,
		errorClass: "is-invalid",validClass: "is-valid",
		errorPlacement: function(error, element) {
			if (element.parent().hasClass('input-group')) {
				error.insertAfter(element.parent());
			}else{
				error.insertAfter(element);
			}
		}
		,rules:rules,messages:msgs
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
      <li class="nav-item active">
        <a class="nav-link" href="/">Home</a>
      </li>
      <?php 
      if(!isset($_SESSION['UM_DATA'])){
      ?>
      <li class="nav-item">
        <a class="nav-link" href="register.php">Register</a>
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
<?php
if(@$ERROR){
	echo '<div class="alert alert-danger" role="alert">'.$ERROR.'</div>';
}else if(@$SUCCESS){
	echo '<div class="alert alert-success" role="alert">'.$SUCCESS.'</div>';
}
if($AUTHORIZED){
?>
<form id="form" action="verify.php" method="post">
  <hr/><h4>Change Password:</h4>
  <div class="form-group">
    <label for="input_password">New Password</label>
    <input type="password" class="form-control" name="password" id="input_password"/>
  </div>
  <div class="form-group">
    <label for="input_password2">Repeat New Password</label>
    <input type="password" class="form-control" name="password2"  id="input_password2"/>
  </div>
  <br/><input type="submit" class="btn btn-primary my-1" value="Change" name="change"/>
</form>
</div>
<?php
}
?>

<hr>
<footer>
<p class="text-center text-ltr engFont">&copy; <a href="https://irhosseinz.github.io/User-Manager/" target="_blank">UserManager</a></p>
</footer>
  </body>
</html>
<?php
$DB->close();
?>
