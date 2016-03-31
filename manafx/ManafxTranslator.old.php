<?php

class Translator extends \Phalcon\Mvc\User\Component
{
	var $_lang = "en-US";
	protected $_translate = array();

	function __construct() {
		$lang = $this->_lang;
		if (isset($this->config->application->default_language)) {
			$lang = $this->config->application->default_language;
		}
		if ($lang=="auto") {
			$lang = $this->request->getBestLanguage();
		}
		if (isset($this->config->application->language)) {
			$lang = $this->config->application->language;
		}
		$this->_lang = $lang;
		$this->getTranslation("default");
	}

	/**
	 * Returns the translation related to the given key
	 */
	public function _($index, $defaultTranslation = "", $placeholders = null)
	{
		$translation = $index;
		if ($defaultTranslation!="") {
			$translation = $defaultTranslation;
		}
		if ($this->exists($index)) {
			$translation = $this->_translate[$index];
		}
		return $this->replacePlaceholders($translation, $placeholders);
	}

	/**
	 * Check whether is defined a translation key in the internal array
	 */
	public function exists($index)
	{
		return isset($this->_translate[$index]);
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
	 * Get translation from file by tag file name
	 * ie:
	 * in controller:
	 * 		$this->getDI()['t']->getTranslation("anytag");
	 * 
	 * @param string $tag	language filename tag to load.
	 */
	public function getTranslation($tag="")
	{
		if (empty($tag)) {
			return;
		}
		$translate = array();
		$lang = $this->_lang;
		$path = array();
		if ($tag=="default") {
			$path[] = CORE_PATH . 'languages/' . $lang . "/$lang.php";
			$active_modules = $this->config->application->active_modules;
			foreach ($active_modules as $active_module) {
				$path[] = APP_PATH . '/modules/' . $active_module . '/languages/' . $lang . "/$lang.php";
			}
		} else {
			$path[] = CORE_PATH . 'languages/' . $lang . "/$lang.$tag.php";
			$path[] = APP_PATH . '/modules/' . $this->router->getModuleName() . '/languages/' . $lang . "/$lang.$tag.php";
		}
		$translate = $this->getTranslationFileContents($path);
		$this->_translate = array_merge($this->_translate, $translate);
  }
	
	protected function getTranslationFileContents($path) {
		$translate = array();
		
		if (!is_array($path)) {
			$path = array($path);
		}
		
  	foreach ($path as $file) {
		  if (file_exists($file)) {
		  	$m = include $file;
		  	if (is_array($m)) {
		  		$translate = array_merge($translate, $m);
	  		} else {
	  			throw new Exception('Language files must be returned array!');
  			}
	  	}
		}
  	return $translate;
	}	

}