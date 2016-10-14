<?php

namespace bb\net;
BT('bb/Pack');

use util\NetUtils;

use bb\Net;
use bb\Parse;

class Request {

	use \bt\bb\Pack;

	// 请求引擎
	protected $engine = Net::SNOOPY;

	// url
	protected $url = '';

	// header
	protected $header = [];

	// data
	protected $data = [];

	// cookie
	protected $cookie = [];



	/**
     * 设置请求引擎
     * @access public
     * @param mixed $params 参数
     */
	public function engine($engine) {
		$this->engine = $engine;
		return $this;
	}


	/**
     * 设置URL地址
     * @access public
     * @param string $url 参数
     */
	public function url($url) {
		$this->url = $url;
		return $this;
	}


	/**
     * 设置参数
     * @access public
     * @param mixed $data 参数
     */
	public function data($data) {
		$this->data = $data;
		return $this;
	}


	/**
     * 设置cookie
     * @access public
     * @param mixed $params 参数
     */
	public function cookie($cookie) {
		$this->cookie = array_merge($this->cookie, $cookie);
		return $this;
	}


	/**
     * 设置header
     * @access public
     * @param mixed $params 参数
     */
	public function header($header) {
		$this->header = array_merge($this->$header, $header);
		return $this;
	}


	public function get($url = '') {
		$url = empty($url) ? $this->url : $url;
		$type = $this->engine == Net::CURL ? 'curl' : 'snoopy';
		$ret = NetUtils::get($url, $type, $this->header, $this->cookie);
		return $this->unpack($ret);
	}


	public function post($url = '', $data = []) {
		$url = empty($url) ? $this->url : $url;
		$type = $this->engine == Net::CURL ? 'curl' : 'snoopy';
		$data = empty($data) ? $this->data : $data;
		$ret = NetUtils::post($url, $type, $data, $this->header, $this->cookie);
		return $this->unpack($ret);
	}

	
}