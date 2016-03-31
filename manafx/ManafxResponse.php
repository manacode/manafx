<?php

class ManafxResponse extends \Phalcon\Http\Response {

	public function session_redirect() {
		if ($this->getDI()->getSession()->has("redirect_to")) {
			$this->redirect($this->getDI()->getSession()->get("redirect_to"));
		}
	}

}