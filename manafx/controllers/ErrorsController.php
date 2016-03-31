<?php

namespace Manafx\Controllers;

class ErrorsController extends \ManafxController {

	public function error404Action() {
		$this->response->setStatusCode(404, 'Not Found');
	}

	public function accessDeniedAction() {
		
	}


}