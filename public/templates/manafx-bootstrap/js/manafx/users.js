$( document ).ready(function() {
	
	// Password Strength
  $("#user_password").keyup(function () {
    var pwvalue = $(this).val();
    var pwstrength = getPasswordStrength(pwvalue);
    $("#pwstrength").removeClass();
    $("#pwstrength").addClass("progress-bar");
    $("#pwstrength").attr("aria-valuenow", pwstrength);
    $("#pwstrength").css("width", pwstrength+"%");
    if (pwstrength>85) {
        $("#pwstrength").html("Very Strong");
        $("#pwstrength").addClass("progress-bar-success");
    }
    if (pwstrength>70 && pwstrength<=85) {
        $("#pwstrength").html("Strong");
        $("#pwstrength").addClass("progress-bar-info");
    }
    if (pwstrength>50 && pwstrength<=70) {
        $("#pwstrength").html("Moderate");
        $("#pwstrength").addClass("progress-bar-warning");
    }
    if (pwstrength<=50) {
        $("#pwstrength").html("Weak");
        $("#pwstrength").addClass("progress-bar-danger");
    }
    checkPasswordConfirm();
  });
  
  // Password Confirmation
  $("#confirm_pass").keyup(function () {
		checkPasswordConfirm();
	});

	$('span', '#confirmation').click(function(e) {
		$('#confirm_pass').select();
	});

});

function checkPasswordConfirm() {
	var p1 = $("#user_password").val();
	var p2 = $("#confirm_pass").val();
	$('#confirmation').addClass("has-feedback")
	$('span', '#confirmation').removeClass("hidden")
	if (p1==p2) {
		$('#confirmation').removeClass("has-error")
		$('#confirmation').addClass("has-success")
		$('span', '#confirmation').removeClass("glyphicon-remove")
		$('span', '#confirmation').addClass("glyphicon-ok")
	} else {
		$('#confirmation').removeClass("has-success")
		$('#confirmation').addClass("has-error")
		$('span', '#confirmation').removeClass("glyphicon-ok")
		$('span', '#confirmation').addClass("glyphicon-remove")
	}
}

function resetForm(purpose) {
	if (purpose === undefined) {
		purpose = "add";
	}
  $('#user_id').val("");
  $('#user_firstname').val("");
  $('#user_lastname').val("");
  $('#user_email').val("");
  $('#user_username').val("");
  $('#user_password').val("");
  $("#pwstrength").removeClass();
  $("#pwstrength").addClass("progress-bar");
  $("#pwstrength").attr("aria-valuenow", 0);
  $("#pwstrength").css("width", "0%");
  $('#confirm_pass').val("");
	$('#confirmation').removeClass("has-error")
	$('#confirmation').removeClass("has-success")
	$('span', '#confirmation').removeClass("glyphicon-remove")
	$('span', '#confirmation').removeClass("glyphicon-ok")
	// reset user_roles select box
  $('input[name=user_roles]').prop("checked", false);
  $('#user_roles option').prop("selected", false);
  $('#user_roles option').removeAttr("selected");
  $('#user_roles').val([]);
  if (purpose=="add") {
		var defaultRole = $('#user_roles').attr('defaultvalue');
		$('#user_roles option[value="' + defaultRole +  '"]').prop("selected", true);
	  $('#user_roles').val([defaultRole]);
 	} else {
    $('#user_status').val("");
  }
}

function getPasswordStrength(pw){
  var pwlength=(pw.length);
  if(pwlength>5)pwlength=5;
  var numnumeric=pw.replace(/[0-9]/g,"");
  var numeric=(pw.length-numnumeric.length);
  if(numeric>3)numeric=3;
  var symbols=pw.replace(/\W/g,"");
  var numsymbols=(pw.length-symbols.length);
  if(numsymbols>3)numsymbols=3;
  var numupper=pw.replace(/[A-Z]/g,"");
  var upper=(pw.length-numupper.length);
  if(upper>3)upper=3;
  var pwstrength=((pwlength*10)-20)+(numeric*10)+(numsymbols*15)+(upper*10);
  if(pwstrength<0){pwstrength=0}
  if(pwstrength>100){pwstrength=100}
  return pwstrength;
}

function getHtmlUserStatus($status) {
	switch ($status) {
		case "A":
			hret = '<span class="text-success">Active</span>';
			break;
		case "B":
			hret = '<strong class="text-danger">Banned</strong>';
			break;
		case "L":
			hret = '<strong class="text-warning">Locked</strong>';
			break;
		case "X":
			hret = "Pending";
	}
	return hret;
}