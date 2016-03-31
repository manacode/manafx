$( document ).ready(function() {
	var $row;
  $('.btnAdd', '.manafx-menus-index').button().click(function (e) {
  	e.preventDefault();
  	$('#purpose').val("add");
  	$('#menu_id').val("");
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

  $('.btnEdit', '.manafx-menus-index').button().click(function (e) {
  	e.preventDefault();
		$row = $(this).parents('tr:first');
  	$('#purpose').val("edit");
    $('#menu_title').val("");
    $('#menu_key').val("");
    $('input[name=menu_status]').prop("checked", false);
    $('#menu_description').val("");
    $('#myModalLabel', '.add-edit-modal').html($('#myModalLabel', '.add-edit-modal').attr("data-title_edit"));

		if ($row.index()==-1) {
			checkedId = $('.check-rows:checked', '.datagrid');
			menu_id = checkedId.val();
			$row = checkedId.parents('tr:first');
		}		
    $('#menu_id').val($('td:eq(1)', $row).text());
    $('#menu_title').val($('td:eq(2)', $row).text());
    $('#menu_key').val($('td:eq(3)', $row).text());
    statuse = $('td:eq(4)', $row).text();
    if (statuse == "Active") {
    	menu_status = "A";
   	} else {
   		menu_status = "X";
		}
    $('#menu_status_'+menu_status.toLowerCase()).prop("checked",true);
    $('#menu_description').val($('td:eq(5)', $row).text());
    
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
  	purpose = $('#purpose').val();
  	menu_id = $('#menu_id').val();
  	menu_title = $('#menu_title').val();
  	menu_key = $('#menu_key').val();
  	menu_status = $('input[name=menu_status]:checked').val();
  	menu_description = $('#menu_description').val();
  	if (purpose=="edit") {
  		reqUri = "/menus/updateMenu";
 		} else {
 			reqUri = "/menus/createMenu";
		}
    var request = $.ajax({
      url: reqUri,
      type: "POST",
			data: {purpose: purpose, menu_id: menu_id, menu_title: menu_title, menu_key: menu_key, menu_status: menu_status, menu_description: menu_description },
      dataType: "text"
    });
    console.debug(request);
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
	        $('td:eq(2) a', $row).text(menu_title);
	        $('td:eq(2) a', $row).attr("href", "menus/manage/" + menu_key);
	        $('td:eq(3)', $row).text(menu_key);
        	$('td:eq(4)', $row).html(getHtmlStatus(menu_status));
        	$('td:eq(5)', $row).text(menu_description);
        	$('.add-edit-modal').modal('hide');
        	sorotMe($row,"success");
       	} else {
       		menu_id = aret['menu_id'];
       		appendRow(menu_id, menu_title, menu_key, menu_status, menu_description);
       		$("#addeditMsg").removeClass("alert-danger");
	        $("#addeditMsg").removeClass("hide");
	        $("#addeditMsg").addClass("alert-success");
	        $("#addeditMsg").html($msg);
			  	$('#menu_id').val("");
			    $('#menu_title').val("");
			    $('#menu_key').val("");
			    $('#menu_status').val("");
			    $('#menu_description').val("");
			    $('#menu_title').focus();
        }
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#addeditMsg").removeClass("hide");
      $("#addeditMsg").html("Internal server error, Please try again?");
      alert( "Request failed: " + textStatus );
//      $("#addeditMsg").html(jqXHR.responseText);
    });  
  });

	$(".btnDelete", '.manafx-menus-index').button().click(function() {
  	$(".modal-body", "#deleteMenus").removeClass("hidden");
  	$("#btnDeleteNow", "#deleteMenus").removeClass("hidden");
    $("#deleteMsg", "#deleteMenus").addClass("hidden");
    $("#deleteMsg", "#deleteMenus").html("");
		menu_id = $(this).attr("data-menu_id");
		if (!menu_id) {
			var searchMenus = $('.check-rows:checked', '.datagrid').map(function(){
				trnya = $(this).parents('tr:first');
				id = $(this).val();
				menutitle = $('.btnDelete', trnya).attr('data-menutitle');
			  return {id: id, menutitle: menutitle};
			}).get();
			// console.debug(searchMenus);
			var menu_id = [];
			var menutitle2delete = "";
			for(i=0; i<searchMenus.length; i++) {
				menu_id[i] = searchMenus[i]['id'];
				menutitle2delete += '<span class="label label-danger">' + searchMenus[i]['menutitle'] + '</span> ';
			}
			
			$('#menutitle2delete', '#confirmDeleteMany').html(menutitle2delete);
			$('#confirmDeleteOne').addClass("hidden");
			$('#confirmDeleteMany').removeClass("hidden");
			$('#menu_id', '#deleteMenus').val(menu_id);
		} else {
			$('#menutitle2delete', '#confirmDeleteOne').text($(this).attr("data-menutitle"));
			$('#confirmDeleteOne').removeClass("hidden");
			$('#confirmDeleteMany').addClass("hidden");
			$('#menu_id', '#deleteMenus').val(menu_id);
		}
		$('#deleteMenus').modal("show");
	});

	$("#btnDeleteNow", '#deleteMenus').button().click(function(e) {
		e.preventDefault();
		menu_id = $('#menu_id', '#deleteMenus').val();
		
    var request = $.ajax({
      url: "/menus/deleteMenus",
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

function appendRow(menu_id, menu_title, menu_key, menu_status, menu_description) {
	//get the footable object
	var footable = $('table').data('footable');
	
	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-id="' + menu_id + '">';
	newRow += '<td class="col4checkbox"><input disabled="disabled" class="check-rows" name="checked_id[]" type="checkbox" value="' + menu_id + '" /></td>';
	newRow += '<td>' + menu_id + '</td>';
	newRow += '<td><a href="menus/manage/' + menu_key + '">' + menu_title + '</a></td>';
	newRow += '<td>' + menu_key + '</td>';
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