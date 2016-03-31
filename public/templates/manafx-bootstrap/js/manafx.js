function is_email(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
function is_url(url) {    
  var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
  return regexp.test(url);    
}
String.prototype.repeat = function( num ) {
	if (isNaN(num)) {
		return '';
	}
  return new Array( num + 1 ).join( this );
}

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function setCaretAtEnd(id){
	var inputField = document.getElementById(id);
	if (inputField != null && inputField.value.length != 0){
	  if (inputField.createTextRange){
      var FieldRange = inputField.createTextRange();
      FieldRange.moveStart('character',inputField.value.length);
      FieldRange.collapse();
      FieldRange.select();
	  }else if (inputField.selectionStart || inputField.selectionStart == '0') {
      var elemLen = inputField.value.length;
      inputField.selectionStart = elemLen;
      inputField.selectionEnd = elemLen;
      inputField.focus();
	  }
	}else{
    inputField.focus();
	}
}

function sorotButton(e, type, interval) {
	if (type === undefined) {
		type = "btn-inverse";
	}
	if (interval === undefined) {
		interval = 3000;
	}
	
	$(e).addClass(type);
	
	$(e).animate({
		opacity: 0.25,
	}, 1000, function() {
		$(e).animate({
			opacity: 1,
		}, 1000, function() {
			$(e).animate({
				opacity: 1,
			}, interval, function() {
				$(e).removeClass(type);
			});
		});	
	});
}

function toSlug(str) {
	return str.toLowerCase().replace(/ /g,'-').replace(/[-]+/g, '-').replace(/[^\w-]+/g,'');
}

function isValidAddress(str, rex) {
	var rex_ipv4 = /(^\s*((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))\s*$)/;
	var rex_ipv6 = /(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$)/;
	var rex_hostname = /(^\s*((?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?)*\.?)\s*$)/;
	if (rex==undefined) { rex = 'all'; }
	var ret = false;
	if (rex == 'all' || rex == 'ip' || rex == 'ipv4' ) {
		if (rex_ipv4.test(str))	{
			ret = true;
		}
	}
	if (rex == 'all' || rex == 'ip' || rex == 'ipv6' ) {
		if (rex_ipv6.test(str))	{
			ret = true;
		}
	}
	if (rex == 'all' || rex == 'hostname' ) {
		if (rex_hostname.test(str))	{
			ret = true;
		}
	}
	return ret;
}