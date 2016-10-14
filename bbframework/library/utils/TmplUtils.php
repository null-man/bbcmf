<?php

// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NullYang <635384073@qq.com>
// +----------------------------------------------------------------------

namespace util;

class TmplUtils {
	protected static $instance = null;

	public static function __callStatic($method, $params){
		if(is_null(self::$instance)){
			self::$instance = new TmplBaseUtils();

			call_user_func_array([self::$instance, $method], $params);
		}
	}

	public static function _init(){
		return  new TmplBaseUtils();
	}
}
