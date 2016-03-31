function getHtmlStatus($status) {
	switch ($status) {
		case "A":
			hret = '<span class="text-success">Active</span>';
			break;
		case "X":
			hret = '<strong class="text-danger">Disabled</strong>';
			break;
	}
	return hret;
}