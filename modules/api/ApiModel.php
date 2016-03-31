<?php

class ApiModel extends \ManafxModel
{
	function getConnectionService() {
		return "dbApi";
	}

	public function getSource() {
    return "fx_" . $this->getClassName();
	}

}