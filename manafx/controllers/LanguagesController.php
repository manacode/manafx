<?php
namespace Manafx\Controllers;
use Manacode\Helpers\CountryList;

class LanguagesController extends \ManafxAdminController {
	public $messages;
	
	public function indexAction() {
		$this->auth->ced();
		$this->view->languages = $this->grabLanguages();
	}

	public function manageAction($languageCode = "") {
		$this->auth->ced();
		if ($this->request->isPost()) {
			$languageCode = $this->request->getPost('languageCode', 'striptags');
		}
		if ($languageCode == "") {
			$this->flashSession->error($this->t->_("language_not_found", "Language not found!"));
			return $this->response->redirect(ADMIN_ROUTE . "/languages/");
		}
		$languageInfo = $this->getLanguageInfo($languageCode);
		if ($languageInfo === false) {
			$this->flashSession->error($this->t->_("language_not_found", "Language not found!"));
			return $this->response->redirect(ADMIN_ROUTE . "/languages/");
		}
		$this->view->languageInfo = $languageInfo;
		$this->view->languageFiles = $this->getLanguageFiles($languageCode);
	}

	function translationAction() {
		if ($this->request->isPost()) {
			$this->auth->ced();
			$purpose = $this->request->getPost('purpose', 'striptags');
			$languageCode = $this->request->getPost('languageCode', 'striptags');
			$languageModule = $this->request->getPost('languageModule', 'striptags');
			$languageFileName = $this->request->getPost('languageFileName', 'striptags');
			if ($languageModule == "system") {
				$language_dir = CORE_PATH . 'languages/' . $languageCode;
			} else {
				$language_dir = APP_PATH . '/modules/' . $languageModule . "/languages/" . $languageCode;
			}
			$path = $language_dir . '/' . $languageFileName;
			if ($purpose == "add") {
				$languageFileContent = array();
				if (is_file($path)) {
					$this->flashSession->error($this->t->_("language_file_exists", "Language file already exists!"));
					return $this->response->redirect(ADMIN_ROUTE . "/languages/manage/$languageCode");
				} else {
					$createFile = $this->updateTranslationFile($path, $languageFileContent);
					if ($createFile === false) {
						$this->flashSession->error($this->t->_("create_translation_fail", "Create new translation file failed!"));
						return $this->response->redirect(ADMIN_ROUTE . "/languages/manage/$languageCode");
					}
				}
			} else {
				$languageFileContent = $this->readTranslationFile($path);
				if ($languageFileContent === false) {
					$this->flashSession->error($this->t->_("language_file_not_found", "Language file not found!"));
					return $this->response->redirect(ADMIN_ROUTE . "/languages/manage/$languageCode");
				}
			}
			$this->view->languageCode = $languageCode;
			$this->view->languageModule = $languageModule;
			$this->view->languageFileName = $languageFileName;
			$this->view->languageFilePath = $path;
			$this->view->languageFileContent = $languageFileContent;
		}
	}
	
	function updateTranslationAction() {
		if ($this->request->isPost()) {
			$languageCode = $this->request->getPost('languageCode', 'striptags');
			$languageFileName = $this->request->getPost('languageFileName', 'striptags');
			$languageFilePath = $this->request->getPost('languageFilePath', 'striptags');
			$translationKey = $this->request->getPost('translationKey', 'striptags');
			$translationValue = $this->request->getPost('translationValue', 'striptags');
			$aTranslation = array();
			foreach ($translationKey as $index => $key) {
				$aTranslation[$key] = $translationValue[$index];
			}
			$updateFile = $this->updateTranslationFile($languageFilePath, $aTranslation);
			if ($updateFile === false) {
				$this->flashSession->error($this->t->_("update_translation_fail", "Update translation file failed!"));
			} else {
				$this->flashSession->success($this->t->_("update_translation_success", "Translation file <strong>%languageFileName%</strong> updated successfully!", array('languageFileName'=>$languageFileName)));
			}
			return $this->response->redirect(ADMIN_ROUTE . "/languages/manage/$languageCode");
		}
	}
	
	function updateTranslationFile($path, $languageFileContent = array()) {
		$dirname = dirname($path);
		if (!is_dir($dirname)) {
			if (!mkdir($dirname)) {
				return false;
			}
		}
		return file_put_contents($path, '<?php return ' . var_export($languageFileContent, true) . ';');
	}
	
