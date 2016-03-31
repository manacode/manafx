<?php
namespace Manafx\Models;

/**
 * User Password Resets
 * Stores the reset password codes
 */
class User_Password_Resets extends \ManafxModel
{

    /**
     *
     * @var integer
     */
    public $password_reset_id;

    /**
     *
     * @var integer
     */
    public $password_reset_user_id;

    /**
     *
     * @var string
     */
    public $password_reset_code;

    /**
     *
     * @var integer
     */
    public $password_reset_created;

    /**
     *
     * @var integer
     */
    public $password_reset_modified;

    /**
     *
     * @var string
     */
    public $password_reset_status;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
	    // Timestamp the confirmaton
	    $this->password_reset_created = time();
	
	    // Generate a random confirmation code
	    $this->password_reset_code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));
	
	    // Set status to non-confirmed
	    $this->password_reset_status = 'N';
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
	    // Timestamp the confirmaton
	    $this->password_reset_modified = time();
    }

    /**
     * Send an e-mail to users allowing him/her to reset their password
     */
    public function afterCreate()
    {
	    $this->getDI()
	        ->getMail()
	        ->send(array(
	        $this->user->user_email => $this->user->name
	    ), "Reset your password", 'reset', array(
	        'resetUrl' => '/reset-password/' . $this->code . '/' . $this->user->email
	    ));
    }

    public function initialize()
    {
	    $this->belongsTo('password_reset_user_id', 'Manafx\Models\Users', 'user_id', array(
		    'alias' => 'user'
	    ));
    }
}
