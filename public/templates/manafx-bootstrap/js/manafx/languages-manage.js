$( document ).ready(function() {
	var $row;
	$('.btnAdd', '.manafx-languages-manage').button().click(function (e) {
  	e.preventDefault();
  	$('#purpose').val("add");
  	$('#languageFileName').val("");
  	$('#languageFileNameInput').val("");
		$('.add-edit-modal').modal({
		  backdrop: false,
			show: true,
		});
		$('.add-edit-modal').on('shown.bs.modal', function () {
			$('#languageModule').focus();
		});
	});
	
  $('.btnEdit', '.manafx-languages-manage').button().click(function (e) {
  	e.preventDefault();
		$row = $(this).parents('tr:first');
		languageCode = $("#languageCode").val();
		languageModule = $(this).attr("data-module");
		languageFileName = $(this).attr("data-filename");

		if (!languageModule) {
			checkedId = $('.check-rows:checked', '.datagrid');
			languageModule = checkedId.attr("data-module");
			languageFileName = checkedId.val();
			$row = checkedId.parents('tr:first');
		}

		languageFileNameInput = languageFileName;
		if (languageFileNameInput.substr(languageFileNameInput.length-4)==".php") {
			languageFileNameInput = languageFileNameInput.substr(0, languageFileNameInput.length -4 );
		}
		if (languageFileNameInput.substr(0, languageCode.length + 1)==languageCode + ".") {
			languageFileNameInput = languageFileNameInput.substr(languageCode.length + 1);
		}
		if (languageFileNameInput == languageCode) {
			languageFileNameInput = "";
		}
  	
  	$('#purpose').val("edit");
    $('#languageModule').val(languageModule);
    $('#languageFileName').val(languageFileName);
    $('#languageFileNameInput').val(languageFileNameInput);
		$('#frmEditFile').submit();
  });
  
  $('#btnCreate', '.add-edit-modal').button().click(function (e) {
  	e.preventDefault();
  	languageCode = $('#languageCode').val();
  	languageFileNameInput = $('#languageFileNameInput').val();
  	if (languageFileNameInput == "") {
  		languageFileName = languageCode + '.php';
 		} else {
 			languageFileName = languageCode + '.' + languageFileNameInput + '.php';
		}
		$('#languageFileName').val(languageFileName);
  	$('#frmEditFile').submit();
  });

	$(".btnDelete", '.manafx-languages-manage').button().click(function() {
  	$(".modal-body", "#deleteLanguageFiles").removeClass("hidden");
  	$("#btnDeleteNow", "#deleteLanguageFiles").removeClass("hidden");
    $("#deleteMsg", "#deleteLanguageFiles").addClass("hidden");
    $("#deleteMsg", "#deleteLanguageFiles").html("");
		languageFiles = $(this).attr("data-filename");
		if (!languageFiles) {
			var searchFiles = $('.check-rows:checked', '.datagrid').map(function(){
				trnya = $(this).parents('tr:first');
				filename = $(this).val();
				module = $('.btnDelete', trnya).attr('data-module');
			  return {filename: filename, module: module};
			}).get();
			// console.debug(searchFiles);
			var languageFiles = [];
			var files2delete = "";
			for(i=0; i<searchFiles.length; i++) {
				languageFiles[i] = searchFiles[i]['filename'];
				module
				files2delete += '<span class="label label-danger">' + "(" + searchFiles[i]['module'] + ")" + searchFiles[i]['filename'] + '</span> ';
			}
			
			$('#files2delete', '#confirmDeleteMany').html(files2delete);
			$('#confirmDeleteOne').addClass("hidden");
			$('#confirmDeleteMany').removeClass("hidden");
			$('#languageFiles', '#deleteLanguageFiles').val(languageFiles);
		} else {
			$('#files2delete', '#confirmDeleteOne').text("(" + $(this).attr("data-module") + ")" + $(this).attr("data-filename"));
			$('#confirmDeleteOne').removeClass("hidden");
			$('#confirmDeleteMany').addClass("hidden");
			$('#languageFiles', '#deleteLanguageFiles').val(languageFiles);
		}
		$('#deleteLanguageFiles').modal("show");
	});

	$("#btnDeleteNow", '#deleteLanguageFiles').button().click(function(e) {
		e.preventDefault();
		languageFiles = $('#languageFiles', '#deleteLanguageFiles').val();
		
    var request = $.ajax({
      url: "/languages/deleteFiles",
      type: "POST",
			data: { languageFiles: languageFiles },
      dataType: "text"
    });
    request.done(function(ret) {
    	var aret = $.parseJSON(ret);
      if (aret['status']!=="1") {
      	$(".modal-body", "#deleteLanguageFiles").addClass("hidden")
      	$("#btnDeleteNow", "#deleteLanguageFiles").addClass("hidden")
        $("#deleteMsg", '#deleteLanguageFiles').removeClass("hidden");
        $("#deleteMsg", '#deleteLanguageFiles').html(aret['msg']);
      } else {
        $("#deleteMsg", '#deleteLanguageFiles').addClass("hidden");
        $("#deleteMsg", '#deleteLanguageFiles').html("");
        removeRows(languageCode);
        $('#languageFiles', '#deleteLanguageFiles').val("");
        $('#deleteLanguageFiles').modal("hide");
      }
    });
    request.fail(function(jqXHR, textStatus) {
      $("#deleteMsg").removeClass("hide");
      $("#deleteMsg").html("Internal server error, Please try again?");
			// $("#deleteMsg").html(jqXHR.responseText);
    });
	});
	
});

function appendRow(filename, module, path) {
	//get the footable object
	var footable = $('table').data('footable');
	
	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-id="' + filename + '">';
	newRow += '<td class="col4checkbox"><input disabled="disabled" class="check-rows" name="checked_id[]" type="checkbox" value="' + filename + '" /></td>';
	newRow += '<td>' + filename + '</td>';
	newRow += '<td class="text-center">' + module + '</td>';
	newRow += '<td>' + path + '</td>';
	newRow += '<td><button disabled="disabled" class="btnEdit btn btn-primary btn-xs" data-filename="' + filename + '"  data-module="' + module + '" title="Edit `' + filename + '`"><span class="glyphicon glyphicon-edit"></span></button></td>';
	newRow += '<td><button disabled="disabled" class="btnDelete btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal" data-filename="' + filename + '"  data-module="' + module + '" title="Delete `' + filename + '`"><span class="glyphicon glyphicon-remove"></span></button></td>';
	newRow += '</tr>';
	
	//add it
	footable.appendRow(newRow);
	
}