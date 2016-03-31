<?php

function getOptions($optionNames) {
	$conditions = "";
	$options = new Manafx\Models\Options;
	if (is_array($optionNames)) {
		foreach($optionNames as $optionName) {
			if ($conditions == "") {
				$conditions .= "option_name = '$optionName'";
			} else {
				$conditions .= " OR option_name = '$optionName'";
			}
		}
		$query = $options::find(array(
			'columns' => "option_id, option_name, option_value",
			'conditions' => $conditions,
		));
		return toAssocArray($query, "option_name", "option_value");
	} else {
		$conditions .= "option_name = '$optionNames'";
		$query = $options::findFirst(array(
			'columns' => "option_id, option_name, option_value",
			'conditions' => $conditions,
		));
		return $query->option_value;
	}
}

/**
 * Get method name caller.
 *
 * @return array $last_call - Detail of caller
 */
function GetCallingMethodName(){
  $e = new Exception();
  $trace = $e->getTrace();
  //position 0 would be the line that called this function so we ignore it
  $last_call = $trace[1];
  return $last_call;
}

/**
 * Delete directory / folder.
 *
 * @param string $dir - path of dir to delete
 * @return bool - sucess or failure
 */
function deleteDir($dir) {
  foreach(glob($dir . '/*') as $file) {
    if(is_dir($file)) deleteDir($file); else unlink($file);
  }
	if (rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}

function jdmonth($m, $calendar = CAL_GREGORIAN, $abbrev = false) { 
   if($calendar == CAL_GREGORIAN && $abbrev) $mode = 0; 
   elseif($calendar == CAL_GREGORIAN && !$abbrev) $mode = 1; 
   elseif($calendar == CAL_JULIAN && $abbrev) $mode = 2; 
   elseif($calendar == CAL_JULIAN && !$abbrev) $mode = 3; 
   elseif($calendar == CAL_JEWISH) $mode = 4; 
   elseif($calendar == CAL_FRENCH) $mode = 5; 
   else $mode = 10; //use an invalid mode and let the underlying function handle it 

   return jdmonthname(gregoriantojd($m,1,1), $mode); 
}

/**
 * Split string to table/alias name (beginning with '_') and column name separated by '_'.
 * Ex. $str = "_ur_role_id";
 * echo split_alias($str)		// "ur.role_id"
 * 
 * @param string $str to split.
 * @return string.
 */
function split_alias($str)
{
	$ret = $str;
	if (substr($str, 0, 1) == "_") {
		$s = substr($str, 1);
		$tbl = strchr($s, "_", true);
		$col = substr(strchr($s, "_"), 1);
		$ret = $tbl . "." . $col;
	}
	return $ret;
}

/**
 * Implode/join array elements with a string formed by the comma with special format.
 * Add $attribute string at beginning or string result and add comma (,) at the end.
 * Ie: array(2,3,5,12,21) or string '2,3,5,12,21'
 * CSVImplode result: 'data,2,3,5,12,21,'
 * 
 * @param array or string $data.
 * @return string.
 */
function CSVImplode($data, $attribute="data,")
{
	if (substr($attribute, -1) !== ",") {
		$attribute .= ",";
	}
	$atrLen = strlen($attribute);
	$str = $data;
	if (is_array($str)) {
		$str = implode(",", $str);
	}
 	$str = trim($str);
	if (substr($str,0,$atrLen) !== $attribute) {
		$str = $attribute . $str;
	}
	if (substr($str, -1) !== ",") {
		$str .= ",";
	}
	return $str;
}

/**
 * Explode/split a string by string formed by the comma with special format.
 * Format begin with $attribute and end with comma.
 * Ie: 'data,2,3,5,12,21,'
 * CSVExplode result: array(2,3,5,12,21)
 * 
 * @param string $data to split.
 * @return array.
 */
function CSVExplode($data, $attribute="data,")
{
	if (substr($attribute, -1) !== ",") {
		$attribute .= ",";
	}
	$atrLen = strlen($attribute);
	if (is_array($data)) {
		return $data;
	}
 	$str = trim($data);
	if (substr($str,0,$atrLen) == $attribute) {
		$str = substr($str, $atrLen);
		if (substr($str, -1) == ",") {
			$str = substr($str, 0, -1);
		}
	}
	$arrs = explode(",", $str);
	return array_diff($arrs, array(""));
}

/**
 * Convert object/array to assoc array.
 *
 * @param object $source.
 * @param string $fKey column name as key.
 * @param string $fValue column name as value. Default null to pass all columns
 * @return mixed Unserialized data can be any type.
 */
function toAssocArray($source, $fKey, $fValue = null)
{
	$assoc = array();
	if (is_object($source)) {
		foreach ($source as $s) {
			if ($fValue==null) {
				$assoc[$s->$fKey][] = $s;
			} else {
				$assoc[$s->$fKey] = $s->$fValue;
			}
		}
	}
	if (is_array($source)) {
		foreach ($source as $s) {
			if ($fValue==null) {
				$assoc[$s[$fKey]][] = $s;
			} else {
				$assoc[$s[$fKey]] = $s[$fValue];
			}
		}
	}
	return $assoc;
}

/**
 * Make recursive
 * 
 * @param array   $d   flat data, implementing a id/parent id (adjacency list) structure
 * @param mixed   $r   root id, node to return
 * @param string  $pk  parent id index
 * @param string  $k   id index
 * @param string  $c   children index
 * @return array
 */
function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'child') {
  $m = array();
  foreach ($d as $e) {
    isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
    isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
    $m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
  }
  return $m[$r];
}	

