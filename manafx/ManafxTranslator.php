<?php

use Manacode\Phalcon\Translate\NativeArray;
class ManafxTranslator extends NativeArray
{
	var $_lang = "en-US";

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
		$this->setTranslation($translate);
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