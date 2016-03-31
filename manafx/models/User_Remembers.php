<?php
namespace Manafx\Models;

/**
 * Remember Tokens
 * Stores the remember me tokens
 */
class User_Remembers extends \ManafxModel
{

    /**
     *
     * @var integer
     */
    public $remember_id;

    /**
     *
     * @var integer
     */
    public $remember_user_id;

    /**
     *
     * @var string
     */
    public $remember_token;

    /**
     *
     * @var integer
     */
    public $remember_user_agent_id;

    /**
     *
     * @var integer
     */
    public $remember_created;


    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->remember_created = time();
    }

    public function initialize()
    {
        $this->belongsTo('remember_user_id', 'Manafx\Models\Users', 'user_id', array(
            'alias' => 'user'
        ));
    }
}
