$( document ).ready(function() {
	var $row;

/*************************************************
	Users Action
*************************************************/
  $('.btnAdd', '.manafx-users-search').button().click(function (e) {
  	e.preventDefault();
  	$('#purpose').val("add");
		resetForm();
    $('#myModalLabel', '.add-edit-modal').html("Add New User");
    $('#btnSave', '.add-edit-modal').text("Submit");
	  $("#addeditMsg").addClass("hidden");
	  $("#addeditMsg").html("");
		$('.add-edit-modal').modal({
		  backdrop: false,
			show: true
		})
		$('.add-edit-modal').on('shown.bs.modal', function () {
		    $('#user_firstname').focus();
		})  	
	});
	
  $('.btnEdit', '.manafx-users-search').button().click(function (e) {
  	e.preventDefault();
		$row = $(this).parents('tr:first');
  	$('#purpose').val("edit");
		resetForm();
    $('#myModalLabel', '.add-edit-modal').html("Edit User");
    $('#btnSave', '.add-edit-modal').text("Save Changes");

		user_id = $(this).attr("data-user_id");
		if (!user_id) {
			checkedId = $('.check-rows:checked', '.datagrid');
			user_id = checkedId.val();
			$row = checkedId.parents('tr:first');
		}

		var request = $.ajax({
      url: "/users/getUser",
      type: "POST",
			data: { user_id: user_id },
      dataType: "text"
    });
		request.done(function(ret) {
     	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
        $("#addeditMsg").removeClass("hidden");
        $("#addeditMsg").html(aret['msg']);
      } else {
        $("#addeditMsg").addClass("hidden");
        $("#addeditMsg").html("");
        datane = aret['data'];
        $('#user_id').val(user_id);
		    $('#user_firstname').val(datane["user_firstname"]);
		    $('#user_lastname').val(datane["user_lastname"]);
		    $('#user_email').val(datane["user_email"]);
		    $('#user_username').val(datane["user_username"]);
		    $('#user_roles option').prop("selected", false);
		    $('#user_roles option').removeAttr("selected");
		    $('#user_roles').val([]);
		    var user_roles = datane["user_roles"]
		    for(i=0; i<user_roles.length; i++) {
		    	role_id = user_roles[i];
		    	$('#user_roles option[value="' + role_id +  '"]').prop("selected", true);
	    	}
				$('#user_status').val(datane["user_status"]);
				$('.add-edit-modal').modal({
				  backdrop: false,
  				show: true
				})
				$('.add-edit-modal').on('shown.bs.modal', function () {
//        $('#role_status'+datane["role_status"].toLowerCase()).prop("checked",true);
					$('#user_firstname').focus();
				})
      }
    });
    request.fail(function(jqXHR, textStatus) {
			// alert(jqXHR.responseText);
      $("#addeditMsg").removeClass("hidden");
      $("#addeditMsg").html("Internal server error, Please try again?");
    });
  });
  
  $('#btnSave', '.add-edit-modal').button().click(function (e) {
  	e.preventDefault();
  	purpose = $('#purpose').val();
  	user_id = $('#user_id').val();
    user_firstname = $('#user_firstname').val();
    user_lastname = $('#user_lastname').val();
    user_email = $('#user_email').val();
    user_username = $('#user_username').val();
    user_password = $('#user_password').val();
    confirm_pass = $('#confirm_pass').val();
    user_roles = $('#user_roles').val();
    user_status = $('#user_status').val();
  	if (purpose=="edit") {
  		reqUri = "/users/updateUser";
 		} else {
 			reqUri = "/users/createUser";
		}
    var request = $.ajax({
      url: reqUri,
      type: "POST",
			data: {purpose: purpose, user_id: user_id, user_firstname: user_firstname, user_lastname: user_lastname, user_email: user_email, user_username: user_username, user_password: user_password, confirm_pass: confirm_pass, user_roles: user_roles, user_status: user_status },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
    	$msg = aret['msg'];
      if (aret['status']!=="1") {
      	$("#addeditMsg").removeClass("alert-success");
        $("#addeditMsg").removeClass("hidden");
        $("#addeditMsg").addClass("alert-danger");
        $("#addeditMsg").html($msg);
		    $('#user_firstname').select();
      } else {
        $("#addeditMsg").addClass("hidden");
        $("#addeditMsg").html("");

        if (purpose=="edit") {
	        $('td:eq(2)', $row).text(user_username);
        	$('td:eq(3)', $row).text(user_firstname);
        	$('td:eq(4)', $row).text(user_lastname);
        	$('td:eq(5)', $row).text(user_email);
        	
        	$('td:eq(6)', $row).html();
					newRoleHtml = '<div class="barrier"><ul>';
						for(i=0; i<user_roles.length; i++) {
							newRoleHtml += '<li>';
							newRoleHtml += $('#user_roles option[value="' + user_roles[i] + '"]').html();
							newRoleHtml += '</li>';
						}
					newRoleHtml += '</ul></div>';
        	$('td:eq(6)', $row).html(newRoleHtml);
        	
        	$('td:eq(7)', $row).html(getHtmlUserStatus(user_status));
        	$('.add-edit-modal').modal('hide');
        	sorotMe($row,"success");
       	} else {
       		user_id = aret['user_id'];
       		appendRow(user_id, user_username, user_firstname, user_lastname, user_email, user_roles, user_status);
       		$("#addeditMsg").removeClass("alert-danger");
	        $("#addeditMsg").removeClass("hidden");
	        $("#addeditMsg").addClass("alert-success");
	        $("#addeditMsg").html($msg);
	        resetForm();
			    $('#user_firstname').focus();
        }
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#addeditMsg").removeClass("hidden");
      $("#addeditMsg").html(jqXHR.responseText + " Internal server error, Please try again?");
//      $("#addeditMsg").html(jqXHR.responseText);
    });  
  });

	$(".btnDelete", '.manafx-users-search').button().click(function() {
  	$(".modal-body", "#deleteUsers").removeClass("hidden");
  	$("#btnDeleteNow", "#deleteUsers").removeClass("hidden");
    $("#deleteMsg", "#deleteUsers").addClass("hidden");
    $("#deleteMsg", "#deleteUsers").html("");
		user_id = $(this).attr("data-user_id");
		if (!user_id) {
			var searchUsers = $('.check-rows:checked', '.datagrid').map(function(){
				trnya = $(this).parents('tr:first');
				id = $(this).val();
				username = $('.btnDelete', trnya).attr('data-username');
			  return {id: id, username: username};
			}).get();
			// console.debug(searchUsers);
			var user_id = [];
			var username2delete = "";
			for(i=0; i<searchUsers.length; i++) {
				user_id[i] = searchUsers[i]['id'];
				username2delete += '<span class="label label-danger">' + searchUsers[i]['username'] + '</span> ';
			}
			
			$('#username2delete', '#confirmDeleteMany').html(username2delete);
			$('#confirmDeleteOne').addClass("hidden");
			$('#confirmDeleteMany').removeClass("hidden");
			$('#user_id', '#deleteUsers').val(user_id);
		} else {
			$('#username2delete', '#confirmDeleteOne').text($(this).attr("data-username"));
			$('#confirmDeleteOne').removeClass("hidden");
			$('#confirmDeleteMany').addClass("hidden");
			$('#user_id', '#deleteUsers').val(user_id);
		}
		$('#deleteUsers').modal("show");
	});

	$("#btnDeleteNow", '#deleteUsers').button().click(function(e) {
		e.preventDefault();
		user_id = $('#user_id', '#deleteUsers').val();
		
    var request = $.ajax({
      url: "/users/deleteUsers",
      type: "POST",
			data: { user_id: user_id },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
      	$(".modal-body", "#deleteUsers").addClass("hidden")
      	$("#btnDeleteNow", "#deleteUsers").addClass("hidden")
        $("#deleteMsg", '#deleteUsers').removeClass("hidden");
        $("#deleteMsg", '#deleteUsers').html(aret['msg']);
      } else {
        $("#deleteMsg", '#deleteUsers').addClass("hidden");
        $("#deleteMsg", '#deleteUsers').html("");
        removeRows(user_id);
        $('#user_id', '#deleteUsers').val("");
        $('#deleteUsers').modal("hide");
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#deleteMsg", '#deleteUsers').removeClass("hidden");
      $("#deleteMsg", '#deleteUsers').html("Internal server error, Please try again?!");
			// $("#deleteMsg", '#deleteUsers').html(jqXHR.responseText);
    });
	});

	// Password Strength
  $("#user_password").keyup(function () {
    var pwvalue = $(this).val();
    var pwstrength = getPasswordStrength(pwvalue);
    $("#pwstrength").removeClass();
    $("#pwstrength").addClass("progress-bar");
    $("#pwstrength").attr("aria-valuenow", pwstrength);
    $("#pwstrength").css("width", pwstrength+"%");
    if (pwstrength>85) {
        $("#pwstrength").html("Very Strong");
        $("#pwstrength").addClass("progress-bar-success");
    }
    if (pwstrength>70 && pwstrength<=85) {
        $("#pwstrength").html("Strong");
        $("#pwstrength").addClass("progress-bar-info");
    }
    if (pwstrength>50 && pwstrength<=70) {
        $("#pwstrength").html("Moderate");
        $("#pwstrength").addClass("progress-bar-warning");
    }
    if (pwstrength<=50) {
        $("#pwstrength").html("Weak");
        $("#pwstrength").addClass("progress-bar-danger");
    }
    checkPasswordConfirm();
  });
  
  // Password Confirmation
  $("#confirm_pass").keyup(function () {
		checkPasswordConfirm();
	});

	$('span', '#confirmation').click(function(e) {
		$('#confirm_pass').select();
	});

});

