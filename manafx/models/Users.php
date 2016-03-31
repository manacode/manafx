<?php
namespace Manafx\Models;

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Manafx\Models\Users
 * All the users registered in the application
 */
class Users extends \ManafxModel
{
	
    /**
     *
     * @var integer
     */
  public $user_id;
  public $user_firstname;
  public $user_lastname;
  public $user_email;
  public $user_username;
  public $user_password;
  public $user_status;	// A=Active, B=Banned, L=Locked, X=Activation Link
  public $user_roles;
  public $user_registered;
  public $user_activation_key;

	protected $fieldAlias = array(
		"user_id" => "User ID",
		"user_firstname" => "First Name",
		"user_lastname" => "Last Name",
		"user_email" => "E-mail",
		"user_username" => "Username",
		"user_password" => "Password",
		"user_status" => "Status",
		"user_roles" => "Roles",
		"user_registered" => "Registered",
		"user_activation_key" => "Activation Key"
	);

	public function beforeValidation() {
    $this->user_roles = CSVImplode($this->user_roles);
    
    $skipAttributesOnUpdate = array();
		if ($this->user_password=="") {
			$skipAttributesOnUpdate[] = 'user_password';
		}
		
		if ($this->user_registered=="") {
			$skipAttributesOnUpdate[] = 'user_registered';
		}
		if ($this->user_activation_key=="") {
			$skipAttributesOnUpdate[] = 'user_activation_key';
		}
		if (!empty($skipAttributesOnUpdate)) {
			$this->skipAttributesOnUpdate($skipAttributesOnUpdate);
		}
		
	}
	
	public function afterFetch() {
    $this->user_roles = CSVExplode($this->user_roles);
	}    

	public function validation()
	{

		$this->validate(new EmailValidator(array(
			'field' => 'user_email',
			'message' => 'E-maile is not Valid!'
		)));
/*		
		$this->validate(new PresenceOf(array(
			'field' => 'user_password',
			'message' => 'Password is required!'
		)));
*/
		$this->validate(new Uniqueness(array(
		    'field'   => 'user_username',
		    'message' => 'Username already used!'
		  )));
		  
		$this->validate(new Uniqueness(array(
		    'field'   => 'user_email',
		    'message' => 'E-mail already Eexists!'
		  )));

		if ($this->validationHasFailed() == true) {
			return false;
		}
	}

	public function getMessages($filter=NULL)
	{
		$messages = array();
		foreach (parent::getMessages() as $message) {
	    switch ($message->getType()) {
	      case 'InvalidCreateAttempt':
	        $messages[] = 'The record cannot be created because it already exists';
	        break;
	      case 'InvalidUpdateAttempt':
	        $messages[] = 'The record cannot be updated because it already exists';
	        break;
	      case 'PresenceOf':
	    		$fieldName = $message->getField();
	        $messages[] = $this->fieldAlias[$fieldName] . ' ' . $this->getDI()->get('t')->_('is required');
	        break;
	      case 'Unique':
	    		$fieldName = $message->getField();
	        $messages[] = $this->fieldAlias[$fieldName] . ' ' . $this->getDI()->get('t')->_('already used');
	        break;
	    }
		}
		return $messages;
	}


}