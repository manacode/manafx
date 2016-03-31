$( document ).ready(function() {
	$("#btnSave").button().click(function(e) {
		e.preventDefault();
		$('#confirmPasswordDialog').modal("show");
		$('#confirmPasswordDialog').on('shown.bs.modal', function () {
			$('#confirm_password').focus();
		})  	
	});
	$("#btnSubmit").button().click(function(e) {
		e.preventDefault();
		$('#user_password').val($('#confirm_password').val());
		$('form[name="frmProfile"]').submit();		
	});
});