var A;
function Admin(fs,perm){
	this.fs=fs;
	this.perm=perm;
}
Admin.prototype.prev = function() {
	this.getUsers(this.list_prev);
}
Admin.prototype.next = function() {
	this.getUsers(this.list_next);
}
Admin.prototype.modal_click = function() {
	$('#modal_action').attr("disabled", true);
	var self=this;
	switch(this.modal[0]){
		case 'password':
			$.post('admin.php','change='+this.modal[1],function(data){
				if(data.ok){
					$('#modal_body').text('Password is Changed to: '+data.new);
				}else{
					$('#modal_body').text('Error: '+data.error);
				}
			},'json');
			break;
		case 'admin':
			$.post('admin.php','make_admin='+this.modal[1]+'&'+$('#edit_form').serialize(),function(data){
				if(data.ok){
					$('#modal_body').text('Profile Edited!');
					self.user_data[self.modal[2]].perm=data.perm;
				}else{
					$('#modal_body').text('Error: '+data.error);
				}
			},'json');
			break;
		default:
			break;
	}
}
Admin.prototype.view = function(i) {
	var u=this.user_data[i];
	$('#modal_action').attr("disabled", true);
	$('#modal_title').text('Viewing User #'+u._id);
	var html='<p><b>Email:</b>'+(u.email?u.email+" (verified)":u.email_temp+" (unverified)")+"<br/>";
	html+='<b>Registered On:</b>'+u.reg_date+"<br/>";
	html+='<b>Referred by:</b>'+u.ref+"<br/>";
	html+='<b>Referral link:</b><a href="'+u.ref_link+'" target="_blank">'+u.ref_link+"</a><br/>";
	for(var i in this.fs){
		html+='<b>'+this.fs[i].name+':</b>'+u[i]+"<br/>";
	}
	html+='</p>';
	$('#modal_body').html(html);
	$('#modal').modal()
}
Admin.prototype.password = function(i) {
	var u=this.user_data[i];
	this.modal=['password',u._id];
	$('#modal_action').attr("disabled", false);
	$('#modal_title').text('Editing User #'+u._id);
	$('#modal_body').text('Password for this account would be reseted. Do you want to continue?');
	$('#modal').modal()
}
Admin.prototype.admin = function(i) {
	var u=this.user_data[i];
	$('#modal_action').attr("disabled", false);
	this.modal=['admin',u._id,i];
	$('#modal_title').text('Making User #'+u._id+" Admin");
	var html='<form id="edit_form">';
	for(var i in u.perm){
		html+='<div class="form-group"><div class="form-check"><input class="form-check-input" type="checkbox" name="'+i+'"'+(u.perm[i]?' checked=""':'')+'><label class="form-check-label">'+i+'</label></div></div>';
	}
	html+='</form>';
	$('#modal_body').html(html);
	$('#modal').modal()
}
Admin.prototype.show_users = function(data) {
	$('#users').html('');
	var self=this;
	for(var j in data){
		var html='<tr class="'+(data[j].perm.admin?'table-success':'')+'"><th scope="row" class="align-middle">'+data[j]._id+'</th>';
		html+='<td class="align-middle">'+(data[j].email?data[j].email+'<span class="badge badge-secondary">VERIFIED</span>':data[j].email_temp)+'</td>';
//			for(var k in self.fs){
//				if(self.fs[k].nget)
//					continue;
//				html+='<td class="align-middle">'+data[j][k]+'</td>';
//			}
		html+='<td class="align-middle"><a class="btn btn-primary m-1" onclick="A.view('+j+')"><span data-feather="eye" color="#ffffff" stroke-width="3"></span></a>';
		if(self.perm.edit_admin){
			html+='<a class="btn btn-info m-1" onclick="A.admin('+j+')"><span data-feather="unlock" color="#ffffff" stroke-width="3"></span></a>';
		}
		if(self.perm.edit_password){
			html+='<a class="btn btn-warning m-1" onclick="A.password('+j+')"><span data-feather="key" color="#ffffff" stroke-width="3"></span></a>';
		}
		html+='</td></tr>';
		$('#users').append(html);
	}
	feather.replace();
}
Admin.prototype.getUsers = function(i) {
	var self=this;
	if(i===this.list_current){
		return;
	}
	$.get('admin.php?list='+i,function(data){
		if(data.length==0){
			return;
		}
		self.user_data=data;
		if(i==0){
			self.list_next=i+data.length;
			self.list_prev=0;
			self.list_current=i;
		}else if(i>self.list_current){
			self.list_next=i+data.length;
			self.list_prev=self.list_current;
			self.list_current=i;
		}else{
			self.list_prev=i-data.length;
			if(self.list_prev<0)
				self.list_prev=0;
			self.list_next=self.list_current;
			self.list_current=i;
		}
		self.show_users(data);
	},'json');
}
Admin.prototype.search_change = function() {
	this.search_changed=true;
}
Admin.prototype.search_users = function(input) {
	var self=this;
	this.search_changed=false;
	this.search_i=input;
	if(input.value.length<3 || input.value===this.search_last){
		return;
	}
	this.search_last=input.value;
	setTimeout(function(){
		if(self.search_changed){
			return;
		}
		$.get('admin.php?list&g=search&'+input.getAttribute('name')+'='+input.value,function(data){
			self.show_users(data);
		},'json');
	},1000);
}
