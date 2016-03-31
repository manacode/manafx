<?php
namespace Manafx\Models;

/**
 * Permissions
 * Stores the permissions by role
 */
class Permissions extends \ManafxModel
{

    /**
     *
     * @var integer
     */
    public $permission_id;

    /**
     *
     * @var integer
     */
    public $permission_role_id;

    /**
     *
     * @var string
     */
    public $permission_module;

    /**
     *
     * @var string
     */
    public $permission_controller;

    /**
     *
     * @var string
     */
    public $permission_action;

    public function init()
    {
        $this->belongsTo('permission_role_id', 'Manafx\Models\User_Roles', 'role_id', array(
            'alias' => 'user_roles'
        ));
    }
}
