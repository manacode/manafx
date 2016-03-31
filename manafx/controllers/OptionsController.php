<?php

namespace Manafx\Controllers;
use Manafx\Models\Options;

class OptionsController extends \ManafxAdminController {

	public function indexAction()
	{
		$this->auth->ced();
		$options = Options::find();
		$this->view->data_options = $options;
		
		$identities = $this->scan_Modules();
		array_push($identities, "-", "public", "system");
		$this->view->identities = $identities;
	}

	public function getOptionAction($option_id="")
	{
		if ($this->request->isPost()) {
			$option_id = $this->request->getPost('option_id');
		}
	  # $option = Options::findFirstByoption_id($option_id);
	  $option = Options::findFirst("option_id = '" . $option_id . "'");
	  if (!$option) {
    	$success = "0";
      $msg = "Option was not found";
    	$return = array("status" => $success, "msg" => $msg);
	  } else {
    	$success = "1";
	  	$data = $option;
    	$return = array("status" => $success, "data" => $data);
  	}
    echo json_encode($return);
	}

	private function isExists($field, $value)
	{
	  $option = Options::findFirst("$field = '" . $value . "'");
	  if (!$option) {
	  	return false;
	  } else {
	  	return true;
  	}
	}
	
	public function createOptionAction()
	{
	  if ($this->request->isPost()) {
			$purpose =  $this->request->getPost('purpose', 'striptags');
			$option_name = $this->request->getPost('option_name', 'striptags');
			
			if ($this->isExists("option_name", $option_name)) {
				$return = array("status" => "0", "msg" => "Option name already exists!");
				echo json_encode($return);
				return;
			}
			$option = new Options();

	    $option->assign(array(
        'option_name' => $option_name,
        'option_value' => $this->request->getPost('option_value', 'striptags'),
        'option_autoload' => $this->request->getPost('option_autoload'),
        'option_identity' => $this->request->getPost('option_identity', 'striptags'),
        'option_description' => $this->request->getPost('option_description', 'striptags')
	    ));
	
	    if (!$option->save()) {
	    	$status = "0";
	    	$msg = '';
				foreach ($option->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
		    $return = array("status" => $status, "msg" => $msg);
	    } else {
	    	$status = "1";
   			$option_id = $option->option_id;
      	$msg = "New option . `" . $option_name . "` was saved successfully";
		    $return = array("status" => $status, "msg" => $msg, "option_id" => $option_id);
	    }
	    echo json_encode($return);
	  }
	}

	public function updateOptionAction()
	{
	  if ($this->request->isPost()) {
			$purpose =  $this->request->getPost('purpose', 'striptags');
			$option_name = $this->request->getPost('option_name', 'striptags');
			
			$option_id = $this->request->getPost('option_id');
			$option = Options::findFirst("option_id = '" . $option_id . "'");
			
	    $option->assign(array(
        'option_name' => $option_name,
        'option_value' => $this->request->getPost('option_value', 'striptags'),
        'option_autoload' => $this->request->getPost('option_autoload'),
        'option_identity' => $this->request->getPost('option_identity'),
        'option_description' => $this->request->getPost('option_description', 'striptags')
	    ));
	
	    if (!$option->save()) {
	    	$success = "0";
	    	$msg = '';
				foreach ($option->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
	    } else {
	    	$success = "1";
      	$msg = "Option was updated successfully";
	    }
	    $return = array("status" => $success, "msg" => $msg, "option_id" => $option_id);
	    echo json_encode($return);
	  }
	
	}

  public function deleteOptionsAction($option_id="")
  {
		if ($this->request->isPost()) {
			$option_id = $this->request->getPost('option_id');
		}
		if (is_array($option_id)) {
			$option_id = implode(",", $option_id);
		}
    $option = Options::find("option_id IN ($option_id)");
		if ($option->delete() == false) {
			$success = "0";
			$msg = "Sorry, we can't delete the option(s) right now: \n";
			foreach ($option->getMessages() as $message) {
				$msg .= $message . "\n";
			}
		} else {
			$success = "1";
			$msg = "The option(s) was deleted successfully!";
		}
		$return = array("status" => $success, "msg" => $msg);
		echo json_encode($return);

  }

	
}