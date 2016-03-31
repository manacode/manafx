$( document ).ready(function() {
	var $row;
	var newKey = [];
	var $dataChanged = false;
  $('.btnAdd', '.manafx-languages-translation').button().click(function (e) {
  	e.preventDefault();
  	$('#purpose').val("add");
  	$('#translationKey').val("");
  	$('#translationKey').removeAttr("oldtranslationkey");
    $('#translationValue').val("");
    $('#myModalLabel', '.add-edit-modal').html($('#myModalLabel', '.add-edit-modal').attr('text-create'));
		$('.add-edit-modal').modal({
		  backdrop: false,
			show: true,
		});
		$('.add-edit-modal').on('shown.bs.modal', function () {
			$('#translationKey').focus();
		});
	});
	
  $('.btnEdit', '.manafx-languages-translation').button().click(function (e) {
  	e.preventDefault();
		$row = $(this).parents('tr:first');
		translationKey = $('td:eq(1) input', $row).val();
		translationValue = $('td:eq(2) input', $row).val();
		if (!translationKey) {
			checkedId = $('.check-rows:checked', '.datagrid');
			$row = checkedId.parents('tr:first');
			translationKey = $('td:eq(1) input', $row).val();
			translationValue = $('td:eq(2) input', $row).val();
		}
  	$('#purpose').val("edit");
  	$('#translationKey').val(translationKey);
  	$('#translationKey').attr("oldtranslationkey", translationKey);
    $('#translationValue').val(translationValue);
    $('#myModalLabel', '.add-edit-modal').html($('#myModalLabel', '.add-edit-modal').attr('text-edit'));
		$('.add-edit-modal').modal({
		  backdrop: false,
			show: true,
		});
		$('.add-edit-modal').on('shown.bs.modal', function () {
			$('#translationValue').focus();
		});
  });

  $('#btnUpdateForm', '.add-edit-modal').button().click(function (e) {
  	e.preventDefault();
  	translationKey = $('#translationKey').val();
  	oldTranslationKey = $('#translationKey').attr("oldtranslationkey");
  	if (translationKey == "") {
  		$('#translationKey').parent().addClass("has-error");
  		$('#translationKey').focus();
  		return false;
 		}
		
		st = searchText($('table tbody tr'), $('.translationKey'), translationKey);
  	if ( (st!= false && st != oldTranslationKey) || newKey.indexOf(translationKey) > -1) {
    	$("#addeditMsg").removeClass("alert-success");
      $("#addeditMsg").removeClass("hide");
      $("#addeditMsg").addClass("alert-danger");
      $("#addeditMsg").html('Translation Key already exists!');
  		$('#translationKey').parent().addClass("has-error");
  		$('#translationKey').focus();
  		return false;
 		}
 		
 		translationValue = $('#translationValue').val();
  	if ($('#translationValue').val() == "") {
  		$('#translationValue').parent().addClass("has-error");
  		$('#translationValue').focus();
  		return false;
 		}
 		if ($('#purpose').val() == "add") {
 			appendRow(translationKey, translationValue);
 			newKey.push(translationKey);
	  	$('#translationKey').parent().removeClass("has-error");
	  	$('#translationKey').val("");
	    $('#translationValue').val("");
			$('#translationKey').focus();
      $("#addeditMsg").addClass("hide");
      $("#addeditMsg").html("");
		} else {
			$('td:eq(1) input', $row).val(translationKey);
      $('td:eq(1) span.translationKey', $row).text(translationKey);
			$('td:eq(2) input', $row).val(translationValue);
    	$('td:eq(2) span', $row).text(translationValue);
    	$('.add-edit-modal').modal('hide');
    	sorotMe($row,"success");
		}
		$dataChanged = true;
		$("#btnUpdateTranslation").removeAttr("disabled");
  });

	$(".btnDelete", '.manafx-languages-translation').button().click(function(e) {
  	e.preventDefault();
  	$(".modal-body", "#deleteTranslation").removeClass("hidden");
  	$("#btnDeleteNow", "#deleteTranslation").removeClass("hidden");
    $("#deleteMsg", "#deleteTranslation").addClass("hidden");
    $("#deleteMsg", "#deleteTranslation").html("");
		translationKey = $(this).attr("data-key");

		$row = $(this).parents('tr:first');
		translationKey = $('td:eq(1) input', $row).val();
		if (!translationKey) {
			var searchKeys = $('.check-rows:checked', '.datagrid').map(function(){
			  return {tKey: $(this).val()};
			}).get();
			var translationKey = [];
			var keys2delete = "";
			for(i=0; i<searchKeys.length; i++) {
				translationKey[i] = searchKeys[i]['tKey'];
				keys2delete += '<span class="label label-danger">' + searchKeys[i]['tKey'] + '</span> ';
			}
			
			$('#keys2delete', '#confirmDeleteMany').html(keys2delete);
			$('#confirmDeleteOne').addClass("hidden");
			$('#confirmDeleteMany').removeClass("hidden");
			$('#translationKey', '#deleteTranslation').val(translationKey);
		} else {
			$('#key2delete', '#confirmDeleteOne').text(translationKey);
			$('#confirmDeleteOne').removeClass("hidden");
			$('#confirmDeleteMany').addClass("hidden");
			$('#translationKey', '#deleteTranslation').val(translationKey);
		}
		$('#deleteTranslation').modal({
		  backdrop: false,
			show: true,
		});
		$('#deleteTranslation').on('shown.bs.modal', function () {
			$('#btnDeleteNow').focus();
		});
	});

	$("#btnDeleteNow", '#deleteTranslation').button().click(function(e) {
		e.preventDefault();
		translationKey = $('#translationKey', '#deleteTranslation').val();
    removeRows(translationKey);
    $("#deleteMsg", '#deleteTranslation').addClass("hidden");
    $("#deleteMsg", '#deleteTranslation').html("");
    $('#translationKey', '#deleteTranslation').val("");
    $('#deleteTranslation').modal("hide");
		$dataChanged = true;
		$("#btnUpdateTranslation").removeAttr("disabled");
	});

	$(".btnBack").button().click(function(e) {
		e.preventDefault();
		$("#frmBack").submit();
	});
	
	$("#btnUpdateTranslation").button().click(function(e) {
		e.preventDefault();
		$dataChanged = false;
		$("#frmTranslation").submit();
	});

	$(window).bind('beforeunload', function(e){
		if ($dataChanged) {
			sorotButton($("#btnUpdateTranslation"),"btn-warning");
			message = "You have attempted to leave this page.  If you have made any changes to the fields without clicking the Update button, your changes will be lost.  Are you sure you want to exit this page?";
			e.returnValue = message;
			return message;
		} else {
			return;
		}
	});	
	
});

