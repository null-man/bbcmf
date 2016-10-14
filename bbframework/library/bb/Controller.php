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

use think\Controller as TPController;

class Controller extends TPController {


    protected $view_render = true;


    /**
     * 架构函数
     * @access public
     */
    public function __construct() {

        if($this->view_render) {
            $this->view = $this->view();
        }

        // 控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                $this->beforeAction($options) :
                $this->beforeAction($method, $options);
            }
        }
    }


    protected function view() {
        return \think\View::instance(\think\Config::get('template'), \think\Config::get('view_replace_str'));
    }
    

	/**
     * 视图内容替换
     * @access public
     * @param string|array $content 被替换内容（支持批量替换）
     * @param string  $replace    替换内容
     * @return $this
     */
    public function replace($content, $replace = '') {
    	$this->view->replace($content, $replace);
    }



    public function success($msg = '', $url = null, $data = '', $wait = 1) {
        return \think\Response::success($msg, $data, $url, $wait);
    }


    public function error($msg = '', $url = null, $data = '', $wait = 1) {
        return \think\Response::error($msg, $data, $url, $wait);
    }

}