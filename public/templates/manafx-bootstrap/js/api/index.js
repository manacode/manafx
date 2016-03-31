$( document ).ready(function() {
  $('#regenerate').button().click(function (e) {
  	e.preventDefault();
  	var theurl = $(this).attr('href');
		var request = $.ajax({
		  url: theurl,
		  method: "POST",
		  dataType: "text"
		});
		request.done(function(text) {
		  sorotMe($("#api-key"));
		  $("#api-key").text(text);
		  $("#api-key").focus();
		});
		request.fail(function(jqXHR, textStatus) {
		  alert("Request failed: " + textStatus);
		});
  	
	});
	
  $('.add-ip-address').button().click(function (e) {
  	e.preventDefault();
		ipaddress = $('#ip-address').val();
		if (ipaddress=="") {
			$('.simple-error').show();
			$('#ip-address').focus();
			return;
		}
		if (!isValidAddress(ipaddress, 'ip')) {
			$('.simple-error').show();
			$('#ip-address').focus();
			return;
		}
		if (isInputhasValue(ipaddress)) {
			$('.simple-error').show();
			$('#ip-address').focus();
			return;
		}
		add_ip_address(ipaddress);
		$('.simple-error').hide();		
		$('#ip-address').val('');
		$('#ip-address').focus();
	});

 	$('table#ip-address-list tbody').on('click', ".deleteip", function () {
  	$row = $(this).parents('tr:first');
  	$row.remove();
	});
	
});

function isInputhasValue(value) {
	var ret = false;
	$('.input-ip-address').each(function(){
		if ($(this).val() == value) {
			ret = true;
		}
	});
	return ret;
}

function add_ip_address(ipaddress) {
	var oTable = $('table#ip-address-list tbody');

	//build up the row we are wanting to add
	var newRow = '';
	newRow += '<tr data-key="' + ipaddress + '">';
	newRow += '<td><input class="form-control input-ip-address" placeholder="IP Address" name="ipaddress[]" value="' + ipaddress + '" readonly>';
	newRow += '<td>';
	newRow += '<button type="button" class="deleteip btn btn-default">Remove</button>';
	newRow += '</td>';
	newRow += '</tr>';
	
	//add it
	oTable.append(newRow);
	
}
