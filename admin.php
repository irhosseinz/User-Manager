<?php
include_once('includes/config.php');
include_once('includes/umf.php');
if(!isset($_SESSION['UM_DATA'])){
	header('Location: login.php');
	exit;
}
if(!$_SESSION['UM_DATA']['perm']['admin']){
	header('Location: user.php');
	exit;
}
$SUCCESS=false;
$ERROR=false;
if(isset($_GET['list'])){
	switch(@$_GET['g']){
		case 'search':
			if(isset($_GET['email'])){
				$st=$DB->prepare("select * from users where email like ?");
				$email="%{$_GET['email']}%";
				$st->bind_param('s',$email);
				$st->execute();
				$q=$st->get_result();
			}else if(isset($_GET['id'])){
				$q=$DB->query("select * from users where _id=".intval($_GET['id']));
			}
			break;
		default:
			$q=$DB->query("select * from users order by _id desc limit ".intval($_GET['list']).",10");
			break;
	}
	if(!$q){
		echo $DB->error;
	}
	$o=array();
	while($r=$q->fetch_assoc()){
		unset($r['password']);
		$r['perm']=decodeConfig($r['perm']);
		$r['ref_link']=UM_DOMAIN.sprintf(UM_REFERRAL_FORMAT,$r['_id']);
		$o[]=$r;
	}
	echo json_encode($o);
	$DB->close();
	exit;
}else if(isset($_POST['change'])){
	if($_SESSION['UM_DATA']['perm']['edit_password']){
		$password=UM_randomString(8);
		$DB->query("update users set password='".UM_PASSWORD($password)."' where _id=".intval($_POST['change']));
		$o=array('ok'=>true,'new'=>$password);
	}else{
		$o=array('ok'=>false,'error'=>'Permission denied!');
	}
	echo json_encode($o);
	$DB->close();
	exit;
}else if(isset($_POST['make_admin'])){
	if($_SESSION['UM_DATA']['perm']['edit_admin']){
		$perm=encodeConfig($_POST);
		$DB->query("update users set perm='{$perm}' where _id=".intval($_POST['make_admin']));
		$o=array('ok'=>true,'perm'=>decodeConfig($perm));
	}else{
		$o=array('ok'=>false,'error'=>'Permission denied!');
	}
	echo json_encode($o);
	$DB->close();
	exit;
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
    <title>Dashboard - Admin</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.css?v=1.00" rel="stylesheet">
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.js"></script>
	 <script src="/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/admin.js?v=1.32"></script>
</head>
<body>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modal_body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="modal_action" onclick="A.modal_click()">Ok</button>
      </div>
    </div>
  </div>
</div>
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
<?php
$DATA=$DB->query("select * from users where _id={$_SESSION['UM_DATA']['_id']}")->fetch_assoc();
?>
<div class="container alert alert-secondary border-green rounded p-3">
<table class="table table-striped table-hover">
	<thead>
		<tr>
		<th scope="col" class="align-middle"><input name="id" class="t-input search_users" placeholder="#"/></th>
		<th scope="col" class="align-middle"><input name="email" class="t-input search_users" placeholder="Email"/></th>
		<?php
		$fs=json_decode($UM_CONFIG['FIELDS'],true);
		$fs_ids=array();
		foreach($fs as $k=>$v){
			if($v['nget'])
				continue;
			$fs_ids[]=$k;
//			echo '<th scope="col">'.$v['name'].'</th>';
		}
		?>
		<th scope="col"></th>
		</tr>
	</thead>
	<tbody id="users">
	</tbody>
</table>
<nav>
  <ul class="pagination">
    <li class="page-item"><a class="page-link" onclick="A.prev()">Previous</a></li>
    <li class="page-item"><a class="page-link" onclick="A.next()">Next</a></li>
  </ul>
</nav>
</div>
          </div>
        </main>
      </div>
    </div>
    <script src="js/feather.min.js"></script>
    <script type="text/javascript">
		$( document ).ready(function(){
			A=new Admin(<?php echo $UM_CONFIG['FIELDS'].','.json_encode($_SESSION['UM_DATA']['perm']);?>);
			A.getUsers(0);
			$('[data-toggle="popover"]').popover();
			$('.search_users').keydown(function(event){
				A.search_change(event.target);
			});
			$('.search_users').keyup(function(event){
				A.search_users(event.target);
			});
		});
      feather.replace();
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
