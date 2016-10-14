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

use bb\es\Connection;

class ES {

	// ES连接实例
    private static $instances = [];

    /**
     * ES初始化 并取得ES实例
     * @static
     * @access public
     * @param mixed $config 连接配置
     * @return Object 返回ES驱动类
     */
    public static function connect($config = []) {
    	// 解析连接配置
        list($name, $conn) = self::parseConfig($config);
        // 判断是否已经连接
        if (!isset(self::$instances[$name])) {
        	$client = new Connection($conn);
        	self::$instances[$name] = $client;
        }
        return self::$instances[$name];
    }

    /**
     * 数据库连接参数解析
     * @static
     * @access private
     * @param mixed $config
     * @return array
     */
    private static function parseConfig($config) {
        if (empty($config)) {
            $config = C(ES_CONFIG);
            if(isset($config[ES_DEFAULT])) {
                $config = $config[ES_DEFAULT];
            }
            return [ES_DEFAULT, $config];
        } elseif (is_string($config)) {
            // 支持读取配置参数
            $database = C(ES_CONFIG);
            return [$config, isset($database[$config]) ? $database[$config] : $database];
        } else {
        	if(!isset($config['hosts'])) {
                $name = key($config);
                $config = $config[$name];  
            } else {
                $name = md5(serialize($config));
            }
            return [$name, $config];
        }
    }

    // 调用驱动类的方法
    public static function __callStatic($method, $params) {
        return call_user_func_array([self::connect(), $method], $params);
    }

}