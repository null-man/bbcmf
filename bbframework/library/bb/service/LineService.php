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

namespace bb\service;

use bb\Service;

class LineService extends Service {


	protected $namespace = '';

	// 多服务
	protected $services = [];

	public function serviceName($name) {
		if(!empty($this->namespace)) {
			$service = $this->namespace . '\\' . $name . $this->suffix;
		} else {
			$service = $this->parseClassName($name . $this->suffix);
		}
		return $service;
	}

	/**
     * 添加service
     * @return void
     */
	public function add($service, $options = [], $callback = null) {
		if(is_string($service)) {
			$service = $this->serviceName($service);
			$service = new $service($options);
		}

		if($service instanceof Service) {
			$this->services[] = $service;
			if(!empty($callback)) 	$service->callback($callback);
			return $this;
		}

		return false;
	}


	/**
     * 运行
     * @access public
     * @param mixed $data 数据
     * @return $this
     */
	public function run($data) {
		$ret = false;
		if(empty($this->services)) {
			$ret = false;
		} else {
			$ret = $this->data;
			for($i = 0; $i < count($this->services); $i++) {
				$service = $this->services[$i];
				if($service->getData() instanceof \Closure) {
					$service->data_params($ret);
				} else {
					$service->data($ret);
				}
				$ret = $service->done();
			}
		}
		return $ret;
	}


	public static function create($name) {
		$cls = get_called_class();
		$s = new $cls();
		$cls = $s->serviceName($name);
		return new $cls();
	}

	public static function exists($name) {
		$cls = get_called_class();
		$s = new $cls();
		$cls = $s->serviceName($name);
		return class_exists($cls);	
	}

}