function appendRow(user_id, user_username, user_firstname, user_lastname, user_email, user_roles, user_status) {
	//get the footable object
	var footable = $('table').data('footable');
	
	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-id="' + user_id + '">';
	newRow += '<td class="col4checkbox"><input disabled="disabled" class="check-rows" name="checked_id[]" type="checkbox" value="' + user_id + '" /></td>';
	newRow += '<td>' + user_id + '</td>';
	newRow += '<td>' + user_username + '</td>';
	newRow += '<td>' + user_firstname + '</td>';
	newRow += '<td>' + user_lastname + '</td>';
	newRow += '<td>' + user_email + '</td>';
	newRow += '<td><div class="barrier"><ul>';
		for(i=0; i<user_roles.length; i++) {
			newRow += '<li>';
			newRow += $('#user_roles option[value="' + user_roles[i] + '"]').html();
			newRow += '</li>';
		}
	newRow += '</ul></div></td>';

	newRow += '<td class="text-center">';
	newRow += getHtmlUserStatus(user_status);
	newRow += '</td>';
	newRow += '<td><button disabled="disabled" class="btnEdit btn btn-primary btn-xs" data-user_id="' + user_id + '" title="Edit `' + user_username + '`"><span class="glyphicon glyphicon-edit"></span></button></td>';
	newRow += '<td><button disabled="disabled" class="btnDelete btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal" data-user_id="' + user_id + '" title="Delete `' + user_username + '`"><span class="glyphicon glyphicon-remove"></span></button></td>';
	newRow += '</tr>';
	
	//add it
	footable.appendRow(newRow);
	$('a[data-page="last"]').click();
	var el = $('tbody tr:last', 'table.footable');
	var rowpos = el.offset();
	$(window).scrollTop(rowpos.top);	
}