<?php
include_once('../includes/umf.php');

ini_set('display_errors',false);
if(isset($_GET['error'])){
	$ERROR=$_GET['error'];
}

if($_POST){
	$db="define('SQL_HOST','{$_POST['host']}');\ndefine('SQL_DB','{$_POST['name']}');\ndefine('SQL_USER','{$_POST['user']}');\ndefine('SQL_PASS','{$_POST['password']}');";
	$data=file_get_contents('../includes/config.php');
	$data=preg_replace('%UM_DB_CONFIG\*\/[\s\S]+\/\*UM_DB_CONFIG%',"UM_DB_CONFIG*/\n{$db}\n/*UM_DB_CONFIG",$data);
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
<p>Welcome to UserManager installer. please insert your database info:</p>
<form id="form" method="post" action="index.php">
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