/**
 * Make tree
 * 
 * @param array   $d   flat data, implementing a id/parent id (adjacency list) structure
 * @param mixed   $r   root id, node to return
 * @param string  $pk  parent id index
 * @param string  $k   id index
 * @param string  $c   children index
 * @return array
 */
function makeTree($d, $r = 0, $parentKey = 'parent', $idKey = 'id', $c = 'child') {
	$arr = makeRecursive($d, $r, $parentKey, $idKey, $c);
	$t = array();
  foreach ($arr as $value ) {
  	if ($value[$parentKey]==$r) {
  		$na = $value;
  		$na['level'] = 0;
  		unset($na[$c]);
	  	$t[] = $na;
	  	if (isset($value[$c])) {
	  		if (!empty($value[$c])) {
 					$t = array_merge($t, subTree($value[$c], $idKey, $c, 0));
  			}
	 		}
  	} else {
  		break;
 		}
  }
  return $t;
}

function subTree($subs, $idKey, $c, $level) {
	$st = array();
	$level = $level + 1;
	foreach ($subs as $value) {
		$na = $value;
		$na['level'] = $level;
		unset($na[$c]);
  	$st[] = $na;
  	if (isset($value[$c])) {
  		if (!empty($value[$c])) {
  			$st = array_merge($st, subTree($value[$c], $idKey, $c, $level));
 			}
 		}
	}
	return $st;
}


/**
 * Unserialize value only if it was serialized.
 *
 * @param string $data unserialized data, if is needed.
 * @return mixed Unserialized data can be any type.
 */
function ifneeded_unserialize($data) {
	if (is_serialized($data)) {
		return @unserialize($data);
	}
	return $data;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( $data, $strict = true ) {
	// if it isn't a string, it isn't serialized.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
 	if ( 'N;' == $data ) {
		return true;
	}
	if ( strlen( $data ) < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist.
		if ( false === $semicolon && false === $brace )
			return false;
		// But neither must be in the first X characters.
		if ( false !== $semicolon && $semicolon < 3 )
			return false;
		if ( false !== $brace && $brace < 4 )
			return false;
	}
	$token = $data[0];
	switch ( $token ) {
		case 's' :
			if ( $strict ) {
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
			} elseif ( false === strpos( $data, '"' ) ) {
				return false;
			}
			// or else fall through
		case 'a' :
		case 'O' :
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}

/**
 * Serialize data, if needed.
 *
 * @param string|array|object $data Data that might be serialized.
 * @return mixed A scalar data
 */
function ifneeded_serialize($data) {
	if (is_array($data) || is_object($data) ) {
		return serialize($data);
	}
	return $data;
}
