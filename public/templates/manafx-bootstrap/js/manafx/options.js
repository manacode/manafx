$( document ).ready(function() {
	var $row;
/*************************************************
	Options Action
*************************************************/
  $('.btnAdd', '.manafx-options-index').button().click(function (e) {
  	e.preventDefault();
  	$('#purpose').val("add");
  	$('#option_id').val("");
    $('#option_name').val("");
    $('#option_value').val("");
    $('#option_autoload').prop("checked", true);
    $('#option_description').val("");
    $('#myModalLabel', '#editOption').html("Add New Option");
    $('#btnSave', '#editOption').text("Submit");
		$('#editOption').modal({
		  backdrop: false,
			show: true
		})
		$('#editOption').on('shown.bs.modal', function () {
		    $('#option_name').focus();
		})  	
	});
	
  $('.btnEdit', '.manafx-options-index').button().click(function (e) {
  	e.preventDefault();
		$row = $(this).parents('tr:first');
  	
  	$('#purpose').val("edit");
    $('#option_name').val("");
    $('#option_value').val("");
    $('#option_autoload').prop("checked", true);
    $('#option_description').val("");
    $('#myModalLabel', '#editOption').html("Edit Option");
    $('#btnSave', '#editOption').text("Save Changes");

		option_id = $(this).attr("data-option_id");
		if (!option_id) {
			var searchIDs = $('.check-rows:checked', '.datagrid').map(function(){
			  return $(this).val();
			});
			option_id = searchIDs[0];
		}
		
    var request = $.ajax({
      url:  "/options/getOption",
      type: "POST",
			data: { option_id: option_id },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
        $("#alertMsg").removeClass("hide");
        $("#alertMsg").html(aret['msg']);
      } else {
        $("#alertMsg").addClass("hide");
        $("#alertMsg").html("");
        datane = aret['data'];
        $('#option_id').val(option_id);
        $('#option_name').val(datane["option_name"]);
        $('#option_value').val(datane["option_value"]);
        if (datane["option_autoload"].toUpperCase()=="N" || datane["option_autoload"]=="0") {
        	$('#option_autoload').removeAttr("checked");
        	$('#option_autoload').prop('checked', false);
       	} else {
       		$('#option_autoload').attr("checked", "checked");
       		$('#option_autoload').prop('checked', true);
    		}
    		$identity = datane["option_identity"]
        $('#option_identity option[value="' + $identity + '"]').prop("selected", true);

        $('#option_autoload').val(datane["option_autoload"]);
        $('#option_description').val(datane["option_description"]);
        
				$('#editOption').modal({
				  backdrop: false,
  				show: true
				})
				$('#editOption').on('shown.bs.modal', function () {
				    $('#option_value').focus();
				})  	
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#alertMsg").removeClass("hide");
      $("#alertMsg").html("Internal server error, Please try again?");
			// $("#alertMsg").html(jqXHR.responseText);
    });
  });
  
  $('#btnSave', '#editOption').button().click(function (e) {
  	e.preventDefault();
  	purpose = $('#purpose').val();
  	option_id = $('#option_id').val();
  	option_name = $('#option_name').val();
  	option_value = $('#option_value').val();
  	if ($('#option_autoload').is(':checked')) {
  		option_autoload = "Y";
 		} else {
 			option_autoload = "N";
		}
		option_identity = $('#option_identity').find(":selected").val();
  	option_description = $('#option_description').val();
  	if (purpose=="edit") {
  		reqUri = "/options/updateOption";
 		} else {
 			reqUri = "/options/createOption";
		}
    var request = $.ajax({
      url: reqUri,
      type: "POST",
			data: {purpose: purpose, option_id: option_id, option_name: option_name, option_value: option_value, option_autoload: option_autoload, option_identity: option_identity, option_description: option_description },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
    	$msg = aret['msg'];
      if (aret['status']!=="1") {
      	$("#alertMsg").removeClass("alert-success");
        $("#alertMsg").removeClass("hide");
        $("#alertMsg").addClass("alert-danger");
        $("#alertMsg").html($msg);
		    $('#option_name').select();
      } else {
        $("#alertMsg").addClass("hide");
        $("#alertMsg").html("");
        
        if (purpose=="edit") {
	        $('td:eq(2)', $row).text(option_name);
	        $('td:eq(3)', $row).html('<pre class="pre-scrollable"><code>' + option_value + '</code></pre>');
        	$('td:eq(4)', $row).text(option_autoload);
        	$('td:eq(5)', $row).text(option_identity);
        	$('td:eq(6)', $row).text(option_description);
        	$('#editOption').modal('hide');
        	sorotMe($row,"success");
       	} else {
       		option_id = aret['option_id'];
       		appendRow(option_id, option_name, option_value, option_autoload, option_identity, option_description);
       		$("#alertMsg").removeClass("alert-danger");
	        $("#alertMsg").removeClass("hide");
	        $("#alertMsg").addClass("alert-success");
	        $("#alertMsg").html($msg);
			  	$('#option_id').val("");
			    $('#option_name').val("");
			    $('#option_value').val("");
			    $('#option_description').val("");
			    $('#option_name').focus();
        }
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#alertMsg").removeClass("hide");
      $("#alertMsg").html("Internal server error, Please try again?");
//      $("#alertMsg").html(jqXHR.responseText);
    });  
  });

	$(".btnDelete", '.manafx-options-index').button().click(function() {
		option_id = $(this).attr("data-option_id");
		if (!option_id) {
			var searchIDs = $('.check-rows:checked', '.datagrid').map(function(){
			  return $(this).val();
			}).get();
			option_id = searchIDs;
		}
		$('#option_id', '#deleteOptions').val(option_id);
		$('#deleteOptions').modal("show");
	});

	$("#btnDeleteNow", '#deleteOptions').button().click(function(e) {
		e.preventDefault();
		option_id = $('#option_id', '#deleteOptions').val();
		
    var request = $.ajax({
      url: "/options/deleteOptions",
      type: "POST",
			data: { option_id: option_id },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
        $("#alertMsg").removeClass("hide");
        $("#alertMsg").html(aret['msg']);
      } else {
        $("#alertMsg").addClass("hide");
        $("#alertMsg").html("");
        removeRows(option_id);
        $('#option_id', '#deleteOptions').val("");
        $('#deleteOptions').modal("hide");
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#alertMsg").removeClass("hide");
      $("#alertMsg").html("Internal server error, Please try again?");
			// $("#alertMsg").html(jqXHR.responseText);
    });
	});


});

function appendRow(option_id, option_name, option_value, option_autoload, option_identity, option_description) {
	//get the footable object
	var footable = $('table').data('footable');
	
	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-id="' + option_id + '">';
	newRow += '<td class="col4checkbox"><input disabled="disabled" class="check-rows" name="checked_id[]" type="checkbox" value="' + option_id + '" /></td>';
	newRow += '<td>' + option_id + '</td>';
	newRow += '<td>' + option_name + '</td>';
	newRow += '<td>';
	newRow += '<pre class="pre-scrollable"><code>';
	newRow += option_value;
	newRow += '</code></pre>';
	newRow += '</td>';
	newRow += '<td>' + option_autoload + '</td>';
	newRow += '<td>' + option_identity + '</td>';
	newRow += '<td>' + option_description + '</td>';
	newRow += '<td><button disabled="disabled" class="btnEdit btn btn-primary btn-sm" data-option_id="' + option_id + '" title="Edit `' + option_name + '`"><span class="glyphicon glyphicon-edit"></span></button></td>';
	newRow += '<td><button disabled="disabled" class="btnDelete btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" data-option_id="' + option_id + '" title="Delete `' + option_name + '`"><span class="glyphicon glyphicon-remove"></span></button></td>';
	newRow += '</tr>';
	
	//add it
	footable.appendRow(newRow);
	
}