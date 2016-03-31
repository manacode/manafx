<?php
namespace Manafx\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Manafx\Models\User_Roles;

class UsersForm extends \ManafxForm
{

  public function initialize($entity = null, $options = null)
  {
  	$purpose = "add";
  	if (isset($options['purpose'])) {
  		$purpose = $options['purpose'];
 		}
 		
		$user_id = new Hidden('user_id');
		$this->add($user_id);
    
		/* firstname */
    $user_firstname = new Text('user_firstname', array(
    	'class' => 'form-control',
			'placeholder' => 'First Name'
    ));
    $this->add($user_firstname);

		/* lastname */
    $user_lastname = new Text('user_lastname', array(
    	'class' => 'form-control',
			'placeholder' => 'Last Name'
    ));
    $this->add($user_lastname);

		/* email */
    $user_email = new Text('user_email', array(
    	'class' => 'form-control',
        'placeholder' => 'Email'
    ));
    $user_email->addValidators(array(
        new PresenceOf(array(
            'message' => 'The e-mail is required'
        )),
        new Email(array(
            'message' => 'The e-mail is not valid'
        )),
		));
    $this->add($user_email);

		/* username */
    $user_username = new Text('user_username', array(
    	'class' => 'form-control',
			'placeholder' => 'Username'
    ));
    $user_username->addValidators(array(
        new PresenceOf(array(
            'message' => 'The username is required'
        ))
    ));
    $this->add($user_username);


		$passValidation = true;
		if ($purpose=="edit") {
			$datane = $options["datane"];
			if ($datane["user_password"] == "" && $datane["confirm_pass"] == "") {
				$passValidation = false;
			}
		}

		/* password */
    $user_password = new Password('user_password', array(
    	'class' => 'form-control',
			'placeholder' => 'Password',
			'type' => 'password'
    ));
    if ($passValidation) {
      $user_password->addValidators(array(
        new PresenceOf(array(
            'message' => 'Password is required'
        )),
        new StringLength(array(
            'min' => 2,
            'messageMinimum' => 'Password is too short. Minimum 2 characters'
        )),
        new Confirmation(array(
            'message' => 'Password doesn\'t match confirmation',
            'with' => 'confirm_pass'
        ))
      ));
    }
    $this->add($user_password);

		/* confirm password */
    $confirm_pass = new Password('confirm_pass', array(
    	'class' => 'form-control',
			'placeholder' => 'Confirm Password',
			'inputtype' => 'password'
    ));
    if ($passValidation) {
      $confirm_pass->addValidators(array(
        new PresenceOf(array(
            'message' => 'The confirmation password is required'
        ))
      ));
    }
    $this->add($confirm_pass);


		/* user roles */
    #$this->add(new Select('user_roles', User_Roles::find('role_status = "A" OR role_status = "S" AND role_id<>1'), array(
    $this->add(new Select('user_roles', $this->view->roles, array(
    	'name' => 'user_roles[]',
    	'multiple' => 'multiple',
    	'class' => 'form-control',
      'using' => array(
          'role_id',
          'role_name'
      ),
      'useEmpty' => false,
      'emptyText' => '...',
      'emptyValue' => '',
      'value' => array($this->view->default_role),
      'defaultValue' => $this->view->default_role
    )));

		/* user status */
    $this->add(new Select('user_status', array(
        'A' => 'Active',
        'B' => 'Banned',
        'L' => 'Locked',
        'X' => 'Activation',
    	),
			array(
				'class' => 'form-control',
				'useEmpty' => false,
				'value' => 'A',
		)));

  }
}
