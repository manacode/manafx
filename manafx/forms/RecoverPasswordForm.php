<?php
namespace Manafx\Forms;

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;

class RecoverPasswordForm extends \ManafxForm
{
	public function initialize()
	{
		$user_email = new Text('user_email', array(
			'placeholder' => 'Email'
		));
		$user_email->addValidators(array(
			new PresenceOf(array(
				'message' => 'Email is required'
			)),
			new Email(array(
				'message' => 'Email is not valid'
			))
		));
		$this->add($user_email);
		
		$this->add(new Submit('Send', array(
			'class' => 'btn btn-primary'
		)));

    // CSRF
    // $csrf = new Hidden('csrf');
    $csrf = new Hidden('csrf');

    $csrf->addValidator(new Identical(array(
      'value' => $this->security->getSessionToken(),
      'message' => 'CSRF validation failed'
    )));

    $this->add($csrf);
	}
}
