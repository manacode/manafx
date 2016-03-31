<?php
/**
* Helps to manage css/js assets with condition and priority
* 
	samples usage:
		$headerfooter->add_css(array(
			array(
				"path" => 'themes/' . $current_theme . "/common/css/image-hover-ie7.css",
				"priority" => 0,
				"condition" => "IE 7"
			),
			array(
				"path" => 'themes/' . $current_theme . "/common/css/image-hover-ie8.css",
				"priority" => 0,
				"condition" => "IE 8"
			)
		));
*/

use Phalcon\Tag as Tag;

class ManafxHeaderFooter
{
	var $di;
	public $js_base_url = false;
	private $custom_css = array();
	private $custom_js_header = array();
	private $custom_js_footer = array();

	function __construct($di) {
		$this->di = $di;
	}
	
	/*
	*	condition example: "lt IE 7", IE 7, IE 8, IE 9, gt IE 9, (gt IE 9)|!(IE)
	*/
	function add_css($acss=array()) {
		foreach ($acss as $css) {
			if (isset($css["path"]) && $css["path"]!="") {
				$priority = 0;
				if (isset($css["priority"])) {
					$priority = $css["priority"];
				}
				$condition = "";
				if (isset($css["condition"])) {
					$condition = $css["condition"];
				}
				$is_local = true;
				if (isset($css["is_local"])) {
					$is_local = $css["is_local"];
				}
				$this->custom_css[] = array("css" => $css["path"], "priority" => $priority, "condition" => $condition, "is_local" => $is_local);
			}
		}
	}
	
	function add_js_header($ajs=array()) {
		foreach ($ajs as $js) {
			if (isset($js["path"]) && $js["path"]!="") {
				$priority = 0;
				if (isset($js["priority"])) {
					$priority = $js["priority"];
				}
				$condition = "";
				if (isset($js["condition"])) {
					$condition = $js["condition"];
				}
				$is_local = true;
				if (isset($js["is_local"])) {
					$is_local = $js["is_local"];
				}
				$this->custom_js_header[] = array("js" => $js["path"], "priority" => $priority, "condition" => $condition, "is_local" => $is_local);
			}
		}
	}
	
	function add_js_footer($js, $priority=0, $condition="") {
		foreach ($ajs as $js) {
			if (isset($js["path"]) && $js["path"]!="") {
				$priority = 0;
				if (isset($js["priority"])) {
					$priority = $js["priority"];
				}
				$condition = "";
				if (isset($js["condition"])) {
					$condition = $js["condition"];
				}
				$is_local = true;
				if (isset($js["is_local"])) {
					$is_local = $js["is_local"];
				}
				$this->custom_js_footer[] = array("js" => $js["path"], "priority" => $priority, "condition" => $condition, "is_local" => $is_local);
			}
		}
	}
	
	function get_header() {
		$ch = "";
		$ch .= $this->css_builder();
		if ($this->js_base_url) {
			$ch .= '<script type="text/javascript">';
			#$ch .= '<!--' . "\n";
			$ch .= 'var base_url="' . $this->di->get("config")->application->baseUrl . '";';
			$ch .= 'var admin_url="' . $this->di->get("config")->application->baseUrl . '/' . ADMIN_ROUTE . '";';
			#$ch .= '-->' . "\n";
			$ch .= '</script>' . "\n";
		}
		$ch .= $this->js_builder("header");
		return $ch;
	}

	function css_builder() {
		usort($this->custom_css, $this->array_sorter('priority'));
		$custom_css = $this->custom_css;
		
		$css_links = array();
		
		foreach ($custom_css as $index => $value) {
			$condition = "none";
			if ($value['condition']!="") {
				$condition = $value['condition'];
			}
			$css = $value['css'];
			$is_local = $value['is_local'];
			if ($is_local) {
				if (!is_file($css)) {
					continue;
				}
			}
			$csslink = Tag::stylesheetLink($this->di->get("config")->application->baseUrl . '/' . $css, $is_local);
			$css_links[$condition][] = $csslink;
		}
		$css_text = "";
		if (array_key_exists("none", $css_links)) {
			foreach ($css_links["none"] as $link) {
				$css_text .= $link;
			}
		}
		foreach ($css_links as $cond => $links) {
			if ($cond=="none") {
				continue;
			}
			$css_text .= "<!--[if $cond]>";
			foreach ($links as $link) {
				$css_text .= $link;
			}
			$css_text .= "<![endif]-->";
		}
		return $css_text;
		
	}

	function js_builder($pos="header") {
		$custom_js = $this->custom_js_header;
		if ($pos=="footer") {
			$custom_js = $this->custom_js_footer;
		}
		
		usort($custom_js, $this->array_sorter('priority'));
		
		$js_links = array();
		
		foreach ($custom_js as $index => $value) {
			$condition = "none";
			if ($value['condition']!="") {
				$condition = $value['condition'];
			}
			$js = $value['js'];
			$is_local = $value['is_local'];
			if ($is_local) {
				$no_vers_js = $js;
				$ver_pos = strpos($js, "?");
				if ($ver_pos!==false) {
					$no_vers_js = substr($js, 0, strpos($js, "?"));
				}
				if (!is_file($no_vers_js)) {
					continue;
				}
			}
			$jslink = Tag::javascriptInclude($this->di->get("config")->application->baseUrl . '/' . $js, $is_local);
			$js_links[$condition][] = $jslink;
		}
		$js_text = "";
		if (array_key_exists("none", $js_links)) {
			foreach ($js_links["none"] as $link) {
				$js_text .= $link;
			}
		}
		foreach ($js_links as $cond => $links) {
			if ($cond=="none") {
				continue;
			}
			$js_text .= "<!--[if $cond]>";
			foreach ($links as $link) {
				$js_text .= $link;
			}
			$js_text .= "<![endif]-->";
		}
		return $js_text;
		
	}

	function array_sorter($key) {
		return function ($a, $b) use ($key) {
	    return strnatcmp($a[$key], $b[$key]);
		};
	}

}