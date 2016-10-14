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

BT('bb/Cls');
BT('bb/Pack');

use bb\service\LineService;

class Service {

	use \bt\bb\Cls;
	use \bt\bb\Pack;

	// Service后缀
	protected $suffix = 'Service';

	// 操作数据结构
	protected $data = [];
	// 操作数据结构参数
	protected $data_params = [];
	// 参数
	protected $options = [];
	// 回调
	protected $callback = [];


	public function __construct($options = []) {
		$this->options = array_merge($this->options, $options);
	}


	/**
     * 设置数据参数
     * @access public
     * @param mixed $data 数据
     * @return $this
     */
	public function data_params($data_params) {
		if(count($data_params) == 1) $data_params = [$data_params];
		$this->data_params = $data_params;
		return $this;
	}


	/**
     * 设置数据对象值
     * @access public
     * @param mixed $data 数据
     * @return $this
     */
	public function data($data, $data_params = []) {
		$this->data = $data;
		if(!empty($data_params)) $this->data_params($data_params);
		return $this;
	}


	public function getData() {
		return $this->data;
	}

	/**
     * 配置
     * @access public
     * @param mixed $options 配置
     * @return $this
     */
	public function options($options) {
		$this->options = array_merge($this->options, $this->data_params);
		return $this;
	}

	/**
     * 设置回调
     * @access public
     * @param mixed $callback 回调
     * @return $this
     */
	public function callback($callback) {
		if($callback instanceof \Closure) {
			$this->callback = $callback;
		}
		return $this;
	}

	/**
     * 执行
     * @access public
     * @param mixed $callback 反馈
     * @return $this
     */
	public function done($callback = null) {
		$data = $this->data;
		if($data instanceof \Closure) {
			$data = call_user_func_array($this->data, $this->data_params);
		}
		$result = $this->run($data);
		$callback = empty($callback) ? $this->callback : $callback;
		if(!empty($callback)) {
			if(is_string($callback)) {
				$result = call_user_func_array([$this, $callback], $result);
			} else {
				$result = call_user_func_array($callback, [$result]);
			}
		}

		if(empty($result)) {
			return $this;
		}
		// dump($this->pack($result));
		return $this->pack($result);
	}


	/**
     * 运行
     * @access public
     * @param mixed $data 数据
     * @return $this
     */
	public function run($data) {
		return $data;
	}


	public static function line($services = []) {
		return new LineService();
	}

}