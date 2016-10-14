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

use bb\kafka\Connection;

class Kafka {

	// kafka连接实例
	private static $instances = [];

	public function __construct() {

	}

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
            $config = C(KAFKA_CONFIG);
            if(isset($config[KAFKA_DEFAULT])) {
                $config = $config[KAFKA_DEFAULT];
            }
            return [KAFKA_DEFAULT, $config];
        } elseif (is_string($config)) {
            // 支持读取配置参数
            $database = C(KAFKA_CONFIG);
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

