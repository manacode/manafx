<?php
namespace Manafx\Controllers;

use Manafx\Forms\LoginForm as LoginForm;

class AdminController extends \ManafxAdminController {
	
	public function initialize()
	{
		parent::initialize();
		$this->view->setTemplateBefore('session');
	}
	
	public function _actions_permissions()
	{
		return array(
			"loginAction" => array("*"),
			"logoutAction" => array("*"),
			"recoverAction" => array("*")
		);
	}

	public function accessDeniedAction()
	{
		echo "ACCESS DENIED";
	}

	public function loginAction()
	{
		$this->is_sidebar = false;
		$redirect_to = $this->adminurl;
		if ($this->session->has("auth-i")) {
			return $this->response->redirect($redirect_to);
		}
		
		$form = new LoginForm();

		try {
	    if ($this->request->isPost()) {
	      if ($form->isValid($this->request->getPost()) == false) {
	        foreach ($form->getMessages() as $message) {
	          $this->flashSession->error($message);
	        }
				} else {
	        $user_id = $this->auth->login(array(
	          'username' => $this->request->getPost('username'),
	          'password' => $this->request->getPost('password'),
	          'remember' => $this->request->getPost('remember')
	        ));

					// HOOK after success login
					foreach($this->config->application->active_modules as $active_module) {
						$func = $active_module . "_successLogin";
						if (function_exists($func)) {
							call_user_func_array($func, array($this, $user_id));
						}
					}
					// End Hook
					if ($this->session->has("redirect_to")) {
						$redirect_to = $this->session->get("redirect_to");
	        }
        	return $this->response->redirect($redirect_to);
	      }
	    } else {
	      if ($this->auth->hasRememberMe()) {
	        return $this->auth->loginWithRememberMe($redirect_to);
	      }
	    }
		} catch (\Exception $e) {
	    $this->flashSession->error($e->getMessage());
		}
		unset($_POST['csrf']);
		$this->view->form = $form;
		$this->view->adminInfobar = false;
	}

  public function logoutAction()
  {
    $this->auth->remove();
    return $this->response->redirect(ADMIN_ROUTE);
  }

  /**
   * Recover password
   */
  public function recoverAction()
  {
		$form = new \Manafx\Forms\RecoverPasswordForm();
		if ($this->request->isPost()) {
			if ($form->isValid($this->request->getPost()) == false) {
				foreach ($form->getMessages() as $message) {
					$this->flash->error($message);
				}
			} else {
				$user = \Manafx\Models\Users::findFirstByUser_email($this->request->getPost('user_email'));
				if (!$user) {
					$this->flash->success('There is no account associated to this email');
				} else {
					$resetPassword = new \Manafx\Models\User_Password_Resets();
					$resetPassword->password_reset_user_id = $user->user_id;
					if ($resetPassword->save()) {
						$this->flashSession->success('Success! Please check your messages for an email reset password');
					} else {
						foreach ($resetPassword->getMessages() as $message) {
							$this->flashSession->error($message);
						}
					}
				}
			}
		}
		$this->view->form = $form;
  }

}