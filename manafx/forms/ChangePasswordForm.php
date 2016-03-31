<?php
namespace Manafx\Forms;

use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class ChangePasswordForm extends \ManafxForm
{
  public function initialize()
  {
  	/* old_password */
	  $old_password = new Password('old_password', array(
	  	'class' => 'form-control',
			'placeholder' => 'Password',
			'type' => 'password'
	  ));
    $old_password->addValidators(array(
      new PresenceOf(array(
          'message' => 'Please enter your current password'
      )),
    ));
	  $this->add($old_password);
		
		/* new_password */
	  $user_password = new Password('user_password', array(
	  	'class' => 'form-control',
			'placeholder' => 'Password',
			'type' => 'password'
	  ));
    $user_password->addValidators(array(
      new PresenceOf(array(
          'message' => 'Please enter your new password'
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
	  $this->add($user_password);
		
		/* confirm_password */
	  $confirm_pass = new Password('confirm_pass', array(
	  	'class' => 'form-control',
			'placeholder' => 'Confirm Password',
			'inputtype' => 'password'
	  ));
    $confirm_pass->addValidators(array(
      new PresenceOf(array(
          'message' => 'The confirmation password is required'
      ))
    ));
	  $this->add($confirm_pass);
  	
  }
}
