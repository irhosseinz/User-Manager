<?php
include_once('../includes/config.php');
include_once('../includes/umf.php');

ini_set('display_errors',false);
if($DB->connect_errno){
	header('Location: index.php?error='.urlencode('Database connection Failed!!'));
	exit;
}

if($_POST){
	$c=intval($_POST['ids']);
	$fs=array();
	$table='';
	for($i=1;$i<=$c;$i++){
		$name=$_POST['field'.$i.'_name'];
		if(!$name)
			continue;
		$key="{$i}_".strtolower(preg_replace('%[^a-zA-Z]+%','',$name?$name:'field'));
		if(strlen($key)>10)
			$key=substr($key,0,10);
		$table.="`{$key}` varchar(50) NULL,";
		if(isset($_POST['field'.$i.'_unique'])){
			$table.="UNIQUE KEY `{$key}` (`{$key}`),";
		}
		$fs[]=array(
			'key'=>$key,
			'name'=>$name,
			'type'=>$_POST['field'.$i.'_type'],
			'param'=>$_POST['field'.$i.'_param'],
			'required'=>isset($_POST['field'.$i.'_required'])?true:false,
			'unique'=>isset($_POST['field'.$i.'_unique'])?true:false,
			'uneditable'=>isset($_POST['field'.$i.'_uneditable'])?true:false,
			'nget'=>isset($_POST['field'.$i.'_nget'])?true:false,
			'min'=>$_POST['field'.$i.'_min'],
			'max'=>$_POST['field'.$i.'_max'],
			'hint'=>$_POST['field'.$i.'_hint']
		);
	}
	$ok=$DB->multi_query("CREATE TABLE IF NOT EXISTS `users` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NULL,
  `email_temp` varchar(50) NOT NULL,
  `password` varchar(70) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  {$table}
  PRIMARY KEY (`_id`),
  UNIQUE KEY `email` (`email`),
  KEY `email_temp` (`email_temp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `login_log` (
  `_id` bigint(15) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expire` timestamp NULL,
  `secret` varchar(50) NOT NULL,
  PRIMARY KEY (`_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS `verify` (
  `_id` bigint(15) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `secret` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `action` enum('verify','forget') NOT NULL,
  `wrong` int(3) NOT NULL DEFAULT 0,
  `used` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB;");
	$rules=array();
	$msgs=array();
	$fields=array();
	foreach($fs as $v){
		if($v['hint'])
			$msgs[$v['key']]=$v['hint'];
		$fields[$v['key']]=array('name'=>$v['name'],'type'=>$v['type']
			,'unique'=>$v['unique'],'uneditable'=>$v['uneditable'],'nget'=>$v['nget']);
		$rules[$v['key']]=array();
		if($v['required'] || $v['uneditable'])
			$rules[$v['key']]['required']=true;
		if($v['unique']){
			$rules[$v['key']]['remote']='register.php?exist';
			$rules[$v['key']]['required']=true;
		}
		$min=intval($v['min']);
		if($min>0)
			$rules[$v['key']]['minlength']=$min;
		$max=intval($v['max']);
		if($max>0)
			$rules[$v['key']]['maxlength']=$max;
		
		switch($v['type']){
			case 'number':
				$rules[$v['key']]['number']=true;
				break;
			case 'date':
				$rules[$v['key']]['dateISO']=true;
				break;
			case 'url':
				$rules[$v['key']]['url']=true;
				break;
			case 'email':
				$rules[$v['key']]['email']=true;
				break;
			case 'select':
				$fields[$v['key']]['params']=explode(',',$v['param']);
				break;
			default:
				break;
		}
	}
	$data=file_get_contents('../includes/config.php');
	$data=preg_replace('%UM_DATA\*\/"MSGS"[\s\S]+\n%U','UM_DATA*/"MSGS"=>\''.json_encode($msgs).'\''."\n",$data);
	$data=preg_replace('%UM_DATA\*\/"RULES"[\s\S]+\n%U','UM_DATA*/"RULES"=>\''.json_encode($rules).'\''."\n",$data);
	$data=preg_replace('%UM_DATA\*\/"FIELDS"[\s\S]+\n%U','UM_DATA*/"FIELDS"=>\''.json_encode($fields).'\''."\n",$data);
	if($ok){
		$data=preg_replace('%UM_DATA\*\/"UM_PASSWORD_HASH"[^)]+\)%U','UM_DATA*/"UM_PASSWORD_HASH","'.UM_randomString(20).'")',$data);
	}
	if(file_put_contents('../includes/config.php',$data) && $ok){
		header('Location: finish.html');
		$DB->close();
		exit;
	}else{
		$ERROR="there was an error! please check includes/config.php file permission. (make it 777) ".$DB->error;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Install - Fields</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="@irhosseinz">
<meta content="fa" http-equiv="Content-Language" />
<link rel="icon" href="/img/favicon.png">

<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/usermanager.css" rel="stylesheet">
<script type="text/javascript" src="../js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.bundle.js"></script>
<script src="../js/jquery.validate.min.js"></script>

<script type="text/javascript">
$( document ).ready(function(){
	$(function () {
		$('[data-toggle="popover"]').popover()
	})
	$("#form").validate({
		focusInvalid: false,onkeyup: false,
		errorClass: "is-invalid",validClass: "is-valid",
		submitHandler: function(form) {
			form.submit();
		}
	});
	addField();
});
function filterInputs(value,id){
	switch(value){
		case 'date':
			$("#field"+id+"unique").show();
			$("#field"+id+"uneditable").show();
			$("#field"+id+"required").show();
			$("#field"+id+"min").hide();
			$("#field"+id+"max").hide();
			$("#field"+id+"hint").show();
			$("#field"+id+"_param").hide();
			break;
		case 'select':
			$("#field"+id+"unique").hide();
			$("#field"+id+"uneditable").show();
			$("#field"+id+"required").show();
			$("#field"+id+"min").hide();
			$("#field"+id+"max").hide();
			$("#field"+id+"hint").show();
			$("#field"+id+"_param").show();
			break;
		case 'checkbox':
			$("#field"+id+"unique").hide();
			$("#field"+id+"uneditable").show();
			$("#field"+id+"required").hide();
			$("#field"+id+"min").hide();
			$("#field"+id+"max").hide();
			$("#field"+id+"hint").show();
			$("#field"+id+"_param").hide();
			break;
		default:
			$("#field"+id+"unique").show();
			$("#field"+id+"uneditable").show();
			$("#field"+id+"required").show();
			$("#field"+id+"min").show();
			$("#field"+id+"max").show();
			$("#field"+id+"hint").show();
			$("#field"+id+"_param").hide();
			break;
	}
}
var field_id=0;
function addField(){
	field_id++;
	document.getElementById('ids').value=''+field_id;
	$('#fields').append('<div id="field'+field_id+'">'+
'	<div class="form-row align-items-center">'+
'		<div class="col-auto">'+
'		<label class="my-2" for="inlineFormInput">Name</label>'+
'		<input type="text" class="form-control mb-2" name="field'+field_id+'_name" placeholder="Field Name" required>'+
'		</div>'+
'		<div class="col-auto">'+
'		<label class="my-2" for="inlineFormInput">Format</label>'+
'		<select class="form-control mb-2" name="field'+field_id+'_type" required onchange="filterInputs(this.value,'+field_id+')">'+
'			<option value="text">Text</option>'+
'			<option value="number">Number</option>'+
'			<option value="date">Date (like 2019/01/01)</option>'+
'			<option value="url">Url</option>'+
'			<option value="email">Email</option>'+
'			<option value="select">Select options</option>'+
'			<option value="checkbox">Checkbox</option>'+
'		</select>'+
'		<input type="text" class="my-2 form-control" placeholder="option1,option2,.." id="field'+field_id+'_param" name="field'+field_id+'_param" style="display:none"/>'+
'		</div>'+
'		<div class="col-auto" id="field'+field_id+'nget">'+
'		<label class="my-2" for="inlineFormInput">Not in Registeration<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="check this if you don\'t want to get this data on registeration">?</a></label>'+
'		<input type="checkbox" class="form-control mb-2" name="field'+field_id+'_nget"/>'+
'		</div>'+
'		<div class="col-auto" id="field'+field_id+'unique">'+
'		<label class="my-2" for="inlineFormInput">Unique<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="If checked, system checks that another user is not registered with this exact value. (this fields mostly used for fields like username or etc)">?</a></label>'+
'		<input type="checkbox" class="form-control mb-2" name="field'+field_id+'_unique"/>'+
'		</div>'+
'		<div class="col-auto" id="field'+field_id+'uneditable">'+
'		<label class="my-2" for="inlineFormInput">uneditable<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="If checked, user can not edit this field after registeration">?</a></label>'+
'		<input type="checkbox" class="form-control mb-2" name="field'+field_id+'_uneditable" onchange="document.getElementById(\'field'+field_id+'_required\').disabled=this.checked"/>'+
'		</div>'+
'		<div class="col-auto" id="field'+field_id+'required">'+
'		<label class="my-2" for="inlineFormInput">Required<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="If checked, user is forced to enter some data on this field">?</a></label>'+
'		<input type="checkbox" class="form-control mb-2" name="field'+field_id+'_required" id="field'+field_id+'_required"/>'+
'		</div>'+
'		<div class="col-auto" id="field'+field_id+'min">'+
'		<label class="my-2" for="inlineFormInput">Min Length<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="minimum length of data, user can enter. (enter 0 to skip)">?</a></label>'+
'		<input type="number" class="form-control mb-2" name="field'+field_id+'_min"/>'+
'		</div>'+
'		<div class="col-auto" id="field'+field_id+'max">'+
'		<label class="my-2" for="inlineFormInput">Max Length<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="maximum length of data, user can enter. (enter 0 to skip)">?</a></label>'+
'		<input type="number" class="form-control mb-2" name="field'+field_id+'_max"/>'+
'		</div>'+
'		<div class="col-auto" id="field'+field_id+'hint">'+
'		<label class="my-2" for="inlineFormInput">Data Hint<a tabindex="-2" class="badge badge-info" role="button" data-toggle="popover" data-trigger="focus" title="Hint" data-content="you can enter a text here, it will be shown to him/her as a hint while entering data">?</a></label>'+
'		<input type="text" class="form-control mb-2" name="field'+field_id+'_hint"/>'+
'		</div>'+
'	</div>'+
'	<h4><span class="badge badge-danger" onclick="$(\'#field'+field_id+'\').remove()">REMOVE</span></h4>'+
'	</div>'+
'	<hr/>');
	$('[data-toggle="popover"]').popover();
}
</script>

</head>
<body>

<nav aria-label="breadcrumb">
	<a class="navbar-brand">Installing.. (2/3)</a>
  <ol class="breadcrumb">
    <li class="breadcrumb-item text-secondary">Database</li>
    <li class="breadcrumb-item text-success">Fields</li>
    <li class="breadcrumb-item text-secondary">Finish</li>
  </ol>
</nav>


<div class="container">
<form id="form" method="post" action="fields.php">
<input type="hidden" name="ids" id="ids" value="1">
<div id="fields">
</div>

  <h3><button class="badge badge-info" onclick="addField()">Add a Field</button></h3>
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
<?php
$DB->close();
?>
