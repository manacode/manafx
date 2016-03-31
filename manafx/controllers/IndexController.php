<?php

namespace Manafx\Controllers;

class IndexController extends \ManafxController {
	
	public function indexAction() {
		// $this->view->cleanTemplateBefore();
	}
  public function logoutAction() {
    $this->auth->remove();
    return $this->response->redirect('/');
  }

}