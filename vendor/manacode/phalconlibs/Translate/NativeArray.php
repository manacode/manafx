<?php
/*
  +------------------------------------------------------------------------+
  | ManaCode Phalcon Library                                               |
  +------------------------------------------------------------------------+
  | Copyright (c) 2012-2016 manacode (https://github.com/manacode)         |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.                                 |
  |                                                                        |
  +------------------------------------------------------------------------+
  | Authors: Leonardus Agung <mannacode@gmail.com>                      |
  |                                                                        |
  +------------------------------------------------------------------------+
*/

namespace Manacode\Phalcon\Translate;

class NativeArray extends \Phalcon\Mvc\User\Component
{
	protected $_translate = array();

	/**
	 * Returns the translation related to the given key
	 */
	public function _($translateKey, $defaultTranslation = "", $placeholders = null)
	{
		$translation = $translateKey;
		
		if (is_array($defaultTranslation) && $placeholders === null) {
			$placeholders = $defaultTranslation;
		} else {
			if ($defaultTranslation!="") {
				$translation = $defaultTranslation;
			}
		}
		if ($this->exists($translateKey)) {
			$translation = $this->_translate[$translateKey];
		}
		return $this->replacePlaceholders($translation, $placeholders);
	}

	/**
	 * Check whether is defined a translation key in the internal array
	 */
	public function exists($translateKey)
	{
		return isset($this->_translate[$translateKey]);
	}

	public function replacePlaceholders($translation, $placeholders)
	{
		if (is_array($placeholders) && !empty($placeholders)) {
			foreach ($placeholders as $key => $val) {
				$translation = str_replace("%" . $key . "%", $val, $translation);
			}
		}
		return $translation;
	}

	/**
	 * Set translation data
	 * 
	 * @param array $translation translation data
	 */
	public function setTranslation($translation)
	{
		if (is_array($translation)) {
			$this->_translate = array_merge($this->_translate, $translation);
		}
  }

}