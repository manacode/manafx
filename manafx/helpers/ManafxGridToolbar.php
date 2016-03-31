<?php
/**
 * GridToolbar
 *
 * Helps to build toolbar for datagrid
 */

use Manafx\Models\Permissions;

class ManafxGridToolbar extends \Phalcon\Mvc\User\Component
{
	public function output()
	{
		$this->view->start();
		$this->view->partial('../manafx/partials/gridToolbar', array(
		));
		$html = ob_get_contents();
		$this->view->finish();
		return $html;
	}
}