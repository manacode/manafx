$( document ).ready(function() {
	var $row;
/*************************************************
	Roles Action
*************************************************/
  $('.btnAdd', '.manafx-roles-index').button().click(function (e) {
  	e.preventDefault();
  	$('#purpose').val("add");
  	$('#role_id').val("");
    $('#role_name').val("");
    $('#role_description').val("");
    $('#myModalLabel', '.add-edit-modal').html("Add New Role");
    $('#btnSave', '.add-edit-modal').text("Submit");
		$('.add-edit-modal').modal({
		  backdrop: false,
			show: true,
		});
		$('.add-edit-modal').on('shown.bs.modal', function () {
			$('#role_name').focus();
		});
	});
	
  $('.btnEdit', '.manafx-roles-index').button().click(function (e) {
  	e.preventDefault();
		$row = $(this).parents('tr:first');
  	
  	$('#purpose').val("edit");
    $('#role_name').val("");
    $('input[name=role_status]').prop("checked", false);
    $('#role_description').val("");
    $('#myModalLabel', '.add-edit-modal').html("Edit Role");
    $('#btnSave', '.add-edit-modal').text("Save Changes");

		role_id = $(this).attr("data-role_id");
		if (!role_id) {
			checkedId = $('.check-rows:checked', '.datagrid');
			role_id = checkedId.val();
			$row = checkedId.parents('tr:first');
		}
		
    var request = $.ajax({
      url: "/roles/getRole",
      type: "POST",
			data: { role_id: role_id },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
        $("#addeditMsg").removeClass("hide");
        $("#addeditMsg").html(aret['msg']);
      } else {
        $("#addeditMsg").addClass("hide");
        $("#addeditMsg").html("");
        datane = aret['data'];
        $('#role_id').val(role_id);
        $('#role_name').val(datane["role_name"]);
        $('#role_status_'+datane["role_status"].toLowerCase()).prop("checked",true);
        $('#role_description').val(datane["role_description"]);
        
				$('.add-edit-modal').modal({
				  backdrop: false,
  				show: true
				})
				$('.add-edit-modal').on('shown.bs.modal', function () {
//        $('#role_status'+datane["role_status"].toLowerCase()).prop("checked",true);
				    $('#role_name').focus();
				})
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#addeditMsg").removeClass("hide");
      $("#addeditMsg").html("Internal server error, Please try again?");
			// $("#addeditMsg").html(jqXHR.responseText);
    });
  });
  
  $('#btnSave', '.add-edit-modal').button().click(function (e) {
  	e.preventDefault();
  	purpose = $('#purpose').val();
  	role_id = $('#role_id').val();
  	role_name = $('#role_name').val();
  	role_status = $('input[name=role_status]:checked').val();
  	role_description = $('#role_description').val();
  	if (purpose=="edit") {
  		reqUri = "/roles/updateRole";
 		} else {
 			reqUri = "/roles/createRole";
		}
    var request = $.ajax({
      url: reqUri,
      type: "POST",
			data: {purpose: purpose, role_id: role_id, role_name: role_name, role_status: role_status, role_description: role_description },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
    	$msg = aret['msg'];
      if (aret['status']!=="1") {
      	$("#addeditMsg").removeClass("alert-success");
        $("#addeditMsg").removeClass("hide");
        $("#addeditMsg").addClass("alert-danger");
        $("#addeditMsg").html($msg);
		    $('#role_name').select();
      } else {
        $("#addeditMsg").addClass("hide");
        $("#addeditMsg").html("");

        role_status_text = "Active";
        if (role_status=="R") {
        	role_status_text = "Reserved";
      	}
        if (role_status=="X") {
        	role_status_text = "Disabled";
      	}
        if (purpose=="edit") {
	        $('td:eq(2)', $row).text(role_name);
        	$('td:eq(3)', $row).text(role_status_text);
        	$('td:eq(4)', $row).text(role_description);
        	$('.add-edit-modal').modal('hide');
        	sorotMe($row,"success");
       	} else {
       		role_id = aret['role_id'];
       		appendRow(role_id, role_name, role_status_text, role_description);
       		$("#addeditMsg").removeClass("alert-danger");
	        $("#addeditMsg").removeClass("hide");
	        $("#addeditMsg").addClass("alert-success");
	        $("#addeditMsg").html($msg);
			  	$('#role_id').val("");
			    $('#role_name').val("");
			    $('#role_status').val("");
			    $('#role_description').val("");
			    $('#role_name').focus();
        }
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#addeditMsg").removeClass("hide");
      $("#addeditMsg").html("Internal server error, Please try again?");
//      $("#addeditMsg").html(jqXHR.responseText);
    });  
  });

	$(".btnDelete", '.manafx-roles-index').button().click(function() {
  	$(".modal-body", "#deleteRoles").removeClass("hidden");
  	$("#btnDeleteNow", "#deleteRoles").removeClass("hidden");
    $("#deleteMsg", "#deleteRoles").addClass("hidden");
    $("#deleteMsg", "#deleteRoles").html("");
		role_id = $(this).attr("data-role_id");
		if (!role_id) {
			var searchRoles = $('.check-rows:checked', '.datagrid').map(function(){
				trnya = $(this).parents('tr:first');
				id = $(this).val();
				rolename = $('.btnDelete', trnya).attr('data-rolename');
			  return {id: id, rolename: rolename};
			}).get();
			// console.debug(searchRoles);
			var role_id = [];
			var rolename2delete = "";
			for(i=0; i<searchRoles.length; i++) {
				role_id[i] = searchRoles[i]['id'];
				rolename2delete += '<span class="label label-danger">' + searchRoles[i]['rolename'] + '</span> ';
			}
			
			$('#rolename2delete', '#confirmDeleteMany').html(rolename2delete);
			$('#confirmDeleteOne').addClass("hidden");
			$('#confirmDeleteMany').removeClass("hidden");
			$('#role_id', '#deleteRoles').val(role_id);
		} else {
			$('#rolename2delete', '#confirmDeleteOne').text($(this).attr("data-rolename"));
			$('#confirmDeleteOne').removeClass("hidden");
			$('#confirmDeleteMany').addClass("hidden");
			$('#role_id', '#deleteRoles').val(role_id);
		}
		$('#deleteRoles').modal("show");
	});

	$("#btnDeleteNow", '#deleteRoles').button().click(function(e) {
		e.preventDefault();
		role_id = $('#role_id', '#deleteRoles').val();
		
    var request = $.ajax({
      url: "/roles/deleteRoles",
      type: "POST",
			data: { role_id: role_id },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
      	$(".modal-body", "#deleteRoles").addClass("hidden")
      	$("#btnDeleteNow", "#deleteRoles").addClass("hidden")
        $("#deleteMsg", '#deleteRoles').removeClass("hidden");
        $("#deleteMsg", '#deleteRoles').html(aret['msg']);
      } else {
        $("#deleteMsg", '#deleteRoles').addClass("hidden");
        $("#deleteMsg", '#deleteRoles').html("");
        removeRows(role_id);
        $('#role_id', '#deleteRoles').val("");
        $('#deleteRoles').modal("hide");
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#deleteMsg").removeClass("hide");
      $("#deleteMsg").html("Internal server error, Please try again?");
			// $("#deleteMsg").html(jqXHR.responseText);
    });
	});
	
/*
	$('#hide-me').change(function() {
		if ($(this).is(':checked')) {
			$('tr.default-manafx-role', '.datagrid').css("display", "none");
		} else {
			$('tr.default-manafx-role', '.datagrid').css("display", "table-row");
		}
	});

	var hideDefault = $('#hide-me').is(':checked');
	if (hideDefault) {
		$('tr.default-manafx-role', '.datagrid').css("display", "none");
	} else {
		$('tr.default-manafx-role', '.datagrid').css("display", "table-row");
	}
*/
});

function appendRow(role_id, role_name, role_status, role_description) {
	//get the footable object
	var footable = $('table').data('footable');
	
	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-id="' + role_id + '">';
	newRow += '<td class="col4checkbox"><input disabled="disabled" class="check-rows" name="checked_id[]" type="checkbox" value="' + role_id + '" /></td>';
	newRow += '<td>' + role_id + '</td>';
	newRow += '<td>' + role_name + '</td>';
	newRow += '<td>' + role_status + '</td>';
	newRow += '<td>' + role_description + '</td>';
	newRow += '<td><button disabled="disabled" class="btnEdit btn btn-primary btn-xs" data-role_id="' + role_id + '" title="Edit `' + role_name + '`"><span class="glyphicon glyphicon-edit"></span></button></td>';
	newRow += '<td><button disabled="disabled" class="btnDelete btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal" data-role_id="' + role_id + '" title="Delete `' + role_name + '`"><span class="glyphicon glyphicon-remove"></span></button></td>';
	newRow += '</tr>';
	
	//add it
	footable.appendRow(newRow);
	$('a[data-page="last"]').click();
	var el = $('tbody tr:last', 'table.footable');
	var rowpos = el.offset();
	$(window).scrollTop(rowpos.top);	
}