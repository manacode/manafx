<?php
namespace Manafx\Models;

/**
 * Manafx\Models\User_Roles
 * All the role levels in the application. Used in conjenction with ACL lists
 */
class User_Roles extends \ManafxModel
{
    /**
     *
     * @var integer
     */
	public $role_id;
	public $role_name;
	public $role_status;		// A=Active, R=Reserved, S=System, X=Disabled
	public $role_description;
	
}