<?php
namespace bb\admin\controller;

use bb\admin\controller\Admin;
use bb\DB;

class BasicAdmin extends Admin {

    protected $navigations = [
        'config' => '基础配置',
        'task'   => '定时配置',
    ];

    protected $renders = [
        // 'table' => ['url' => 'page_list', 'vars' => ['page']],
        // 'add'   => ['url' => 'page_add', 'jump' => 'index'],
        // 'edit'  => ['url' => 'page_edit', 'vars' => ['id'], 'jump' => 'index'],

        'config'        => ['url' => 'page_list', 'vars' => ['page']],
        'config_edit'   => ['url' => 'page_edit', 'vars' => ['id'], 'jump' => 'config'],

        'task'          => ['url' => 'page_list', 'vars' => ['page']],
        'task_edit'     => ['url' => 'page_edit', 'vars' => ['id'], 'jump' => 'task'],

        'tasklog'       => ['url' => 'page_list_tasklog', 'vars' => ['page', 'id']],
        'tasklog_edit'  => ['url' => 'page_edit', 'vars' => ['id'], 'jump' => 'tasklog'],

    ];


    protected function is_module($module) {
        if($module == ACTION_NAME || $module == I('get.render')) return true;
        return strpos(ACTION_NAME, $module.'_') === 0 || strpos(I('get.render'), $module.'_') === 0;
    }

    
    public function _initialize() {

        if($this->is_module('config')) {
            $this->model    = '\\bb\\admin\\model\\Config';
        } elseif($this->is_module('task')) {
            $this->page     = 10;
            $this->filter   = ['url', 'is_on'];
            $this->model    = '\\bb\\admin\\model\\Task';
        } elseif($this->is_module('tasklog')) {
            $this->page     = 100;
            $this->filter   = ['task_id'];
            $this->model    = '\\bb\\admin\\model\\TaskLog';
        }

        parent::_initialize();

    }


    public function config() {
        return $this->render('config');
    }

    public function config_edit() {
        return $this->render('config_edit');
    }


    public function config_thread_on($id) {
        $ret = $this->model()->find($id);
        $ret->config = '1';

        return $this->modal_json($ret->save(), '开启操作');
//        return $this->jump($ret->save(), '开启', 'config');
    }

    public function config_thread_off($id) {
        $ret = $this->model()->find($id);
        $ret->config = '0';

        return $this->modal_json($ret->save(), '关闭操作');
//        return $this->jump($ret->save(), '关闭', 'config');
    }


    public function task() {
        return $this->render('task');
    }

    public function task_edit() {
        return $this->render('task_edit');
    }

    public function task_del() {
        return $this->del();
    }

    public function task_on($id) {
        $ret = $this->model()->find($id);
        $ret->is_on = 1;

        return $this->modal_json($ret->save(), '开启');
//        return $this->jump($ret->save(), '开启', 'task');
    }

    public function task_off($id) {
        $ret = $this->model()->find($id);
        $ret->is_on = 0;

        return $this->modal_json($ret->save(), '关闭');
//        return $this->jump($ret->save(), '关闭', 'task');
    }


    public function tasklog() {
        return $this->render('tasklog');
    }

    public function page_list_tasklog() {
        $id = I('id');
        if(!empty($id)) {
            $this->filter_value = ['task_id' => I('id')];
        }
        return $this->page_list();
    }

    public function tasklog_edit() {
        return $this->render('tasklog_edit');
    }

    public function tasklog_del() {
        return $this->del();
    }




    // + ---------------------------------------------
    // | 辅助 - 获取 crontab 规则结果
    // + ---------------------------------------------
    public function crontab_json(){
        $url = 'http://tool.lu/crontab/ajax.html';
        $post_data['expression']    =   I('expression', '* * * * *');
        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $res = $this->request_post($url, $post_data);

        return $res;
    }



    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

}