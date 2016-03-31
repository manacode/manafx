<?php
namespace Manafx\Models;

/**
 * User Password Changes
 * Register when a user changes their password
 */
class User_Password_Changes extends \ManafxModel
{

    /**
     *
     * @var integer
     */
    public $password_change_id;

    /**
     *
     * @var integer
     */
    public $password_change_user_id;

    /**
     *
     * @var string
     */
    public $password_change_ip_address;

    /**
     *
     * @var integer
     */
    public $password_change_user_agent_id;

    /**
     *
     * @var integer
     */
    public $password_change_created;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
	    // Timestamp the confirmaton
	    $this->password_change_created = time();
    }

    public function initialize()
    {
	    $this->belongsTo('password_change_user_id', 'Manafx\Models\Users', 'user_id', array(
	      'alias' => 'user'
	    ));
    }
}
