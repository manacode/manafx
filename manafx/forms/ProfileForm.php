<?php
namespace Manafx\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

class ProfileForm extends \ManafxForm
{
  public function initialize()
  {
    $user_id = new Hidden('user_id');
    $this->add($user_id);

    $user_firstname = new Text('user_firstname', array(
    	'class' => 'form-control',
			'placeholder' => 'First Name'
    ));
    $this->add($user_firstname);

    $user_lastname = new Text('user_lastname', array(
    	'class' => 'form-control',
			'placeholder' => 'Last Name'
    ));
    $this->add($user_lastname);

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

	  $user_password = new Password('user_password', array(
	  	'class' => 'form-control',
			'placeholder' => 'Password',
			'type' => 'password'
	  ));
    $user_password->addValidators(array(
      new PresenceOf(array(
        'message' => 'Password is required'
      ))
    ));
    
	  $this->add($user_password);
    
  }
}
