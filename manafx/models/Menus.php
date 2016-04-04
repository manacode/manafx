<?php
namespace Manafx\Models;

/**
 * Manafx\Models\Menus
 * All the menu levels in the application.
 */
class Menus extends \ManafxModel
{
  /**
   *
   * @var integer
   */
	public $menu_id;
	public $menu_parent;
	public $menu_type;
	public $menu_key;
	public $menu_title;
	public $menu_action;
	public $menu_roles;
	public $menu_status;
	public $menu_description;

	public function beforeValidation() {
    $this->menu_roles = CSVImplode($this->menu_roles);		
	}
	// note: afterFetch is not working for .toArray()
	public function afterFetch() {
    $this->menu_roles = CSVExplode($this->menu_roles);
	}    
	
}