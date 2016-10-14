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

namespace bb\cache;

use think\cache\driver\Redis as RedisCache;

class Redis extends RedisCache {

	public function __call($method, $params) {
		return call_user_func_array([$this->handler, $method], $params);
	}
	
}