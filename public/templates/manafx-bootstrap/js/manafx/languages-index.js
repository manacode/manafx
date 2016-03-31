$( document ).ready(function() {

  $('.btn-action').button().click(function (e) {
  	e.preventDefault();
  	var form = $(this).closest('form');
  	if ($(this).attr("data-action")=="manage") {
	  	var lang = $('option:selected','#language-selector').val();
	  	$('input[name="languageCode"]', form).val(lang);
	  	purpose = "manage";
  	} else {
  		purpose = "create";
 		}
  	var formaction = form.attr("action") + purpose;
  	form.attr("action", formaction);
 		form.submit();
	});

	$('#language-selector').change(function() {
		$('#manage-btn-group .btn-action').attr("data-action","manage");
		caption = $('#manage-btn-group .btn-action').data("caption_manage");
		$('#manage-btn-group .btn-action').html(caption)
		$('#manage-btn-group .btn-togler').removeClass('disabled')
	});
	
  $('#manage-default').button().click(function (e) {
  	e.preventDefault();
  	var form = $(this).closest('form');
  	var lang = $('option:selected','#language-selector').val();
  	var formaction = form.attr("action") + "setDefault";
  	$('input[name="languageCode"]', form).val(lang);
  	form.attr("action", formaction);
 		form.submit();
	});

  $('#manage-delete').button().click(function (e) {
  	e.preventDefault();
		var name = $('option:selected','#language-selector').text();
		$('#language_id', '#deleteLanguage').html(name);
		$('#deleteLanguage').modal("show");
	});
	
	$("#btnDeleteNow").button().click(function(e) {
		e.preventDefault();
  	var form = $('form[name="frmDelete"]');
  	var lang = $('option:selected','#language-selector').val();
  	var langName = $('option:selected','#language-selector').html();
  	$('input[name="languageCode2Delete"]', form).val(lang);
  	$('input[name="languageName2Delete"]', form).val(langName);
 		form.submit();
	});

	$('#country-selector').change(function() {
		$('#btnCreateLanguage').removeClass('disabled')
		var country_code = $("option:selected", this).val();
		var country_name = $("option:selected", this).text();
		$('#languageCode').val(country_code);
		$('#languageName').val(country_name);
		$('#languageCode').focus();
		setCaretAtEnd('languageCode');
	});

  $('#btnCreateLanguage').button().click(function (e) {
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