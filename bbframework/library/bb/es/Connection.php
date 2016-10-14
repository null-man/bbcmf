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

use bb\es\Query;
use bb\es\Indices;

class Connection {

	protected $query = null;

	// ES连接参数配置
    protected $config = [
        // 数据库类型
        'hosts'          => '',
        'prefix'         => '',
        'settings'       => []
    ];

	public function __construct($config = []) {

		if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
        $this->query = new Query($this);
	}

    /**
     * 获取前缀
     * @access public
     * @param string $config 配置名称
     * @return mixed
     */
	public function getPrefix() {
        return $this->config['prefix'];
    }


    /**
     * 获取索引配置
     * @access public
     * @param string $config 配置名称
     * @return mixed
     */
    public function getSettings() {
        return $this->config['settings'];
    }


    /**
     * 获取ES的配置参数
     * @access public
     * @param string $config 配置名称
     * @return mixed
     */
    public function getConfig() {
        $c = $this->config;
        unset($c['prefix']);
        unset($c['settings']);
        return $c;
    }


    /**
     * 调用Query类的查询方法
     * @access public
     * @param string $method 方法名称
     * @param array $args 调用参数
     * @return mixed
     */
    public function __call($method, $args) {
        return call_user_func_array([$this->query, $method], $args);
    }

    
    /**
     * 获得query类
     * @access public
     * @return bb\Query
     */
    public function getQuery() {
        return $this->query;
    }


    /**
     * ES结构
     * @access public
     * @return bb\Query
     */
    public function scheme() {
        return new Scheme($this, $this->query->getClient());
    }


    /**
     * ES连接信息
     * @access public
     * @return bb\Query
     */
    public function info() {
        return $this->query->_info();
    }
}