	function readTranslationFile($path) {
		if (is_file($path)) {
			return include $path;
		}
		return false;
	}
	 
	public function deleteAction() {
		if ($this->request->isPost()) {
			$languageCode = $this->request->getPost('languageCode2Delete', 'striptags');
			$languageName = $this->request->getPost('languageName2Delete', 'striptags');
			$deleteOption = $this->request->getPost('deleteOptions', 'striptags');
			if ($languageCode==$this->config->application->default_language) {
				$this->flashSession->error($this->t->_("Cannot delete default language!"));
			} else {
				$language_dir = CORE_PATH . '/languages/' . $languageCode;
				if (is_dir($language_dir) || $languageCode !== NULL) {
					$ret = deleteDir($language_dir);
					if ($ret) {
						$dir = APP_PATH . '/modules';
						if ($deleteOption=="active") {
							// delete language in all active module only
							$active_modules = $this->config->application->active_modules;
							foreach ($active_modules as $module) {
					    	$language_dir = $dir . "/" . $module . "/languages/" . $languageCode;
				        if (is_dir($language_dir)) {
				        	deleteDir($language_dir);
								}
							}
						}
						if ($deleteOption=="all") {
							// delete language in all module
							if ($handle = opendir($dir)) {
						    while (false !== ($entry = readdir($handle))) {
						    	$language_dir = $dir . "/" . $entry . "/languages/" . $languageCode;
					        if (is_dir($language_dir) && $entry != "." && $entry != "..") {
					        	deleteDir($language_dir);
									}
								}
								closedir($handle);
							}
						}
						$this->flashSession->success($this->t->_("language_deleted_message", "Language %languageName% (%languageCode%) was deleted successfully", array("languageCode" => $languageCode, "languageName" => $languageName)));
					} else {
						$this->flashSession->error($this->t->_("delete_language_failed", "Delete language failed"));
					}
				} else {
					$this->flashSession->error($this->t->_("language_not_found", "Language not found"));
				}
			}
		}
		return $this->response->redirect(ADMIN_ROUTE . "/languages/");
	}

	public function createAction() {
		$this->auth->ced();
		// echo "<pre>";
		//var_dump($this->request->getPost());
		//exit();
		if ($this->request->isPost()) {
			if ($this->request->getPost('create')=="create") {
				$languageInfo = $this->request->getPost('languageInfo');
				$languageCode = $languageInfo['code'];
				if ($languageCode !== "") {
					$xmlFile = CORE_PATH . 'languages/' . $languageCode . "/$languageCode.xml";
					if (is_file($xmlFile)) {
						$this->flashSession->error($this->t->_("language_exist", "Language code already exists!"));
					} else {
						$languageInfo["creation_date"] = $this->dt->today();
						if ($this->updateLanguageInfo($xmlFile, $languageInfo) === false) {
							$this->flashSession->error($this->t->_("language_create_failed", "Create language failed!"));
						} else {
							return $this->response->redirect(ADMIN_ROUTE . "/languages/manage/$languageCode");
						}
					}
				}
			}
		}
		$countryList = new CountryList();
		$this->view->countryList = $countryList->getCountry();
		
	}

	public function editAction($languageCode="") {
		$this->auth->ced();
		if ($this->request->isPost()) {
			if ($this->request->getPost('update')=="update") {
				$languageInfo = $this->request->getPost('languageInfo');
				$languageCode = $languageInfo['code'];
				$xmlFile = CORE_PATH . 'languages/' . $languageCode . "/$languageCode.xml";
				if ($this->updateLanguageInfo($xmlFile, $languageInfo) === false) {
					$this->flashSession->error($this->t->_("language_create_failed", "Create language failed!"));
				} else {
					return $this->response->redirect(ADMIN_ROUTE . "/languages/manage/" . $languageInfo['code']);
				}
			} else {
				$languageCode = $this->request->getPost('languageCode', 'striptags');
			}
		}
		if ($languageCode=="") {
			return $this->response->redirect(ADMIN_ROUTE . "/languages/");
		}
		$languageInfo = $this->getLanguageInfo($languageCode);
		if ($languageInfo === false) {
			$this->flashSession->error($this->t->_("language_not_found", "Language not found!"));
			return $this->response->redirect(ADMIN_ROUTE . "/languages/");
		}
		$this->view->languageInfo = $languageInfo;
	}
	