function appendRow(translationKey, translationValue) {
	//get the footable object
	var footable = $('table').data('footable');
	
	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-id="' + translationKey + '">';
	newRow += '<td class="col4checkbox"><input disabled="disabled" class="check-rows" name="checked_id[]" type="checkbox" value="' + translationKey + '" /></td>';
	newRow += '<td>' + '<input type="hidden" name="translationKey[]" value="' + translationKey + '" /><span>' + translationKey + '</span>' + '</td>';
	newRow += '<td>' + '<input type="hidden" name="translationValue[]" value="' + translationValue + '" /><span>' + translationValue + '</span>' + '</td>';
//	newRow += '<td><button disabled="disabled" class="btnEdit btn btn-primary btn-xs" title="Edit `' + translationKey + '`"><span class="glyphicon glyphicon-edit"></span></button></td>';
//	newRow += '<td><button disabled="disabled" class="btnDelete btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal" title="Delete `' + translationKey + '`"><span class="glyphicon glyphicon-remove"></span></button></td>';
	newRow += '<td><button disabled="disabled" class="btnEdit btn btn-primary btn-xs" title="Edit `' + translationKey + '`"><span class="glyphicon glyphicon-edit"></span></button></td>';
	newRow += '<td><button disabled="disabled" class="btnDelete btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal" title="Delete `' + translationKey + '`"><span class="glyphicon glyphicon-remove"></span></button></td>';
	newRow += '</tr>';


	//add it
	footable.appendRow(newRow);
	$('a[data-page="last"]').click();
	var el = $('tbody tr:last', 'table.footable');
	var rowpos = el.offset();
	$(window).scrollTop(rowpos.top);	
}
