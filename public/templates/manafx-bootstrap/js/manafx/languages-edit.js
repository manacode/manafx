$( document ).ready(function() {
	$('#languageName').focus();
  $('#btnSubmit').button().click(function (e) {
  	e.preventDefault();
  	if ($('#languageName').val() == "") {
  		$('#languageName').parent().addClass("has-error");
  		$('#languageName').focus();
  		return false;
 		}
 		$(this).closest('form').submit();
	});
});