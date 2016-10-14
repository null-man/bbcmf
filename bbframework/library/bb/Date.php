<?php
// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZergL <lin029011@163.com>
// +----------------------------------------------------------------------

namespace bb;

class Date implements \ArrayAccess {

	protected $timestamp;

	protected static $set = [
		'year', 'month', 'day', 'hour', 'minute', 'second', 'week', 'weekday'
	];

	protected static $weekday = [
		'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'
	];

	// 时间信息
	protected static $get = [
		'year'		=> ['Y', 'intval'],		// 年
		'month'		=> ['m', 'intval'],		// 月
		'day'		=> ['d', 'intval'],		// 日
		'hour'		=> ['H', 'intval'],		// 时
		'minute'	=> ['i', 'intval'],		// 分
		'second'	=> ['s', 'intval'],		// 秒
		'weekday'	=> ['N', 'intval'],		// 星期几
		'week'		=> ['W', 'intval'],		// 第几周
		'leap'		=> ['L', '!boolval'],	// 闰年
		'yearday'	=> ['z', 'intval'],		// 第几天
		'monthdays'	=> ['t', 'intval'],		// 每个月多少天
		'timezone'	=> ['T'],				// 时区
		'gmt'		=> ['P'],				// 时区
		'weekday0'	=> ['w', 'intval'],		// 星期几 0为星期天
		'weekdayD'	=> ['D'],				// 星期几 文本3个字母
		'weekdayl'	=> ['l'],				// 星期几 文本
		'monthF'	=> ['m'],				// 月 前置0 文本
		'monthS'	=> ['F'],				// 月 文本格式
	];


	public function __construct($timestamp = null) {
		if(is_null($timestamp)) {
			$timestamp = time();
		}
		$this->timestamp = $timestamp;
	}


	public static function timestamp($timestamp = null) {
		return new Date($timestamp);
	}


	public static function t($t = null) {
		if(func_num_args() > 1) {
			return call_user_func_array(['bb\Date', 'make'], func_get_args());
		} elseif($t instanceof Date) {
			return self::timestamp($t->get());
		} elseif(is_numeric($t)) {
			return self::timestamp(intval($t)); 
		} elseif(is_string($t)) {
			return self::str($t);
		}
		return self::timestamp($t);
	}


	public static function make($year, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0) {
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
		return self::timestamp($timestamp);
	}


	public static function str($str) {
		return self::timestamp(strtotime($str));
	}


	public function format($format = TIME_DEFAULT_FORMAT) {
		return date($format, $this->timestamp);
	}


	public function get() {
		return $this->timestamp;
	}

	public function time2($type) {

		$type = strtolower($type);
		switch($type) {
			case 'year':
				return Date::make($this->year);
			case 'month':
				return Date::make($this->year, $this->month);
			case 'day':
				return Date::make($this->year, $this->month, $this->day);
			case 'hour':
				return Date::make($this->year, $this->month, $this->day, $this->hour);
			case 'minute':
				return Date::make($this->year, $this->month, $this->day, $this->hour, $this->minute);
			case 'week':
				$t = Date::make($this->year, $this->month, $this->day);
				$t->weekday = 1;
				return $t;
			default:
				return null;
		}

	}


	public function __call($method, $params) {
		if(strpos($method, 'time2') === 0) {
			return $this->time2(substr($method, 5));
		}
	}

	public static function __callStatic($method, $params) {
		if(strpos($method, 'time2') === 0) {
			$t = call_user_func_array(['bb\Date', 't'], $params);
			return call_user_func_array([$t, $method], []);
		}
	}


	// 分时段
	public function interval($second) {
		return Date::t($this->timestamp - $this->timestamp%$second);
	}








	/**
     * 修改器 设置数据对象的值
     * @access public
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name, $value) {

    	if(in_array($name, self::$set)) {

    		if(is_string($value)) {
    			$str = $value;
    		} else {
	    		if($name == 'weekday') {
	    			$day = $this->weekday;

					$k = intval($value / 7);
					$weekday = $value % 7;
					if($weekday < 0) {
						$weekday = 7 + $weekday;
					}
					$str = self::$weekday[$weekday];

	    			if($day > $weekday) {
	    				if($k == 0) {
	    					$str = '- 1 ' . $str;
	    				} elseif($k > 0) {
	    					$str = '+ '. ($k - 1) . ' ' .$str;
	    				} else {
							$str = '- '. (abs($k) + 1) . ' ' .$str;
	    				}
	    			} else {
	    				if($k == 0) {
	    					$str = '+ 1 ' . $str;
	    				} elseif($k > 0) {
	    					$str = '+ '. ($k + 1) . ' ' .$str;
	    				} else {
							$str = '- '. (abs($k) - 1) . ' ' .$str;
	    				}
	    			}

	    		} else {
	    			$i = $value - $this[$name];
					if($i == 0) {
						return;
						// $this->timestamp = strtotime($na . . $name);
					} else {
						$str = ($i > 0 ? '+ ' : '-') . abs($i) . ' ' . $name;
						
					}
	    		}
	    	}
    		$this->timestamp = strtotime($str, $this->timestamp);
    	}
    }

    /**
     * 获取器 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name) {
    	if(array_key_exists($name, self::$get)) {
			$c = Date::$get[$name];
			$i = date($c[0], $this->timestamp);
			if(!empty($c[1])) {
				$fun = $c[1];
				if(strpos($fun, '!') === 0) {
					$fun = substr($fun, 1);
					return !call_user_func_array($fun, [$i]);
				} else {
					return call_user_func_array($fun, [$i]);
				}
			} else {
				return $i;
			}
		}
    }

    public function __isset($name) {
        return array_key_exists($name, self::$convert);
    }

    /**
     * 销毁数据对象的值
     * @access public
     * @param string $name 名称
     * @return void
     */
    public function __unset($name) {

    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }

    // ArrayAccess
    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }

    public function offsetExists($name)
    {
        return $this->__isset($name);
    }

    public function offsetUnset($name)
    {
        $this->__unset($name);
    }

    public function offsetGet($name)
    {
        return $this->__get($name);
    }







}