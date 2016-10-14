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

namespace bb\es;

use bb\ES;
use bb\es\Builder;

class Scheme {

	// 索引前缀
	protected $prefix = '';

	// ES连接
	protected $connection = null;

	// ES客户端
	protected $client = null;

	// 索引客户端
	protected $indices = null;

	// 参数
	protected $options = [];

	// 默认客户端参数
    protected $params_clinet_default = ['ignore' => ['400', '404', '409']];

    // 原始数据输出
    protected $raw_output = false;

    // 节点客户端



    /**
     * 设置客户端参数
     * @access public
     * @param mixed $params 参数
     */
    public function setClinetDefault($params) {
    	$this->params_clinet_default = $params;
    }


    /**
     * 设置是否原始数据输出
     * @access public
     * @param boolean $raw 是否
     */
    public function output($raw = false) {
       	$this->output = $raw;
       	return $this;
    }


	public function __construct($connection = '', $client = null, $options = []) {
		$this->connection = $connection ?: ES::connect();
		$this->client = is_null($client) ? ClientBuilder::fromConfig($connection->getConfig()) : $client;
        $this->options = $options;
        // 设置前缀
        $this->prefix = $this->connection->getPrefix();
        // 索引客户端
        $this->indices = $this->client->indices();
	}

	
	/**
     * 指定索引
     * @access public
     * @param string $index 索引名 
     * @return $this
     */
    public function index($index, $withPrefix = true) {
    	if($withPrefix && !empty($this->prefix)) $index = $this->prefix . $index;
        $this->options['index'] = $index;
        return $this;
    }


    /**
     * 指定类型
     * @access public
     * @param string $index 索引名 
     * @return $this
     */
    public function type($type) {
    	$this->options['type'] = $type;
        return $this;
    }


    /**
     * 指定mappings
     * @access public
     * @param string $index 索引名 
     * @return $this
     */
    public function mapping($mapping, $raw = true) {
    	if(!$raw) {
    		$mapping = self::_mapping($mapping);
    	}
    	$this->options['mapping'] = $mapping;
        return $this;
    }


    /**
     * 指定filed
     * @access public
     * @param string $index 索引名 
     * @return $this
     */
    public function field($field) {
    	$this->options['field'] = $field;
        return $this;
    }

    /**
     * 指定settings
     * @access public
     * @param string $index 索引名 
     * @return $this
     */
    public function setting($setting) {
    	$this->options['setting'] = $setting;
        return $this;
    }


	/**
     * 是否存在索引 或 类型
     * @access public
     * @return $this
     */
	public function exists($index = '', $type = '') {
		if(!empty($index)) $this->index($index);
		if(!empty($type))  $this->type($type);
		$options = $this->parseOptions();
		$ret = false;
		if(isset($options['type'])) { // 类型
			$ret = $this->indices->existsType($options);
		} else { // 索引
			$ret = $this->indices->exists($options);
		}
		return $ret;
	}


	/**
     * 获得索引/类型信息
     * @access public
     * @return $this
     */
	public function get($index = '', $type = '', $field = '') {
		if(!empty($index)) $this->index($index);
		if(!empty($type))  $this->type($type);
		if(!empty($field)) $this->type($field);
		$options = $this->parseOptions();
		if(isset($options['field'])) {
			$ret = $this->indices->getFieldMapping($options);
		} elseif(isset($options['type'])) { // 类型
			$ret = $this->indices->getMapping($options);
		} else { // 索引
			$ret = $this->indices->get($options);
		}
		return $ret;
	}

	/**
     * 创建索引/类型
     * @access public
     * @return $this
     */
	public function create($index = '', $type = '', $mapping = []) {
		if(!empty($index)) $this->index($index);
		if(!empty($type))  $this->type($type);
		if(!empty($mapping)) $this->mapping($mapping);
		$options = $this->parseOptions();
		if(isset($options['type'])) { // 类型
			$dsl = $this->parseMapping($options);
			$ret = $this->indices->putMapping($dsl);
		} else { // 索引
			$dsl = $this->parseSetting($options);
			$ret = $this->indices->create($dsl);
		}
		return $ret;
	}


	/**
     * 删除索引/类型
     * @access public
     * @return $this
     */
	public function delete($index = '', $type = '') {
		if(!empty($index)) $this->index($index);
		if(!empty($type))  $this->type($type);
		$options = $this->parseOptions();
		if(isset($options['type'])) { // 类型
			$dsl['index'] = [$options['index']];
			$dsl['type'] = $options['type'];
			$ret = $this->indices->deleteMapping($dsl);
		} else { // 索引
			$dsl['index'] = $options['index'];
			$ret = $this->indices->delete($dsl);
		}
		return $ret;
	}


	/**
     * 更新索引/类型
     * @access public
     * @return $this
     */
	public function update($index = '', $type = '', $mapping = []) {
		if(!empty($index)) $this->index($index);
		if(!empty($type))  $this->type($type);
		if(!empty($mapping)) $this->mapping($mapping);
		$options = $this->parseOptions();
		if(isset($options['type'])) { // 类型
			$dsl = $this->parseMapping($options);
			// $dsl['ignore_conflicts'] = true;
			dump($dsl);
			$ret = $this->indices->putMapping($dsl);
		} else { // 索引
			$dsl = $this->parseSetting($options);
			$ret = $this->indices->putSettings($dsl);
		}
	}

	
	/**
     * 分析参数
     * @access public
     * @return $this
     */
	public function parseOptions() {

		$options = $this->options;

		// 添加默认客户端参数
        if(!isset($options['client']) && !empty($this->params_clinet_default)) {
            $options['client'] = $this->params_clinet_default;
        }

        $this->options = [];
        return $options;
	}


	/**
     * 生成mapping dsl
     * @access public
     * @return $this
     */
	protected function parseMapping($options) {
		$dsl['index'] = $options['index'];
		$dsl['type'] = $options['type'];
		$dsl['body'] = isset($options['mapping']) ? $options['mapping'] : [];
		return $dsl;
	}

	/**
     * 生成settings dsl
     * @access public
     * @return $this
     */
	protected function parseSetting($options) {
		$dsl['index'] = $options['index'];

		if(empty($options['setting'])) {
			$options['setting'] = $this->connection->getSettings();
		}

		$dsl['body'] = isset($options['setting']) ? $options['setting'] : [];
		return $dsl;
	}


	/**
     * 解析mapping值
     * @access public
     * @return $this
     */
	public static function _mapping($m, $root = true) {
		$ret = [];
		foreach ($m as $key => $value) {
			if(isset($value['type'])) {
				$ret[$key] = $value;
				if($value['type'] == 'date' && empty($ret[$key]['format'])) {
					$ret[$key]['format'] = 'yyyy-MM-dd HH:mm:ss';
				}
			} else {
				$ret[$key] = ['type' => 'object'];
				$ret[$key]['properties'] = self::_mapping($m[$key], false);
			}
		}
		if($root) {
			$x = [];
			$x['properties'] = $ret;
			return $x;
		}
		return $ret;
	}




}