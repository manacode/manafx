<?php
/*
  +------------------------------------------------------------------------+
  | ManaCode PHP Helpers                                                   |
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

namespace Manacode\Helpers;
class DateTime extends \DateTime
{
	protected $dateFormat = "Y-m-d";
	protected $timeFormat = "H:i:s";
	protected $timeReference = "gmt";
	protected $timeZone = "UTC";
	
	function __construct($config = array()) {
		if (isset($config['dateFormat'])) {
			$this->dateFormat = $config['dateFormat'];
		}
		if (isset($config['timeFormat'])) {
			$this->timeFormat = $config['timeFormat'];
		}
		if (isset($config['timeReference'])) {
			$this->timeReference= $config['timeReference'];
		}
		if (isset($config['timeZone'])) {
			$this->timeZone = $config['timeZone'];
		}
		$this->set_timeZone();
	}

	function set_timeZone($tz="") {
		if ($tz=="") {
			$tz = $this->timeZone;
		} else {
			$this->timeZone = $tz;
		}
		date_default_timezone_set($tz);
	}

	function set_dateFormat($df) {
		$this->dateFormat = $df;
	}

	function set_timeFormat($tf) {
		$this->timeFormat = $tf;
	}

	function set_timeReference($tr) {
		$this->timeReference = $tr;
	}

	function get_timeZone() {
		return $this->timeZone;
	}

	function get_dateFormat() {
		return $this->dateFormat;
	}

	function get_timeFormat() {
		return $this->timeFormat;
	}

	function get_timeReference() {
		return $this->timeReference;
	}

	// Return current Unix timestamp or its GMT equivalent based on the time reference
	function now() {
		if (strtolower($this->timeReference) == 'gmt') {
			$now = time();
			$system_time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
			if (strlen($system_time) < 10) {
				$system_time = time();
			}
			return $system_time;
		} else {
			return time();
		}
	}

	// Return date based on the date format
	function mdate($date_format = '', $time = '') {
		if ($date_format == '') {
			$date_format = $this->dateFormat;
		}
		if ($time == '') {
			$time = $this->now();
		}
		return date($date_format, $time);
	}
	
	// Return date and time based on the datetime format
	function mdatetime($time = '', $timezone = '', $datetime_format = '') {
		if ($datetime_format == '') {
			$datetime_format = $this->dateFormat . ' ' . $this->timeFormat;
		}
		if ($time == '') {
			$time = 'now';
		}
		if ($timezone == '') {
			$timezone = $this->timeZone;
		}
		$utc = new \DateTimeZone($timezone);
		$dt = new \DateTime($time, $utc);
		return $dt->format($datetime_format);
	}

	// Return list of timezones
	function days_in_month($month = 0, $year = '') {
		if ($month < 1 || $month > 12) {
			$month = $this->mdate('m');
		}
		if (!is_numeric($year) || strlen($year) != 4) {
			$year = date('Y');
		}
		if ($month == 2) {
			if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 != 0)) {
				return 29;
			}
		}

		$days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		return $days_in_month[$month - 1];
	}

	// Return list of timezones
	function get_timezones() {
		$utc = new \DateTimeZone('UTC');
		$dt = new \DateTime('now', $utc);
		$timezones = array();
		foreach(\DateTimeZone::listIdentifiers() as $tz) {
	    $current_tz = new \DateTimeZone($tz);
	    $offset =  $current_tz->getOffset($dt);
	    $transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
	    $abbr = $transition[0]['abbr'];
			$timezones[$tz]= $abbr. ' '. $this->formatOffset($offset);
		}
		return $timezones;
	}
	
	function formatOffset($offset) {
		$hours = $offset / 3600;
		$remainder = $offset % 3600;
		$sign = $hours > 0 ? '+' : '-';
		$hour = (int) abs($hours);
		$minutes = (int) abs($remainder / 60);
		if ($hour == 0 AND $minutes == 0) {
			$sign = ' ';
		}
		return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');
	}

	// Return only current time
  function timeNow() {
    return $this->mdate($this->timeFormat);
  }
  
	// Return something like: Monday 8th of August 2005
  function today() {
    return $this->mdate('l jS \of F Y');
  }

	// Return something like: Monday 8th of August 2005 03:12:46 PM
  function todaytime() {
    return $this->mdate('l jS \of F Y ' . $this->timeFormat);
  }

	// Return first date of this week
  function get_first_date_this_week() {
    return $this->mdate($this->dateFormat, strtotime('this week', time()));
  }

	// Return last date of this week
  function get_last_date_this_week() {
    return $this->mdate($this->dateFormat, strtotime('this week +6 days', time()));
  }

	// Return first date of this month
  function get_first_date_this_month($showtime=false) {
    if ($showtime===true) {
      return $this->mdate("Y-m-01 00:00:01");
    } else {
      return $this->mdate("Y-m-01");
    }
  }

	// Return last date of this month
  function get_last_date_this_month() {
    return $this->mdate("Y-m-t");
  }

	// Return first date of last month
  function get_first_date_last_month($m=1) {
    return $this->mdate($this->dateFormat, mktime(0, 0, 0, $this->mdate("m")-$m, 1, $this->mdate("Y")));
  }

	// Return last date of last month
  function get_last_date_last_month($m=1) {
    return $this->mdate($this->dateFormat, mktime(24, 0, 0, $this->mdate("m")-($m-1), -1, $this->mdate("Y")));
  }

	// Return first date of this year
  function get_first_date_this_year($showtime=false) {
    if ($showtime===true) {
      return $this->mdate("Y-01-01 00:00:01");
    } else {
      return $this->mdate("Y-01-01");
    }
  }

	// Return last date of this year
  function get_last_date_this_year() {
    return $this->mdate($this->dateFormat, mktime(24, 0, 0, 1, -1, $this->mdate("Y")+1));
  }

	// Return first date of last year
  function get_first_date_last_year($y=1) {
    return $this->mdate($this->dateFormat, mktime(0, 0, 0, 1, 1, $this->mdate("Y")-$y));
  }

	// Return last date of last year
  function get_last_date_last_year($y=1) {
    return $this->mdate($this->dateFormat, mktime(24, 0, 0, 1, -1, $this->mdate("Y")-($y-1)));
  }
  
	// Return something like: Mar
  function get_month_shortname($m="") {
  	if ($m=="") {
  		$m = $this->mdate("m");
 		}
		$dt = \DateTime::createFromFormat('!m', $m);
		return $dt->format('M');
 	}

	// Return something like: March
  function get_month_longname($m="") {
  	if ($m=="") {
  		$m = $this->mdate("m");
 		}
		$dt = \DateTime::createFromFormat('!m', $m);
		return $dt->format('F');
 	}
 	
	// Return something like: Mon
  function get_day_shortname($date="") {
  	if ($date=="") {
  		$date = $this->mdate();
 		}
		$dt = \DateTime::createFromFormat($this->dateFormat, $date);
		return $dt->format('D');
 	}
 	
	// Return something like: Monday
  function get_day_longname($date="") {
  	if ($date=="") {
  		$date = $this->mdate();
 		}
		$dt = \DateTime::createFromFormat($this->dateFormat, $date);
		return $dt->format('l');
 	}
 	
}