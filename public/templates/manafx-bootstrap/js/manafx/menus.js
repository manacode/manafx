function getHtmlStatus(status) {
	hret = status;
	switch (status) {
		case "A":
			hret = '<span class="text-success">Active</span>';
			break;
		case "X":
			hret = '<strong class="text-danger">Disabled</strong>';
			break;
	}
	return hret;
}

function moveRowAfter(row2move, rowtarget) {
	row2move.insertAfter(rowtarget);
	childId = row2move.attr("data-id");
	if ($('table tbody tr[data-parent=' + childId + ']')) {
		moveChildRow(childId, row2move);
	}
}

function moveChildRow(rowId, rowtarget) {
	$('table tbody tr[data-parent=' + rowId + ']').each(function() {
		$(this).insertAfter(rowtarget);
		childId = $(this).attr("data-id");
		if ($('table tbody tr[data-parent=' + childId + ']')) {
			moveChildRow(childId, $(this));
		}
	});
}

function getParentRow(parentId) {
	return $('table tbody tr[data-id=' + parentId + ']');
}

function getRowLevel(dataId) {
	return Number($('table tbody tr[data-id=' + dataId + ']').attr("data-level"));
}