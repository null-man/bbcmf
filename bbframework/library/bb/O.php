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

class O {

	protected $obj;
	protected $type;

	protected static $obs = [
		'is_array' 		=> 'bb\\lang\\Arr',
		'is_callable' 	=> 'bb\\lang\\Func',
		'is_string' 	=> 'bb\\lang\\Str',
		// 'is_numeric'	=> 'bb\\lang\\Num'
	];

	public function __construct($obj) {
		$this->obj = $obj;
		$this->type = 'bb\\lang\\Unknown';

		foreach (static::$obs as $func => $type) {
			if(call_user_func_array($func, [$this->obj])) {
				$this->type = $type;
				break;
			}
		}
	}

	public static function b($obj) {
		return new O($obj);
	}

	public function __call($method, $params) {
		$r = call_user_func_array($this->type. '::' . $method, array_merge([& $this->obj], $params));
		return new O($r);
	}

	public function get() {
		return $this->obj;
	}

	public function getType() {
		return $this->type;
	}

	public static function register($func, $type) {
		static::$obs[$func] = $type;
	}
}