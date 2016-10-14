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

use think\Cache as ThinkCache;
use think\Config;
use think\Log;

class Cache extends ThinkCache {

	/**
     * 连接缓存
     * @access public
     * @param array $options  配置数组
     * @return object
     */
    public static function connect(array $options = []) {
        $md5 = md5(serialize($options));
        if (!isset(self::$instance[$md5])) {
            $type  = !empty($options['type']) ? $options['type'] : 'Redis';
            $class = (!empty($options['namespace']) ? $options['namespace'] : '\\bb\\cache\\') . ucwords($type);
            unset($options['type']);
            self::$instance[$md5] = new $class($options);
            // 记录初始化信息
            APP_DEBUG && Log::record('[ CACHE ] INIT ' . $type . ':' . var_export($options, true), 'info');
        }
        self::$handler = self::$instance[$md5];
        return self::$handler;
    }


    public static function __callStatic($method, $params) {
        if (is_null(self::$handler)) {
            // 自动初始化缓存
            self::connect(Config::get('cache'));
        }
        return call_user_func_array([self::$handler, $method], $params);
    }

}