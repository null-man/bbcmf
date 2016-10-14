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

namespace bt\bb;

trait Singleton
{
    protected static $instance = null;

    // 实例化（单例）
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($options);
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    protected function init() {}

    // 静态调用
    public static function __callStatic($method, $params)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        if(is_callable([self::$instance, $method])) {
            return call_user_func_array([self::$instance, $method], $params);
        } else {
            throw new \think\Exception("not exists method:" . $method);
        }
        
    }
}
