<?php
namespace Manafx\Api\Models;

/**
 * Manafx\Models\Categories
 */
class Api extends \ApiModel
{
  /**
   *
   * @var integer
   */
	public $user_id;
	public $api_key;
	public $ip_address;

	public function beforeValidation() {
    $this->ip_address = CSVImplode($this->ip_address, '__IPLIST__');
	}
	public function afterFetch() {
    $this->ip_address = CSVExplode($this->ip_address, '__IPLIST__');
	}    

}