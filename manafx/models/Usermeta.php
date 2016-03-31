<?php
namespace Manafx\Models;

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Manafx\Models\Usermeta
 * All the users meta configuration
 */
class Usermeta extends \ManafxModel
{
	
    /**
     *
     * @var integer
     */
  public $usermeta_id;
  public $usermeta_user_id;
  public $usermeta_key;
  public $usermeta_value;

	protected $fieldAlias = array(
		"usermeta_id" => "Meta ID",
		"usermeta_user_id" => "User ID",
		"usermeta_key" => "Meta Key",
		"usermeta_value" => "Meta Value",
	);

  public function initialize()
  {
  	$this->useDynamicUpdate(true);
  }
  

	public function beforeValidation() {
		if ($this->usermeta_value=="") {
			$this->skipAttributesOnUpdate(array('usermeta_value'));
		}
	}

	public function getMessages($filter=NULL)
	{
		$messages = array();
		foreach (parent::getMessages() as $message) {
	    switch ($message->getType()) {
	      case 'InvalidCreateAttempt':
	        $messages[] = $this->t->_('cannot_create_exists', 'The record cannot be created because it already exists');
	        break;
	      case 'InvalidUpdateAttempt':
	        $messages[] = $this->t->_('cannot_update_exists', 'The record cannot be updated because it already exists');
	        break;
	      case 'PresenceOf':
	    		$fieldName = $message->getField();
	        $messages[] = $this->fieldAlias[$fieldName] . ' ' . $this->t->_('is required');
	        break;
	      case 'Unique':
	    		$fieldName = $message->getField();
	        $messages[] = $this->fieldAlias[$fieldName] . ' ' . $this->t->_('already used');
	        break;
	    }
		}
		return $messages;
	}


}