	function updateLanguageInfo($path, $languageInfo) {
		foreach($languageInfo as $key => $value) {
			if ($value=="") {
				$languageInfo[$key] = "-";
			}
		}
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<language>' . "\n";
		$xml .= '	<code>' . $languageInfo['code'] . '</code>' . "\n";
		$xml .= '	<name>' . $languageInfo['name'] . '</name>' . "\n";
		$xml .= '	<rtl>' . $languageInfo['rtl'] . '</rtl>' . "\n";
		$xml .= '	<version>' . $languageInfo['version'] . '</version>' . "\n";
		$xml .= '	<creation_date>' . $languageInfo['creation_date'] . '</creation_date>' . "\n";
		$xml .= '	<author>' . $languageInfo['author'] . '</author>' . "\n";
		$xml .= '	<author_uri>' . $languageInfo['author_uri'] . '</author_uri>' . "\n";
		$xml .= '	<locale>' . $languageInfo['locale'] . '</locale>' . "\n";
		$xml .= '	<description>' . $languageInfo['description'] . '</description>' . "\n";
		$xml .= '	<license>' . $languageInfo['license'] . '</license>' . "\n";
		$xml .= '	<license_uri>' . $languageInfo['license_uri'] . '</license_uri>' . "\n";
		$xml .= '</language>' . "\n";

		$dirname = dirname($path);
		if (!is_dir($dirname)) {
			if (!mkdir($dirname)) {
				return false;
			}
		}
		return file_put_contents($path, $xml);
	}

	function getLanguageInfo($languageCode) {
		$xmlFile = CORE_PATH . '/languages/' . $languageCode . "/$languageCode.xml";
  	if (is_file($xmlFile)) {
      $info_xml = simplexml_load_file($xmlFile);
      $info_json = json_encode($info_xml);
			$info_array = json_decode($info_json, true);
      return $info_array;
   	}
		return false;
	}

	function getLanguageFiles($languageCode) {
		$languageFiles = array();
		// search core path
		$dir = CORE_PATH . 'languages/' . $languageCode;
		$languageFiles["system"] = $this->grabLanguageFiles($languageCode, $dir);
		// search active modules path
		$active_modules = $this->config->application->active_modules;
		foreach ($active_modules as $module) {
			$dir = APP_PATH . '/modules/' . $module . '/languages/' . $languageCode;
			$languageFiles[$module] = $this->grabLanguageFiles($languageCode, $dir);
		}
		return $languageFiles;
	}
	
	function grabLanguageFiles($languageCode, $dir) {
		$langFiles = array();
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
		    while (false !== ($entry = readdir($handle))) {
		    	$langFile = $dir . "/" . $entry;
					if (is_file($langFile) && $entry != "." && $entry != "..") {
						if (substr($entry, 0, strlen($languageCode)) == $languageCode && substr($entry, -4) == ".php") {
	        		$langFiles[] = array('filename' => $entry, 'path' => $langFile);
    				}
	        }
		    }
		    closedir($handle);
			}
		}
		return $langFiles;
	}

	function grabLanguages() {
		$dir = CORE_PATH . '/languages';
		$languages = array();
		if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
				if (is_dir($dir . "/" . $entry) && $entry != "." && $entry != "..") {
        	$language_info_path = $dir . "/" . $entry . "/$entry.xml";
        	if (is_file($language_info_path)) {
	          $info_xml = simplexml_load_file($language_info_path);
	          $info_json = json_encode($info_xml);
						$info_array = json_decode($info_json, true);
	          $languages[$entry] = $info_array;
         	}
        }
	    }
	    closedir($handle);
		}
		return $languages;
	}
	
	public function setDefaultAction() {
		if ($this->request->isPost()) {
			$languageCode = $this->request->getPost('languageCode', 'striptags');
			$this->updateGlobalConfigVar("application", "default_language", $languageCode, true);
			if ($this->updateGlobalConfigFile()===false) {
				$this->flashSession->error($this->t->_("update_setting_failed", "Update setting failed, please try again later."));
			} else {
				$this->config->application->language = $languageCode;
				$this->flashSession->success($this->t->_("default_language_activated", "New default language %languageCode% was activated successfully", array("languageCode" => $languageCode)));
			}
		}
		return $this->response->redirect(ADMIN_ROUTE . "/languages/");
	}

}