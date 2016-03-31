<?php
namespace Manafx\Models;

class Options extends \ManafxModel
{
	public $option_id;
	public $option_name;
	public $option_value;
	public $option_autoload;
	public $option_identity;
	public $option_description;

	public function beforeUpdate()
	{
    $this->option_value = ifneeded_serialize($this->option_value);
	}

	public function afterFetch()
	{
    # $this->option_value = ifneeded_unserialize($this->option_value);
	}

}