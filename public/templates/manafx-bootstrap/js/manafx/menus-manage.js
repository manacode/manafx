$( document ).ready(function() {
	var $row;
  $('.btnAdd').button().click(function (e) {
  	e.preventDefault();
  	$('#purpose').val("add");
  	$('#menu_id').val("");
  	refreshBSOption($('#menu_parent'), $('#menu_parent').val());
    $('#menu_title').val("");
    $('#menu_key').val("");
    $('#menu_description').val("");
    $('#myModalLabel', '.add-edit-modal').html($('#myModalLabel', '.add-edit-modal').attr("data-title_create"));
		$('.add-edit-modal').modal({
		  backdrop: false,
			show: true,
		});
		$('.add-edit-modal').on('shown.bs.modal', function () {
			$('#menu_title').focus();
		});
	});
	
	$('#menu_title').keyup(function() {
	  $('#menu_key').val(toSlug($(this).val()));
	});

  $('.btnEdit').button().click(function (e) {
  	e.preventDefault();
		$row = $(this).parents('tr:first');
  	$('#purpose').val("edit");
    refreshBSOption($('#menu_parent'));
    $('#menu_title').val("");
    $('#menu_key').val("");
    $('#menu_roles').val("");
    $('input[name=menu_status]').prop("checked", false);
    $('#menu_description').val("");
    $('#myModalLabel', '.add-edit-modal').html($('#myModalLabel', '.add-edit-modal').attr("data-title_edit"));

		if ($row.index()==-1) {
			checkedId = $('.check-rows:checked', '.datagrid');
			menu_id = checkedId.val();
			$row = checkedId.parents('tr:first');
		}
		menu_id = $row.attr("data-id");
    $('#menu_id').val(menu_id);
    $('#old_menu_parent').val($('td:eq(2)', $row).text());
  	refreshBSOption($('#menu_parent'), $('td:eq(2)', $row).text());
    hideBSOption($('#menu_parent'), menu_id);
    $('#menu_title').val($('td:eq(3)', $row).attr("data-menu_title"));
    $('#menu_key').val($('td:eq(4)', $row).text());
/*    
    $('#menu_roles option').prop("selected", false);
    $('#menu_roles option').removeAttr("selected");
*/
  	$('#menu_roles').selectpicker('val', []);
  	menu_roles = $('input[name=menu_' + $('#menu_key').val() + ']').val();
  	$('#menu_roles').selectpicker('val', $.parseJSON(menu_roles));

    statuse = $('td:eq(6)', $row).text();
    if (statuse == "Active") {
    	menu_status = "A";
   	} else {
   		menu_status = "X";
		}
    $('#menu_status_'+menu_status.toLowerCase()).prop("checked",true);
    $('#menu_description').val($('td:eq(7)', $row).text());
    
		$('.add-edit-modal').modal({
		  backdrop: false,
			show: true
		})
		$('.add-edit-modal').on('shown.bs.modal', function () {
	    $('#menu_title').focus();
		})
  });
  
  $('#btnSave', '.add-edit-modal').button().click(function (e) {
  	e.preventDefault();
    var level = 0;
  	purpose = $('#purpose').val();
  	menu_id = $('#menu_id').val();
  	menu_parent = $('#menu_parent').val();
  	menu_type = $('#menu_type').val();
  	if (isNaN(menu_parent)) {
  		menu_parent = 0;
 		}
  	menu_title = $('#menu_title').val();
  	menu_key = $('#menu_key').val();
    menu_roles = $('#menu_roles').val();
  	menu_status = $('input[name=menu_status]:checked').val();
  	menu_description = $('#menu_description').val();
  	reqUri = $('form', '.add-edit-modal').attr('action');
  	if (menu_roles == null) {
  		menu_roles = [10];
 		}
  	if (purpose=="edit") {
  		reqUri += "updateMenuItem";
 		} else {
 			reqUri += "createMenuItem";
		}
    var request = $.ajax({
      url: reqUri,
      type: "POST",
			data: {purpose: purpose, menu_id: menu_id, menu_parent: menu_parent, menu_type: menu_type, menu_title: menu_title, menu_key: menu_key, menu_roles: menu_roles, menu_status: menu_status, menu_description: menu_description },
      dataType: "text"
    });
    // console.debug(request);
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
    	$msg = aret['msg'];
      if (aret['status']!=="1") {
      	$("#addeditMsg").removeClass("alert-success");
        $("#addeditMsg").removeClass("hide");
        $("#addeditMsg").addClass("alert-danger");
        $("#addeditMsg").html($msg);
		    $('#menu_title').select();
      } else {
        $("#addeditMsg").addClass("hide");
        $("#addeditMsg").html("");
        if (purpose=="edit") {
        	if (isNaN(menu_parent) || menu_parent=="0") {
        		level = 0;
      		} else {
	        	if ($('#old_menu_parent').val() != menu_parent) {
	  					level = getRowLevel(menu_parent) + 1;
	     			} else {
	     				level = $row.data("level");
	  				}
  				}
	        $('td:eq(2)', $row).text(menu_parent);
	        $('td:eq(3)', $row).html(("&mdash;").repeat(level) + ' ' + menu_title);
	        $('td:eq(3)', $row).attr("data-menu_title", menu_title);
	        $('td:eq(4)', $row).text(menu_key);
					newRoleHtml = '<div class="barrier"><ul>';
						for(i=0; i<menu_roles.length; i++) {
							newRoleHtml += '<li>';
							newRoleHtml += $('#menu_roles option[value="' + menu_roles[i] + '"]').html();
							newRoleHtml += '</li>';
						}
					newRoleHtml += '</ul>';
					newRoleHtml += '<input type="hidden" value="[' + menu_roles + ']" name="menu_' + menu_key + '"></div>';
        	$('td:eq(5)', $row).html(newRoleHtml);
        	$('td:eq(6)', $row).html(getHtmlStatus(menu_status));
        	$('td:eq(7)', $row).text(menu_description);
        	$('.add-edit-modal').modal('hide');
        	sorotMe($row,"success");
        	if ($('#old_menu_parent').val() != menu_parent) {
	       		moveRowAfter($row, getParentRow(menu_parent));
  				}
       	} else {
       		menu_id = aret['menu_id'];
        	if (isNaN(menu_parent) || menu_parent=="0") {
        		level = 0;
      		} else {
       			level = getRowLevel(menu_parent) + 1;
     			}
       		appendRow(menu_id, menu_parent, menu_title, menu_key, menu_roles, menu_status, menu_description, level);
       		moveRowAfter($('table tbody tr:last'), getParentRow(menu_parent));
       		$("#addeditMsg").removeClass("alert-danger");
	        $("#addeditMsg").removeClass("hide");
	        $("#addeditMsg").addClass("alert-success");
	        $("#addeditMsg").html($msg);
			  	$('#menu_id').val("");
			    $('#menu_title').val("");
			    $('#menu_key').val("");
			    $('#menu_action').val("");
				  $('#menu_roles').selectpicker('val',[]);
				  addItemBSOption($('#menu_parent'), {"value": menu_id}, menu_title);
					var defaultRole = $('#menu_roles').attr('defaultvalue');
					$('#menu_roles option[value="' + defaultRole +  '"]').prop("selected", true);
				  $('#menu_roles').val([defaultRole]);
				  $('#menu_roles').selectpicker('val', [defaultRole]);
			    $('#menu_status').val("");
			    $('#menu_description').val("");
			    $('#menu_title').focus();
        }
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#addeditMsg").removeClass("hide");
      $("#addeditMsg").html("Internal server error, Please try again?");
    });  
  });
  
	$(".btnDelete").button().click(function() {
  	$(".modal-body", "#deleteMenus").removeClass("hidden");
  	$("#btnDeleteNow", "#deleteMenus").removeClass("hidden");
    $("#deleteMsg", "#deleteMenus").addClass("hidden");
    $("#deleteMsg", "#deleteMenus").html("");
		menu_id = $(this).attr("data-menu_id");
		if (!menu_id) {
			var searchMenus = $('.check-rows:checked', '.datagrid').map(function(){
				trnya = $(this).parents('tr:first');
				id = $(this).val();
				menuname = $('.btnDelete', trnya).attr('data-menuname');
			  return {id: id, menuname: menuname};
			}).get();
			// console.debug(searchMenus);
			var menu_id = [];
			var menuname2delete = "";
			for(i=0; i<searchMenus.length; i++) {
				menu_id[i] = searchMenus[i]['id'];
				menuname2delete += '<span class="label label-danger">' + searchMenus[i]['menuname'] + '</span> ';
			}
			
			$('#menuname2delete', '#confirmDeleteMany').html(menuname2delete);
			$('#confirmDeleteOne').addClass("hidden");
			$('#confirmDeleteMany').removeClass("hidden");
			$('#menu_id', '#deleteMenus').val(menu_id);
		} else {
			$('#menuname2delete', '#confirmDeleteOne').text($(this).attr("data-menuname"));
			$('#confirmDeleteOne').removeClass("hidden");
			$('#confirmDeleteMany').addClass("hidden");
			$('#menu_id', '#deleteMenus').val(menu_id);
		}
		$('#deleteMenus').modal("show");
	});

	$("#btnDeleteNow", '#deleteMenus').button().click(function(e) {
		e.preventDefault();
		menu_id = $('#menu_id', '#deleteMenus').val();
		
  	reqUri = $('form', '.delete-modal').attr('action') + "deleteMenus";
    var request = $.ajax({
      url: reqUri,
      type: "POST",
			data: { menu_id: menu_id },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
      	$(".modal-body", "#deleteMenus").addClass("hidden")
      	$("#btnDeleteNow", "#deleteMenus").addClass("hidden")
        $("#deleteMsg", '#deleteMenus').removeClass("hidden");
        $("#deleteMsg", '#deleteMenus').html(aret['msg']);
      } else {
        $("#deleteMsg", '#deleteMenus').addClass("hidden");
        $("#deleteMsg", '#deleteMenus').html("");
        removeRows(menu_id);
        $('#menu_id', '#deleteMenus').val("");
        $('#deleteMenus').modal("hide");
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#deleteMsg").removeClass("hide");
      $("#deleteMsg").html("Internal server error, Please try again?");
			// $("#deleteMsg").html(jqXHR.responseText);
    });
	});
	
});

