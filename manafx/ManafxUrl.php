<?php

class ManafxUrl extends \Phalcon\Mvc\Url
{

	/**
	 * Create URL Slug
	 *
	 * Takes a string as input and creates a human-friendly URL string with a "separator" string as the word separator.
	 *
	 * @param string $str Input string
	 * @param string $separator Word separator (usually '-' or '_')
	 * @param bool $lowercase Whether to transform the output string to lowercase
	 * @return string
	 */
	function getUrlSlug($str, $separator = '-', $lowercase = TRUE)
	{
		$q_separator = preg_quote($separator, '#');
	
		$trans = array(
			'&.+?;'			=> '',
			'[^\w\d _-]'		=> '',
			'\s+'			=> $separator,
			'('.$q_separator.')+'	=> $separator
		);
	
		$str = strip_tags($str);
		foreach ($trans as $key => $val) {
			$str = preg_replace('#'.$key.'#i'.(TRUE ? 'u' : ''), $val, $str);
		}
	
		if ($lowercase === TRUE) {
			$str = strtolower($str);
		}
	
		return trim(trim($str, $separator));
	}

}