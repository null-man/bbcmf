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

namespace bb;

use bb\cron\CronBase;

class Cron {

	protected static $handler = null;

	public static function __callStatic($method, $params) {
		if(is_null(self::$handler)) {
			self::$handler = new CronBase();
		}
		call_user_func_array([self::$handler, $method], $params);
	}

}