function appendRow(menu_id, menu_parent, menu_title, menu_key, menu_roles, menu_status, menu_description, level) {
	//get the footable object
	var footable = $('table').data('footable');
	
	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-id="' + menu_id + '" data-parent="' + menu_parent + '" data-level="' + level + '">';
	newRow += '<td class="col4checkbox"><input disabled="disabled" class="check-rows" name="checked_id[]" type="checkbox" value="' + menu_id + '" /></td>';
	newRow += '<td>' + menu_id + '</td>';
	newRow += '<td>' + menu_parent + '</td>';
	newRow += '<td data-menu_title="' + menu_title + '">' + ("&mdash;").repeat(level) + ' ' + menu_title + '</td>';
	newRow += '<td>' + menu_key + '</td>';
	newRow += '<td><div class="barrier"><ul>';
		for(i=0; i<menu_roles.length; i++) {
			newRow += '<li>';
			newRow += $('#menu_roles option[value="' + menu_roles[i] + '"]').html();
			newRow += '</li>';
		}
	newRow += '</ul><input type="hidden" value="[' + menu_roles + ']" name="menu_' + menu_key + '"></div></td>';
	newRow += '<td class="text-center">';
	newRow += getHtmlStatus(menu_status);
	newRow += '</td>';
	newRow += '<td>' + menu_description + '</td>';
	newRow += '<td><button disabled="disabled" class="btnEdit btn btn-primary btn-xs" data-menu_id="' + menu_id + '" title="Edit `' + menu_title + '`"><span class="glyphicon glyphicon-edit"></span></button></td>';
	newRow += '<td><button disabled="disabled" class="btnDelete btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal" data-menu_id="' + menu_id + '" title="Delete `' + menu_title + '`"><span class="glyphicon glyphicon-remove"></span></button></td>';
	newRow += '</tr>';
	
	//add it
	footable.appendRow(newRow);
	$('a[data-page="last"]').click();
	var el = $('tbody tr:last', 'table.footable');
	var rowpos = el.offset();
	$(window).scrollTop(rowpos.top);	
}