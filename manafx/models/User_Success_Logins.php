<?php
namespace Manafx\Models;

/**
 * SuccessLogins
 * This model registers successfull logins registered users have made
 */
class User_Success_Logins extends \ManafxModel
{

    /**
     *
     * @var integer
     */
    public $success_login_id;

    /**
     *
     * @var integer
     */
    public $success_login_user_id;

    /**
     *
     * @var string
     */
    public $success_login_ip_address;

    /**
     *
     * @var integer
     */
    public $success_login_user_agent_id;

    public function initialize()
    {
        $this->belongsTo('success_login_user_id', 'Manafx\Backend\Models\Users', 'user_id', array(
            'alias' => 'user'
        ));
    }
}
