<?php
namespace Manafx\Models;

/**
 * FailedLogins
 * This model registers unsuccessfull logins registered and non-registered users have made
 */
class User_Failed_Logins extends \ManafxModel
{
    /**
     *
     * @var integer
     */
    public $failed_login_id;

    /**
     *
     * @var integer
     */
    public $failed_login_user_id;

    /**
     *
     * @var string
     */
    public $failed_login_ip_address;

    /**
     *
     * @var integer
     */
    public $failed_login_time;
    
    /**
     *
     * @var integer
     */
    public $failed_login_user_agent_id;

    public function initialize()
    {
        $this->belongsTo('failed_login_user_id', 'Manafx\Models\Users', 'user_id', array(
            'alias' => 'user'
        ));
    }
}
