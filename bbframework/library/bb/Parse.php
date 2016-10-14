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


class Parse {

	// 参数
	protected $options = [];

	// 支持类型
	protected static $support = ['json' => 'bb\\parse\\Json'];

	// 注册支持类型
	public static function register($name, $cls) {
		$support[$name] = $cls;
	}

	// 创建实体解析类
	public static function create($name, $options = []) {
		$name = strtolower($name);
		$cls = Parse::$support[$name];
		if(empty($cls)) return null;
		return new $cls($options);
	}

	// 创建实体解析类另一种写法
	public static function __callStatic($method, $params) {
		return Parse::create($method, $params);
    }


    public function __construct($options = []) {
    	$this->options = array_merge($this->options, $options);
    }

    public function encode($data, $options = []) {
    	$this->options = array_merge($this->options, $options);
    	return $data;
    }

    public function decode($data, $options = []) {
    	$this->options = array_merge($this->options, $options);
    	return $data;
    }



}