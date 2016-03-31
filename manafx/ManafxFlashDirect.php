<?php

class ManafxFlashDirect extends \Phalcon\Flash\Direct
{
	
	public function message($type, $message)
	{
		$this->_automaticHtml = false;
		
		$alertType = $type;
		if ($type=="error") {
			$alertType = "danger";
		}
		if ($type=="notice") {
			$alertType = "info";
		}
		$classes = $type . "Message alert alert-dismissable alert-" . $alertType;
		
		$newMessage = '';
		if (is_array($message)) {
			foreach($message as $index => $mess) {
				$newMessage = '<div class="' . $classes . '">';
				$newMessage .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
				$newMessage .= $mess;
				$newMessage .= '</div>';
			}
		} else {
			$newMessage = '<div class="' . $classes . '">';
			$newMessage .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			$newMessage .= $message;
			$newMessage .= '</div>';
		}
		parent::message($type, $newMessage);
	}
}