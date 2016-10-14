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

use bb\Parse;

trait Pack {

	// 解析参数
	protected $_parse = '';
	protected $_parse_options = [];

	/**
     * 设置结果是json，用json解析
     * @access public
     * @param mixed $params 参数
     */
	public function json($options = []) {
		$this->_parse = 'json';
		$this->_parse_options = $options;
		return $this;
	}

	protected function pack($data) {
		if(empty($this->_parse)) return $data;
		return Parse::create($this->_parse, $this->_parse_options)->encode($data);
	}

	protected function unpack($data) {
		if(empty($this->_parse)) return $data;
		return Parse::create($this->_parse, $this->_parse_options)->decode($data);
	}

}
