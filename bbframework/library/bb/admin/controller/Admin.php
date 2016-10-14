<?php

// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NullYang <635384073@qq.com>
// +----------------------------------------------------------------------

namespace bb\admin\controller;
use bb\DB;
use bb\admin\controller\Base as BaseController;

class Admin extends BaseController {

    // service
    protected $service = 'admin_tmpl_service';

    // 模型
    protected $model;

    // 分页
    protected $page = 0;

    // 过滤器
    protected $filter = [];

    // 指定过滤值
    protected $filter_value = [];

    // 导航栏
    protected $navigations = [
        'index' => '首页', 
        'add'   => '添加',
    ];

    // 
    protected $renders = [
        'table' => ['url' => 'page_list', 'vars' => ['page']],
        'add'   => ['url' => 'page_add', 'jump' => 'index'],
        'edit'  => ['url' => 'page_edit', 'vars' => ['id'], 'jump' => 'index']
    ];


    public function _initialize() {
        parent::_initialize();
        if(!empty($this->model)) {
            $this->setModel($this->model);
        }
    }

    protected function addRenders($key, $value) {
        $this->renders[$key] = $value;
    }

    protected function setModel($model) {
        if(is_string($model)) {
            $model = new $model;
        }
        $this->service->setModel($model);
    }


    public function index() {
        // if(IS_GET) {
            return $this->render('table');
        // }
    }


    public function add() {
        if(IS_GET) {
            return $this->render('add');
        }
        if(IS_POST) {
            $ret = $this->service->insert(array_merge($_POST, $_FILES));
            return $this->jump($ret, '添加', $this->renders['add']['jump']);
        }
    }


    public function edit() {
        if(IS_GET) {
            return $this->render('edit');
        }

        if(IS_POST) {
            $ret = $this->service->update(I('id'), array_merge($_POST, $_FILES));
            $render = I('get.render');
            return $this->jump($ret, '修改', $this->renders[$render]['jump']);
        }
    }


    public function del() {

        if(IS_GET) {
            $id = I('get.id');
            $ret = $this->service->delete($id);
        }

        if(IS_POST) {
            $ids = $_REQUEST['id'];
            $ret = $this->service->delete($ids);
        }

        return json_encode(['status' => 1, 'info' => '']);

    }


    // 列表页面
    public function page_list() {
        $this->service->setNavigations($this->navigations);
        if($this->page > 0) {
            $render = I('get.render');
            $this->service->setPage(I('page', 1), $this->page);
        }
        if(!empty($this->filter)) {
            $this->service->setFilter($this->filter, $this->filter_value);
        }
        return $this->service->json_data('table');
    }


    // 添加页面
    public function page_add() {
        $this->service->setNavigations($this->navigations);
        return $this->service->json_data('add');
    }


    // 修改页面
    public function page_edit() {
        $this->service->setNavigations($this->navigations);
        $this->service->setID(I('id'));
        return $this->service->json_data('edit');
    }


    protected function render($type = 'table', $url = '') {
        if(empty($url)) {

            $r = $this->renders[$type];

            $url  = $r['url'];
            $vars = isset($r['vars']) ? $r['vars'] : [];

            if(strpos($url, 'page_list') !== false) {
                foreach($this->filter as $filter) {
                    array_push($vars, $filter);
                }
            }

            $params = [];
            foreach ($vars as $key) {
                if(isset($_REQUEST[$key])) {
                    $value = $_REQUEST[$key];
                    if(!empty($value) || $value === "0") {
                        $params[$key] = is_array($value) ? implode(',', $value) : $value;
                    }
                }
            }


            $params['action'] = md5(admin_url(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            $params['render'] = $type;
            $url = admin_url($url, $params);

        }
        $this->assign('json_url', $url);
        return $this->fetch('/tmpl');
    }


    // 根据结果跳转
    protected function jump($ret, $str, $url) {
        if(is_string($ret)) {
            return $this->error($ret);
        }
        if($ret) {
            return $this->success($str.'成功', admin_url($url));
        }
        return $this->error($str.'失败');
    }


    /**
     * 组装成日期格式 yyyy-mm-dd HH:mm:ss 或者 时间戳
     *
     * @param $name 参数名
     * @param bool $is_timestamp 是否显示为时间戳
     * @return bool|string
     */
    protected function date_build($name, $is_timestamp = false){
        if (!isset($name)){
            return false;
        }

        $date   = I($name, '1970-01-01');
        $hour   = I($name.'hh', '00');
        $min    = I($name.'mm', '00');
        $second = I($name.'ss', '00');

        $str = $date . ' ' . $hour . ':' . $min . ':' . $second;

        return $is_timestamp ? strtotime($str) : $str;
    }



    // 操作返回modal json数据
    protected function modal_json($ret, $str){

        if($ret){
            $status = 1;
            $info = $str.'成功';
        }else{
            $status = 0;
            $info = $str.'失败';
        }

        $ret = [
            'status' => $status,
            'info' => $info
        ];

        return json_encode($ret);
    }


    protected function model() {
        return $this->service->getModel();
    }

}
