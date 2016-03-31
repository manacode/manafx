$( document ).ready(function() {
	$('#username').focus();
	
  $('#btnSubmit').button().click(function (e) {
  	e.preventDefault();
  	if ($('#username').val()=="") {
  		$('#username').parent().addClass("has-error");
  		$('#username').focus();
  		return false;
 		}
 		$('.form-signin').submit();
	});
	
	$('#username').change(function() {
		if ($(this).val()!="") {
			$('#username').parent().removeClass("has-error");
		}
	});

});