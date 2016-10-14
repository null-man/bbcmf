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


trait Cls
{

	/**
     * 获得类信息
     * @access public
     * @param string $ds 分隔符
     * @return string 返回类信息
     */
	public function getClass($ds = null) {
		$ret = get_class($this);
		return empty($ds) ? $ret : str_replace('\\', $ds, $ret);
	}

	/**
     * 获得类名
     * @access public
     * @return string 返回类名
     */
	public function getClassName() {
		return basename($this->getClass('/'));
	}


	/**
     * 获得命名空间
     * @access public
     * @return string 返回命名空间
     */
	public function getNamespace() {
		return str_replace('/', '\\', dirname($this->getClass('/')));
	}


	/**
     * 解析类名
     * @access public
     * @return string 返回类名
     */
	public function parseClassName($name) {
		if(is_string($name) && false === strpos($name, '\\')) {
			return $this->getNamespace().'\\'.$name;
		}
		return $name;
	}


}