jQuery(document).ready(function ($) {
  "use strict";
	$('#users_can_register_checkbox').change(function() {
		if ($(this).is(':checked')) {
			$('#users_can_register_input').val("1");
		} else {
			$('#users_can_register_input').val("0");
		}
	});

  $('#date_format_custom').click(function() {
  	$('#date_format').select();
  });
  $('#date_format').click(function() {
  	$('#date_format_custom').prop("checked", true);
  });

  $('#time_format_custom').click(function() {
  	$('#time_format').select();
  });
  $('#time_format').click(function() {
  	$('#time_format_custom').prop("checked", true);
  });


	$('input[name="date_format"]').change(function() {
		var date_format = $(this).val();
		if (date_format != "__custom__") {
			$('#date_format').val(date_format);
		}
	});
	$('input[name="time_format"]').change(function() {
		var time_format = $(this).val();
		if (time_format != "__custom__") {
			$('#time_format').val(time_format);
		}
	});

	getTimezoneDate("date");
  $('#date_format').change(function() {
 		getTimezoneDate("date");
  });
  getTimezoneDate("time");
  $('#time_format').change(function() {
 		getTimezoneDate("time");
  });
  $('#timezone_string').change(function() {
  	getTimezoneDate("date");
		getTimezoneDate("time");
	})
	
	//Validation
	$(".btnSave").button().click(function() {
		var lret = true;
		var siteurl = $('#app_url').val();
		if (!is_url(siteurl)) {
			$(window).scrollTop(100);
			$("#siteurl").focus();
			lret = false;
		}
		var admin_email = $('#admin_email').val();
		if (!is_email(admin_email)) {
			$(window).scrollTop(100);
			$("#admin_email").focus();
			lret = false;
		}
		
    if (!lret) {
      return false;
    } else {
    	var form = $(this).closest('form');
    	form.submit();
   	}
	});
	
	// Mailer options
	$('#mail_mailer').change(function() {
		$('.mailer_smtp').hide();
		$('.mailer_sendmail').hide();
		var sm = $("option:selected", this).val();
		if (sm == "smtp") {
			$('.mailer_smtp').show();
		} else {
			if (sm == "sendmail") {
				$('.mailer_sendmail').show();
			}
		}
	});
	
	fpmodechange();
	$('input[name="data[frontpage_mode]"]').change(function() {
		fpmodechange($(this).val())
	});
	refreshRouteSelector();
	$('#base-module').change(function() {
		refreshRouteSelector('module');
		$('#base-action-input').val('')
		$('#base-controller-input').val('');
		$('#base-module-input').val($(this).val()) .focus();
	});
	$('#base-controller').change(function() {
		refreshRouteSelector('controller');
		$('#base-action-input').val('')
		$('#base-controller-input').val($(this).val()) .focus();
	});
	$('#base-action').change(function() {
		$('#base-action-input').val($(this).val()) .focus();
	});
	
});

function refreshRouteSelector(bySelector) {
	var mod = $("#base-module").val();
	var con = $("#base-controller").val();
	if (bySelector==undefined) {
		$(".route-controller").hide();
		$(".route-action").hide();
		$(".route-controller.route-" + mod).show();
		$(".route-action.route-" + mod + "-" + con).show();
		$("#base-controller").val($("#base-controller-input").val());
		$("#base-action").val($("#base-action-input").val());
	}
	if (bySelector=="module") {
		$("#base-controller:selected").removeAttr("selected");
		$("#base-controller:selected").prop("selected", false);
		$("#base-controller").val([]);
		$("#base-action:selected").removeAttr("selected");
		$("#base-action:selected").prop("selected", false);
		$("#base-action").val([]);
		$(".route-controller").hide();
		$(".route-action").hide();
		$(".route-controller.route-" + mod).show();
	}
	if (bySelector=="controller") {
		$("#base-action:selected").removeAttr("selected");
		$("#base-action:selected").prop("selected", false);
		$("#base-action").val([]);
		$(".route-action").hide();
		$(".route-action.route-" + mod + "-" + con).show();
	}
	
}

function fpmodechange(valune) {
	if (valune == undefined) {
		valune = $('input[name="data[frontpage_mode]"]:checked').val();
	}
	if (valune == "redirect") {
		$('#mode_redirect').removeAttr("disabled");
		$('#mode_redirect').focus();
		$('#route-group').hide();
	} else {
		$('#mode_redirect').attr("disabled", "disabled");
		$('#route-group').show();
	}
}

function getTimezoneDate($custom) {
	var timezone = $('#timezone_string').val();
 	var date_format = "";
	if ($custom=="date") {
		date_format = $('#date_format').val();
	} else {
		date_format = $('#time_format').val();
	}
  var request = $.ajax({
    url: "/settings/getTimezoneDate",
    type: "POST",
		data: {date_format: date_format, timezone: timezone},
    dataType: "text"
  });
  request.done(function(ret) {
  	if ($custom=="date") {
  		$('#custom_date').html(ret);
 		} else {
 			$('#custom_time').html(ret);
		}
  });
  request.fail(function(jqXHR, textStatus, error) {
  	return "error";
  });  
}