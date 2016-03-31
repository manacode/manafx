$( document ).ready(function() {

	$('#country-selector').change(function() {
		$('#btnSubmit').removeClass('disabled')
		$('#btnSubmit').removeAttr('disabled')
		var country_code = $("option:selected", this).val();
		var country_name = $("option:selected", this).text();
		$('#languageCode').val(country_code + "-" + country_code.toUpperCase());
		$('#languageName').val(country_name);
		$('#languageCode').focus();
		setCaretAtEnd('languageCode');
	});

  $('#btnSubmit').button().click(function (e) {
  	e.preventDefault();
  	if ($('#languageCode').val() == "") {
  		$('#languageCode').parent().addClass("has-error");
  		$('#languageCode').focus();
  		return false;
 		}
  	if ($('#languageName').val() == "") {
  		$('#languageName').parent().addClass("has-error");
  		$('#languageName').focus();
  		return false;
 		}
 		$(this).closest('form').submit();
	});

});