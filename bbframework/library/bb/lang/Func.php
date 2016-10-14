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

namespace bb\lang;

class Func {

	public static function is($func) {
		if(empty($func)) return false;
		if($func instanceof \Closure) return true;
		if(is_array($func) || is_string($func)) return is_callable($func);
		return false;
	}

	public static function invoke($func, $params) {
		return call_user_func_array($func, $params);
	}

}