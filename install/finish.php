<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Installed</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="@irhosseinz">
<meta content="fa" http-equiv="Content-Language" />
<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/usermanager.css" rel="stylesheet">
<link rel="icon" href="/img/favicon.png">
</head>
<body>

<nav aria-label="breadcrumb">
	<a class="navbar-brand">Installing.. (3/3)</a>
  <ol class="breadcrumb">
    <li class="breadcrumb-item text-secondary">Database</li>
    <li class="breadcrumb-item text-secondary">Fields</li>
    <li class="breadcrumb-item text-success">Finish</li>
  </ol>
</nav>


<div class="container">
<h1 class="text-success">Installation is complete. </h1><h3><br/>What's next:<ul>
	<?php if(isset($_SESSION['UM_ADMIN']))echo '<li class="text-info">You can login as Administrator using: <ul><li><b>Email:</b> '.$_SESSION['UM_ADMIN'][0].'</li><li><b>Password:</b> '.$_SESSION['UM_ADMIN'][1].'</li></ul></li>';?>
	<li class="text-danger">you should delete "install" folder now!</li>
	<li>Please take a look at includes/config.php file, and make required changes there!</li>
	</ul><br/><a href="/">CONTINUE</a></h3>
</div>


<hr>
<footer>
<p class="text-center text-ltr engFont">&copy; <a href="http://h.sandbad.biz/User-Manager/" target="_blank">UserManager</a></p>
</footer>
  </body>
</html>
