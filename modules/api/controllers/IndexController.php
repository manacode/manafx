<?php

namespace Manafx\Api\Controllers;
use Manafx\Api\Models\Api;

class IndexController extends \ManafxAdminController {
	
	public function indexAction()
	{
		if ($this->session->has("auth-i")) {
			$identity = $this->session->get('auth-i');
		} else {
			$this->view->disable();
			$this->response->redirect(ADMIN_ROUTE);
			return;
		}
		$user_id = $identity["user_id"];
		$api = $this->getApiData($user_id);

		$this->view->api = $api;
	}
	
	function getApiData($user_id) {
	  $api = Api::findFirst(array("user_id = $user_id"));
	  if (!$api) {
			$api = new Api();
	    $api->assign(array(
	    	'user_id' => $user_id,
        'api_key' => $this->generateApiKey($user_id),
	    ));
	    if (!$api->create()) {
	    	$msg = "";
				foreach ($category->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
				$this->flashSession->error($msg);
				return false;
	    }
  	}
  	return $api;
	}
	
	function regenerateAction() {
		if ($this->session->has("auth-i")) {
			$identity = $this->session->get('auth-i');
		} else {
			$this->view->disable();
			return;
		}
		$user_id = $identity["user_id"];
		$newKey = '';
		$api = Api::findFirst(array("user_id = $user_id"));
		if ($api) {
			$newKey = $this->generateApiKey($user_id);
	    $api->assign(array(
        'api_key' => $newKey,
	    ));
	    if (!$api->update()) {
	    	$msg = "";
				foreach ($category->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
				$this->flashSession->error($msg);
	    }
		}
		if ($this->request->isAjax() == true) {
			echo $newKey;
			return;
		} else {
			return $newKey;
		}
	}

	function whitelistIpAction() {
		if ($this->session->has("auth-i")) {
			$identity = $this->session->get('auth-i');
		} else {
			$this->view->disable();
			return;
		}
		$user_id = $identity["user_id"];
		$success = 0;
		if ($this->request->isPost()) {
			$ipaddress = $this->request->getPost('ipaddress', 'striptags');
			$api = Api::findFirst(array("user_id = $user_id"));
			if ($api) {
		    $api->assign(array(
	        'ip_address' => $ipaddress,
		    ));
		    if (!$api->update()) {
		    	$msg = "";
					foreach ($category->getMessages() as $message) {
				    $msg .= $message->getMessage() . "<br/>";
					}
					if ($this->request->isAjax() == true) {
				    $return = array("status" => $success, "msg" => $msg);
					} else {
		        $this->flashSession->error($msg);
			  	}
				} else {
	    		$success = "1";
	      	$msg = $this->t->_("Whitelist IP Address was updated successfully");
					if ($this->request->isAjax() == true) {
			    	$return = array("status" => '1', "msg" => $msg);
					} else {
		        $this->flashSession->success($msg);
					}
				}
			}

			if ($this->request->isAjax() == true) {
				echo json_encode($return);
				return;
			} else {
				return $this->response->redirect(ADMIN_ROUTE . "/api");
			}
		}
	}

	function generateApiKey($uuid) {
		$crypt = new \Phalcon\Crypt();
	  $crypt->setKey(API_KEY);
	  return substr($this->auth->base62_encode($crypt->encrypt($uuid)), -32);
	}
	

	

}