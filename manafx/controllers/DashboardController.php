<?php

namespace Manafx\Controllers;

class DashboardController extends \ManafxAdminController {
	
	public function indexAction() {
		if (!$this->session->has("auth-i")) {
			$this->view->disable();
			$this->response->redirect(ADMIN_ROUTE . "/login");
			return;
		}
		
		
	}
	
}