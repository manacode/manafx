jQuery(document).ready(function ($) {
  "use strict";

	var isMobile = window.matchMedia("screen and (max-width: 767px)");
	if (isMobile.matches) {
		sidebarToggle("off");
	} else {
		sidebarToggle($.cookie('sbT'));
	}

  $('#sidebar').perfectScrollbar({
  	suppressScrollX: true,
  	wheelSpeed: 20,
	  wheelPropagation: false,
	  minScrollbarLength: 100
	});
	
	$('#sidebar').button().click(function() {
		$('#sidebar').perfectScrollbar('update');
	});

	$('#sidebar-toggler').click(function() {
		sidebarToggle();
	});

	if ($('.footable').footable) {
		$('.footable').footable();
	  $('.clear-filter').click(function (e) {
	    e.preventDefault();
	    $('table.footable').trigger('footable_clear_filter');
	  });
  }

	if ($('.footable').footable) {
		
	  $('#change-page-size').button().click(function (e) {
	  	e.preventDefault();
			var pageSize = $('#page-size').val();
			if (isNumber(pageSize)) {
				$('.footable').data('page-size', pageSize);
				$('.footable').trigger('footable_initialized');
			} else {
				$('#page-size').popover('show');
			}
		});
		
	  $('#page-size').focus(function (e) {
	  	e.preventDefault();
			$('#page-size').popover('hide');
		});
		
  }

/*************************************************
	Datagrid Check All
*************************************************/
  $('#check-all').click(function() {
    if ($(this).prop('checked')) {
      $('.datagrid .check-rows').attr('checked',true);
      $('.datagrid .check-rows').prop('checked',true);
      $('.req-multi-item').removeAttr('disabled');
      $('.req-single-item').attr('disabled','disabled');
      $('.req-any-item').removeAttr('disabled');
    } else {
      $('.datagrid .check-rows').attr('checked',false);
      $('.datagrid .check-rows').prop('checked',false);
      $('.req-multi-item').attr('disabled','disabled');
      $('.req-single-item').attr('disabled','disabled');
      $('.req-any-item').attr('disabled','disabled');
    }
  });

  $('.datagrid .check-rows').click(function() {
    var nchecked=0;
    var nunchecked=0;
    $('.datagrid .check-rows').each(function(index) {
      if ($(this).is(':checked')) {
        nchecked++;
      } else {
      	nunchecked++;
      }
    });
    if (nchecked<=0) {
      $('.req-multi-item').attr('disabled','disabled');
      $('.req-single-item').attr('disabled','disabled');
      $('.req-any-item').attr('disabled','disabled');
    }
    if (nchecked==1) {
      $('.req-multi-item').attr('disabled','disabled');
      $('.req-single-item').removeAttr('disabled');
      $('.req-any-item').removeAttr('disabled');
    }
    if (nchecked>1) {
      $('.req-multi-item').removeAttr('disabled');
      $('.req-single-item').attr('disabled','disabled');
      $('.req-any-item').removeAttr('disabled');
    }
    if (nunchecked>0) {
	    $('#check-all', 'table').attr('checked',false);
	    $('#check-all', 'table').prop('checked',false);
   	} else {
	    $('#check-all', 'table').attr('checked',true);
	    $('#check-all', 'table').prop('checked',true);
		}
  });

});

function sidebarToggle(toggle) {
	var isMobile = window.matchMedia("only screen and (max-width: 767px)");
	var eT = $('#sidebar-toggler');
	var dT = $(eT.attr("data-target"));
	var class_on = $('span', eT).attr("class-on");
	var class_off = $('span', eT).attr("class-off");

	// eT.hide();
	if ((toggle === undefined && eT.hasClass("sidebar-on")) || toggle=="off") {
		$.cookie('sbT', "off", {path: '/'});
		eT.removeClass("sidebar-on");
		eT.addClass("sidebar-off");
		dT.removeClass("sidebar-on");
		dT.addClass("sidebar-off");
		$('span', eT).removeClass(class_on);
		$('span', eT).addClass(class_off);
		$('#sidebar').hide();
		$('.infobar').css('left', '0');
		eT.css('left', '0');
		dT.css('margin-left', '5px');
		dT.css('margin-right', '5px');
	} else {
		$('#sidebar').hide();
		$.cookie('sbT', "on", {path: '/'});
		eT.removeClass("sidebar-off");
		eT.addClass("sidebar-on");
		dT.removeClass("sidebar-off");
		dT.addClass("sidebar-on");
		$('span', eT).removeClass(class_off);
		$('span', eT).addClass(class_on);
		$('#sidebar').show();

		eT.css('left', $('#sidebar').outerWidth());
		$('.infobar').css('left', $('#sidebar').outerWidth());
		dT.css('margin-left', $('#sidebar').outerWidth());
		dT.css('margin-right', '0px');
		// $('.infobar').css('margin-right', '20px');
	}
	if (isMobile.matches) {
		$('.infobar').css('left', '0');
		dT.css('margin-left', '-5px');
		dT.css('margin-right', '-5px');
	}
	eT.show();
}

function sorotMe(e, type, interval) {
	if (type === undefined) {
		type = "success";
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

// remove table rows by data-id attribute
function removeRows(ids) {
	var footable = $('table').data('footable');
	if (!$.isArray(ids)) {
		ids = ids.split(",");
	}
	for (i = 0; i < ids.length; i++) {
		row = $("tr[data-id='" + ids[i] + "']");
		footable.removeRow(row);
	}
}

function searchText(container, el, search_text) {
	var ret = false;
  container.each(function(){
		if($(this).find(el).text() == search_text){
			ret = $(this).find(el).text()
	  }
  });
	return ret;
}

/**
 * bootstrap-select helper
 **/
 
function refreshBSOption(bsEl, setVal) {
	if (bsEl == undefined || bsEl=="_auto_") {
		bsEl = $('.selectpicker');
	}
	showBSOption(bsEl);
	bsEl.selectpicker('refresh');
	if (setVal !== undefined) {
		bsEl.selectpicker('val', setVal);
	}
}
function showBSOption(bsEl) {
	if (bsEl == undefined || bsEl=="_auto_") {
		bsEl = $('.selectpicker');
	}
	bsEl.find('option.hide').each(function() {
		idx = $(this).index();
	  $(this).removeClass("hide");
	  // $('.bootstrap-select ul li[data-original-index="' + idx + '"]').removeClass("hide");
	});
	bsEl.selectpicker('refresh');
	bsEl.selectpicker('render');
}

function hideBSOption(bsEl, optionValue) {
	if (bsEl == undefined || bsEl=="_auto_") {
		bsEl = $('.selectpicker');
	}
	el = bsEl.find('option[value="' + optionValue + '"]');
	idx = el.index();
  el.addClass("hide");
  // $('.bootstrap-select ul li[data-original-index="' + idx + '"]').addClass("hide");
	bsEl.selectpicker('refresh');
	bsEl.selectpicker('render');
}

function addItemBSOption(bsEl, attributes, htmlText) {
	if (bsEl == undefined || bsEl=="_auto_") {
		bsEl = $('.selectpicker');
	}

	var newItem = "<option";
	for (var key in attributes) { 	
		newItem += ' ' + key + '="' + attributes[key] + '"';
	}
	newItem += ">";
	newItem += htmlText;
	newItem += "</option>";
	bsEl.append(newItem);
	bsEl.selectpicker('refresh');
	bsEl.selectpicker('render');
}