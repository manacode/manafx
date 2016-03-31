$( document ).ready(function() {

  $('.btnDetails').button().click(function (e) {
  	e.preventDefault();
  	var tpl = $(this).parent().attr("data-template");
  	var theme = $(this).parent().attr("data-theme");
  	$('#template2activate').val(tpl);
  	$('#template2delete').val(tpl);

		var screenshot = templates[tpl]['screenshot'];
		var name = templates[tpl]['name'];
		var version = templates[tpl]['version'];
		var author = templates[tpl]['author'];
		var author_uri = templates[tpl]['author_uri'];
		var description = templates[tpl]['description'];
		var tags = templates[tpl]['tags'];
		
		var htmle = '';
		$('.template-details').html(htmle);
		htmle += '<div class="col-sm-6 col-md-4">';
		htmle += '<div class="thumbnail">';
		htmle += '<img class="media-object" src="' + screenshot + '" alt="' + name + '" />';
		htmle += '</div>';
		htmle += '</div>';
		htmle += '<div class="media-body">';
		htmle += '<h4>' + name + ' <span class="small">Version: ' + version + '</span></h4>';
		htmle += '<p>By <a href="' + author_uri + '">' + author + '</a></p>';
		htmle += '<p>' + description + '</p>';
		htmle += '<p class="small"><b>Tags</b>: <span class="text-muted">' + tags + '</span></p>';
		htmle += '</div>';
		$('.template-details').html(htmle);
		$('#templatedetails').modal({
			show: true
		})
	});

	$('#theme-selector').change(function() {
		var theme = $("option:selected", this).val();
		var screenshot = $("option:selected", this).attr("data-screenshot");
		$('#active-screenshot').attr("src", screenshot);
  	$('#template2activate').val("");
  	$('#theme2activate').val(theme);
	});

  $('.btnActivate').button().click(function (e) {
  	e.preventDefault();
  	var tpl = $(this).parent().attr("data-template");
  	var theme = $(this).parent().attr("data-theme");
  	$('#template2activate').val(tpl);
  	$('#theme2activate').val(theme);
  	$('form[name="frmActivate"]').submit();
	});

  $('#btnActivateNow, #btnApplyTheme').button().click(function (e) {
  	e.preventDefault();
  	$('form[name="frmActivate"]').submit();
	});

	$("#btnDelete").button().click(function() {
  	var tpl = $('#template2delete').val();
		var name = templates[tpl]['name'];
		$('#template_id', '#deleteTemplate').html(name + " (" + tpl + ")");
		$('#deleteTemplate').modal("show");
	});

	$("#btnDeleteNow").button().click(function(e) {
		e.preventDefault();
		$('form[name="frmDelete"]').submit();		
	});

});