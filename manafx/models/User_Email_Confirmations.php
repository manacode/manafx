<?php
namespace Manafx\Models;

/**
 * EmailConfirmations
 * Stores user email confirmation
 */
class User_Email_Confirmations extends \ManafxModel
{

    /**
     *
     * @var integer
     */
    public $email_confirmation_id;

    /**
     *
     * @var integer
     */
    public $email_confirmation_user_id;

    public $email_confirmation_code;

    /**
     *
     * @var integer
     */
    public $email_confirmation_created;

    /**
     *
     * @var integer
     */
    public $email_confirmation_modified;

    public $email_confirmation_confirmed;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->email_confirmation_created = time();

        // Generate a random confirmation code
        $this->email_confirmation_code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Set status to non-confirmed
        $this->email_confirmation_confirmed = 'N';
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        // Timestamp the confirmaton
        $this->email_confirmation_modified = time();
    }

    /**
     * Send a confirmation e-mail to the user after create the account
     */
    public function afterCreate()
    {
        $this->getDI()
            ->getMail()
            ->send(array(
            $this->user->email => $this->user->name
        ), "Please confirm your email", 'confirmation', array(
            'confirmUrl' => '/confirm/' . $this->email_confirmation_code . '/' . $this->user->email
        ));
    }

    public function initialize()
    {
        $this->belongsTo('email_confirmation_user_id', 'Manafx\Models\Users', 'user_id', array(
            'alias' => 'user'
        ));
    }
}
