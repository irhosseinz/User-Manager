<?php
include_once('includes/config.php');
include_once('includes/umf.php');
if(!isset($_SESSION['UM_DATA'])){
	header('Location: login.php');
	exit;
}
$SUCCESS=false;
$ERROR=false;


?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/img/favicon.png">
    <title>Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.js"></script>
	 <script src="/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$( document ).ready(function(){
	
});
</script>
  </head>

  <body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#"><?php echo $UM_CONFIG['TITLE'];?></a>
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
          <a class="nav-link" href="login.php?logout">Sign out</a>
        </li>
      </ul>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar" style="top:48px">
          <div class="sidebar-sticky">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link" href="user.php">
                  <span data-feather="user"></span>
                  Profile
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="#">
                  <span data-feather="file"></span>
                  Example <span class="sr-only">(current)</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="shopping-cart"></span>
                  Products
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="users"></span>
                  Customers
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="bar-chart-2"></span>
                  Reports
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="layers"></span>
                  Integrations
                </a>
              </li>
            </ul>
          </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
<?php
$DATA=$DB->query("select * from users where _id={$_SESSION['UM_DATA']['_id']}")->fetch_assoc();
?>
<div class="container">
<?php
if(@$ERROR){
	echo '<div class="alert alert-danger" role="alert">'.$ERROR.'</div>';
}else if(@$SUCCESS){
	echo '<div class="alert alert-success" role="alert">'.$SUCCESS.'</div>';
}
?>
<form id="regForm" action="user.php" method="post">
  <div class="form-group">
    <label for="input_email">Email address</label>
    <?php
    if($DATA['email']){
    	echo '<input type="text" disabled class="form-control" id="input_email" value="'.$DATA['email'].'"/><span class="badge badge-secondary">VERIFIED</span>';
    }else{
    	echo '<input type="email" required name="email" class="form-control" id="input_email" value="'.$DATA['email_temp'].'"/><span class="badge badge-danger">NOT VERIFIED</span>';
    }
    ?>
  </div>
  <?php
		if(!$DATA['email']){
			echo '<br/><input type="submit" class="btn btn-primary my-1" value="Verify" name="verify"/>';
		}
  ?>
</form>
<form id="regForm1" action="user.php" method="post">
  <hr/><h4>Change Password:</h4>
  <div class="form-group">
    <label for="input_password">Old Password</label>
    <input type="password" class="form-control" name="password0" id="input_password0"/>
  </div>
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
<form id="regForm2" action="user.php" method="post">
  <hr/>
  
  <?php
	$fs=json_decode($UM_CONFIG['FIELDS'],true);
	$msgs=json_decode($UM_CONFIG['MSGS'],true);
	if(sizeof($fs)>0){
		echo '<h4>Profile Data:</h4>';
	foreach($fs as $k=>$v){
		echo '<div class="form-group">';
		if($v['uneditable']){
			echo '<label for="input_'.$k.'">'.$v['name'].'</label><input disabled type="text" class="form-control" name="'.$k.'" id="input_'.$k.'" value="'.$DATA[$k].'"/>';
			continue;
		}
		$hint0=(isset($msgs[$k])?$msgs[$k]:'');
		$hint=(isset($msgs[$k])?'<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="'.$msgs[$k].'">?</a>':'');
		switch($v['type']){
			case 'email':
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<input type="email" class="form-control" name="'.$k.'" id="input_'.$k.'" value="'.$DATA[$k].'" placeholder="'.$hint0.'"/>';
				break;
			case 'number':
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<input type="number" class="form-control" name="'.$k.'" id="input_'.$k.'" value="'.$DATA[$k].'" placeholder="'.$hint0.'"/>';
				break;
			case 'checkbox':
				echo '<div class="form-check"><input type="checkbox" class="form-check-input" name="'.$k.'" id="input_'.$k.'" '.($DATA[$k]?'checked=""':'').'/><label for="input_'.$k.'" class="form-check-label">'.$v['name'].$hint.'</label>'.$hint.'</div>';
				break;
			case 'select':
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<select class="form-control" name="'.$k.'" id="input_'.$k.'">';
				foreach($v['params'] as $p){
					if(!$p)
						continue;
					echo '<option value="'.$p.'" '.($DATA[$k]==$p?'selected=""':'').'>'.$p.'</option>';
				}
				echo '</select>'.$hint;
				break;
			default:
				echo '<label for="input_'.$k.'">'.$v['name'].'</label>'.$hint.'<input type="text" class="form-control" name="'.$k.'" id="input_'.$k.'" value="'.$DATA[$k].'"/>';
				break;
		}
		echo "</div>\n";
	}
		echo '<br/><input type="submit" class="btn btn-primary my-1" value="Save" name="save"/>';
	}
  ?>
</form>
</div>
          </div>
        </main>
      </div>
    </div>
    <script src="js/feather.min.js"></script>
    <script>
      feather.replace()
    </script>
<hr>
<footer>
<p class="text-center text-ltr engFont">&copy; <a href="http://h.sandbad.biz/User-Manager/" target="_blank">UserManager</a></p>
</footer>
  </body>
</html>
<?php
$DB->close();
?>
