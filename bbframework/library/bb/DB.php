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

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class DB extends Capsule {

	public function __construct(Container $container = null) {
		parent::__construct($container);
    }

    /**
     * 数据库初始化 并取得数据库类实例
     * @static
     * @access public
     * @param mixed $config 连接配置
     * @return Object 返回数据库驱动类
     */
    public static function connect($config = []) {
        $name = self::connectName($config);
        // 返回数据库驱动类
        return self::connection($name);

    
    }

    /**
     * 关闭数据库
     * @static
     * @access public
     * @param mixed $config 连接配置
     * @return boolean
     */
    public static function disconnect($config = []) {

        // 解析连接配置名
        $name = self::parseConfigName($config);
        $connections = static::$instance->manager->getConnections();
        if(!(array_key_exists($name, $connections))) {
            return false;   
        }
        // 关闭数据库
        static::$instance->manager->purge($name);
        return true;
    }


    /**
     * 数据库连接参数解析
     * @static
     * @access private
     * @param mixed $config
     * @return array
     */
    private static function parseConfig($config)
    {
        if (empty($config)) {
            $config = C(DB_CONFIG);
            if(isset($config[DB_DEFAULT])) {
                $config = $config[DB_DEFAULT];
            }
            return [DB_DEFAULT, $config];
        } elseif (is_string($config) && false === strpos($config, '/')) {
            // 支持读取配置参数
            $database = C(DB_CONFIG);
            return [$config, isset($database[$config]) ? $database[$config] : $database];
        }
        if (is_string($config)) {
            $conn = self::parseDsn($config);
            return [md5(serialize($conn)), $conn];
        } else {
            if(!isset($config['driver'])) {
                $name = key($config);
                $config = $config[$name];  
            } else {
                $name = md5(serialize($config));
            }
            return [$name, $config];
        }
    }


    /**
     * DSN解析
     * 格式： mysql://username:passwd@localhost:3306/DbName?param1=val1&param2=val2#utf8#utf8_unicode_ci
     * @static
     * @access private
     * @param string $dsnStr
     * @return array
     */
    private static function parseDsn($dsnStr)
    {
        $info = parse_url($dsnStr);

        if (!$info) {
            return [];
        }

        $dsn = [
            'driver'     => $info['scheme'],
            'username' => isset($info['user']) ? $info['user'] : '',
            'password' => isset($info['pass']) ? $info['pass'] : '',
            'host' => isset($info['host']) ? $info['host'].':'.(isset($info['port']) ? $info['port'] : '') : '',
            // 'hostport' => isset($info['port']) ? $info['port'] : '',
            'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'  => isset($info['fragment']) ? explode('#', $info['fragment'])[0] : 'utf8',
            'collation'  => isset($info['fragment']) ? explode('#', $info['fragment'])[1] : 'utf8_unicode_ci',
        ];

        if (isset($info['query'])) {
            parse_str($info['query'], $dsn['params']);
        } else {
            $dsn['params'] = [];
        }
        return $dsn;
    }

    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array([static::connect(), $method], $parameters);
    }


    /**
     * 数据库连接名解析
     * @static
     * @access private
     * @param mixed $config
     * @return string
     */
    private static function parseConfigName($config)
    {
        if (empty($config)) {
            return DB_DEFAULT;
        } elseif (is_string($config) && false === strpos($config, '/')) {
            return $config;
        }
        if (is_string($config)) {
            $conn = self::parseDsn($config);
            return md5(serialize($conn));
        } else {
            if(!isset($config['driver'])) {
                $name = key($config);
            } else {
                $name = md5(serialize($config));
            }
            return $name;
        }
    }





    /**
     * Get a fluent query builder instance.
     *
     * @param  string  $table
     * @param  mixed  $config
     * @return \Illuminate\Database\Query\Builder
     */
    public static function table($table, $config = []) {
        return static::connect($config)->table($table);
    }

    /**
     * Get a schema builder instance.
     *
     * @param  mixed  $config
     * @return \Illuminate\Database\Schema\Builder
     */
    public static function schema($connection = null)
    {
        return static::connect($config)->getSchemaBuilder();
    }






    /**
     * 数据库初始化 并取得数据库连接名
     * @static
     * @access public
     * @param mixed $config 连接配置
     * @return Object 返回数据库驱动类
     */
    public static function connectName($config = []) {
        if(is_null(static::$instance)) {
            $capsule = new DB;
            // 设置全局静态可访问
            $capsule->setAsGlobal();
            // 启动Eloquent
            $capsule->bootEloquent();
        }
        // 解析连接配置
        list($name, $conn) = self::parseConfig($config);

        // 判断是否已经连接
        $connections = static::$instance->manager->getConnections();
        if(!(array_key_exists($name, $connections))) {
            // 连接数据库
            static::$instance->addConnection($conn, $name);
            APP_DEBUG && \think\Log::record('[ DB ] INIT ' . $name . ':' . var_export($conn, true), 'info');
            !APP_DEBUG && self::connection($name)->disableQueryLog();
        }

        // 返回数据库驱动类
        return $name;
    }



}