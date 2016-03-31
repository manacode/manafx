jQuery(document).ready(function ($) {
  "use strict";
	$(".btnDelete", '.manafx-settings-modules').button().click(function() {
		var module_id = $(this).attr("data-module_id");
		var module_name = $(this).attr("data-module_name");
		$('#module_id', '#deleteModule').html(module_name + " (" + module_id + ")");
		$('#module', '#deleteModule').val(module_id);
		$("#btnDeleteNow", '#deleteModule').attr('data-link',$(this).attr("data-link"));
		$('#deleteModule').modal("show");
	});

	$("#btnDeleteNow", '#deleteModule').button().click(function(e) {
		e.preventDefault();
		
		window.location=$(this).attr("data-link");
		
	});
  
});