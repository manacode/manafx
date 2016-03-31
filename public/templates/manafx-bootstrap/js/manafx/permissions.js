jQuery(document).ready(function ($) {
  "use strict";
  $('.check-all').click(function() {
  	var dataToggle = "#" + $(this).attr("data-toggle");
    if ($(this).prop('checked')) {
      $('.permission_checkbox', dataToggle).not(":disabled").attr('checked',true);
      $('.permission_checkbox', dataToggle).not(":disabled").prop('checked',true);
    } else {
      $('.permission_checkbox', dataToggle).not(":disabled").attr('checked',false);
      $('.permission_checkbox', dataToggle).not(":disabled").prop('checked',false);
    }
  });
  $('.permission_checkbox').click(function() {
  	var dataToggle = $(this).attr("data-toggle");
  	var dataToggleID = "#" + dataToggle;
    var nchecked=0;
    var nunchecked=0;
    $('.permission_checkbox', dataToggleID).each(function(index) {
      if ($(this).is(':checked')) {
        nchecked++;
      } else {
      	nunchecked++;
     	}
    });
    if (nunchecked>0) {
    	$('.check-all[data-toggle=' + dataToggle + ']').attr('checked',false);
    	$('.check-all[data-toggle=' + dataToggle + ']').prop('checked',false);
   	} else {
    	$('.check-all[data-toggle=' + dataToggle + ']').attr('checked',true);
    	$('.check-all[data-toggle=' + dataToggle + ']').prop('checked',true);
 		}
  });
});