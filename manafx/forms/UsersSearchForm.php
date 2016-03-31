<?php
namespace Manafx\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Manafx\Models\User_Roles;

class UsersSearchForm extends \ManafxForm
{
  public function initialize($entity = null, $options = null)
  {
    $user_id = new Text('user_id', array(
    	'id' => 'user_id',
    	'name' => 'user_id',
			'class' => 'form-control',
			'placeholder' => 'User Id'
		));
    $this->add($user_id);

    $user_firstname = new Text('user_firstname', array(
    	'id' => 'user_firstname',
    	'name' => 'user_firstname',
    	'class' => 'form-control',
			'placeholder' => 'First Name'
    ));
    $this->add($user_firstname);
    
    $user_lastname = new Text('user_lastname', array(
    	'id' => 'user_lastname',
    	'name' => 'user_lastname',
    	'class' => 'form-control',
			'placeholder' => 'Last Name'
    ));
    $this->add($user_lastname);

    $user_email = new Text('user_email', array(
    	'id' => 'user_email',
    	'name' => 'user_email',
    	'class' => 'form-control',
			'placeholder' => 'Email'
    ));
    $this->add($user_email);

    $user_login = new Text('user_username', array(
    	'id' => 'user_username',
    	'name' => 'user_username',
    	'class' => 'form-control',
			'placeholder' => 'Username'
    ));
    $this->add($user_login);

    $this->add(new Select('user_roles', User_Roles::find('role_status != "X" AND role_status != "R" AND role_id != 1 AND role_id != 10'), array(
    #$this->add(new Select('user_roles', array(
    	// 'name' => '_ur_role_id[]',
    	'name' => 'roles[]',
    	'multiple' => 'multiple',
    	'class' => 'form-control',
      'using' => array(
          'role_id',
          'role_name'
      ),
      'useEmpty' => false,
      'emptyText' => '',
      'emptyValue' => ''
    )));



  }
}