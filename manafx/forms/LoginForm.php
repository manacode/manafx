<?php
namespace Manafx\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;

class LoginForm extends \ManafxForm
{
  public function initialize()
  {
    // Username
    $username = new Text('username', array(
      'placeholder' => 'Username'
    ));
    $username->addValidators(array(
      new PresenceOf(array(
        'message' => 'The username is required'
      )),
    ));

    $this->add($username);
    
    // Password
    $password = new Password('password', array(
      'placeholder' => 'Password'
    ));

    $password->addValidator(new PresenceOf(array(
      'message' => 'Password is required'
    )));

    $this->add($password);

    // Remember
    $remember = new Check('remember', array(
      'value' => 'yes'
    ));

    $remember->setLabel('Remember me');

    $this->add($remember);

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
