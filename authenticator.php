<?php
include_once('includes/config.php');
include_once('includes/umf.php');
include_once('includes/authenticator.php');
if(!isset($_SESSION['UM_DATA'])){
	header('Location: login.php');
	exit;
}
$SUCCESS=false;
$ERROR=false;
if(isset($_POST['code'])){
    $ga = new PHPGangsta_GoogleAuthenticator();

    $ok=$ga->verifyCode($_POST['secret'], $_POST['code'], UM_AUTHENTICATOR_TOL);

    if($ok){
        $st=$DB->prepare("update users set authen_secret=? where _id=?");
        $st->bind_param('si',$_POST['secret'],$_SESSION['UM_DATA']['_id']);
        $st->execute();
        $SUCCESS="Setup Completed. Your Secret Is <b>{$_POST['secret']}</b>, Please write this down SomeWhere Safe";
    }else{
        $ERROR="Wrong Code!";
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/img/favicon.png">
    <title>2-Step Verification</title>
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
      <a class="navbar-brand col-sm-3 col-md-2 col-4 mr-0" href="#"><?php echo $UM_CONFIG['TITLE'];?></a>
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
          <a class="nav-link" href="login.php?logout">Sign out</a>
        </li>
      </ul>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <nav class="col-md-2 col-sm-1 col-1 d-inline d-md-block bg-light sidebar" style="top:48px">
          <div class="sidebar-sticky">
            <ul class="nav flex-column">
            <?php
            	include('includes/dashboard_sidebar.php');
            ?>
            </ul>
          </div>
        </nav>

        <main role="main" class="col-md-9 offset-md-2 col-sm-11 offset-sm-1 col-11 offset-1 pt-3 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
<div class="container">
<?php
if(@$ERROR){
	echo '<div class="alert alert-danger" role="alert">'.$ERROR.'</div>';
}else if(@$SUCCESS){
	echo '<div class="alert alert-success" role="alert">'.$SUCCESS.'</div>';
}

$DATA=$DB->query("select * from users where _id={$_SESSION['UM_DATA']['_id']}")->fetch_assoc();
if($DATA['authen_secret']){
    echo '<div class="alert alert-primary" role="alert">2FAuthentication is Activated</div>';
}else{
    $ga = new PHPGangsta_GoogleAuthenticator();
    $secret = $_POST['secret']?$_POST['secret']:$ga->createSecret();

    $qrCodeUrl = $ga->getQRCodeGoogleUrl($UM_CONFIG['TITLE']."({$DATA['email']})", $secret);
?>
<form id="regForm1" action="authenticator.php" method="post">
    <input type="hidden" value="<?php echo $secret;?>" name="secret"/>
    <p>
        <h4>Setup 2 Factor Verification</h4>
        <ul>
            <li>Get the Authenticator from The <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank">App store (IOS)</a> or <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Google Play (Android)</a></li>
            <li>After opening App Click on <b>Plus or Add button</b> And Then Select <b>Scan a QR Code</b></li>
            <li>Scan Below QRCode</li>
        <img src="<?php echo $qrCodeUrl;?>"/>
            <li>Enter Generated Code in Below Field</li>
        </ul>
    </p>
  <div class="form-group">
    <input type="text" class="form-control" name="code" placeholder="000000" autocomplete="off"/>
  </div>
  <br/><input type="submit" class="btn btn-primary my-1" value="Verify" name="verify"/>
</form>
<?php
}
?>
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
