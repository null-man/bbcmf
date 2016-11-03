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

namespace app\dmp_admin\controller;
use bb\DB;

class DmpAdminController extends HTController
{
    var $_model;
    var $id;
    var $_other_operation;
    var $_onetoone;

    function _init(){
        parent::_init();
    }

    function __construct() {
        parent::__construct();
    }



    // + ---------------------------------------------
    // | 配置 - 模型
    // + ---------------------------------------------
    protected function set_Model($model = null) {
        if(is_string($model)){
            $this->_model = new $model;
        }else{
            $this->_model = $model;
        }

        return $this;
    }



    // + ---------------------------------------------
    // | 配置 - 标签栏
    // + ---------------------------------------------
    protected function set_navigations($navigations) {
        $this->_model->navs = $navigations;

        return $this;
    }


    // + ---------------------------------------------
    // | 配置 - 分页
    // + ---------------------------------------------
    protected function set_page($page_now = 1, $page_num = 20){
        $this->_model->page_now = $page_now;
        $this->_model->page_num = $page_num;
    }


    // + ---------------------------------------------
    // | 配置 - 分页url
    // + ---------------------------------------------
    protected function set_page_url($url){
        $this->_model->page_url = $url;
    }


    // + ---------------------------------------------
    // | 配置 - id
    // + ---------------------------------------------
    protected function set_id($id){
        $this->_model->_id = $id;
    }



    // + ---------------------------------------------
    // | 配置 - 过滤
    // + ---------------------------------------------
    protected function set_filter($filter = array()){
        foreach ($filter as $param => $value){
            if(!empty($value) || $value === "0"){
                if (is_array($value) && !empty($value[0])) {
                    $final_filter[$param] = explode(',', $value[0]);
                } elseif (is_string($value)){
                    $final_filter[$param] = $value;
                }
            }
        }

        if (!empty($final_filter)){
            $this->_model->filter = $final_filter;
        }
    }






    // + ---------------------------------------------
    // | 获取 - 分页记录总数
    // + ---------------------------------------------
    protected function get_page_count(){
        return $this->_model->page_count();
    }













    // + ---------------------------------------------
    // | 添加 - 表单
    // + ---------------------------------------------
    protected function add($assign = array(), $success_url = ''){
        if(IS_GET){
            return $this->render($assign['url']);
        }

        if(IS_POST){

            if (count($this->_onetoone) > 0){
                $onetoone_model = new $this->_onetoone[1];

                // 查询where数组 - [一对一]
                $one_where_arr = array();

                // 其他表操作 - [一对一]
                foreach ($this->_onetoone[0] as $one_k => $one_p){
                    $onetoone_model->$one_k = $one_p;

                    // 查询条件 - [一对一]
                    $one_where_arr[$one_k] = $one_p;
                }

                // 添加前 先查询是否存在该数据 - [一对一]
                $one_find_ret = $onetoone_model->where($one_where_arr)->get()->toArray();

                if( !empty($one_find_ret) ){
                    return $this->error("一对一操作: 数据已存在");
                }

                // 将 一对一的id 写到主表
                $onetoone_model->save();
                $onetoone_key = $this->_onetoone[2];

                $this->_model->$onetoone_key =$onetoone_model->id;
            }

            // 查询where数组 - [主表]
            $where_arr = array();

            // 添加
            foreach ($assign as $key => $params){
                $this->_model->$key = $params;
                // 查询条件
                $where_arr[$key] = $params;
            }

            // 添加前 先查询是否存在该数据
            $find_ret = $this->_model->where($where_arr)->get()->toArray();

            if( !empty($find_ret) ){
                return $this->error("主表操作: 数据已存在");
            }

            $this->_model->save();
            $ret_id = $this->_model->id;

            // 其他表操作 - [多对多]
            if (isset($this->_other_operation)){
                if ( !empty($this->_other_operation[0])){
                    foreach ($this->_other_operation[0] as $value){
                        $data[$this->_other_operation[2]] = $ret_id;
                        $data[$this->_other_operation[3]] = $value;

                        if(isset($this->_other_operation[4]) && $this->_other_operation[4] === true){
                            $data['created_at'] = date('Y-m-d H:i:s');
                            $data['updated_at'] = date('Y-m-d H:i:s');
                        }

                        $ret = DB::table($this->_other_operation[1])->insertGetId($data);
                        if(!$ret){
                            return $this->error("其他表操作: 添加失败");
                        }
                    }
                }else{
                    return $this->error("其他表操作: 参数类型错误" . $this->_other_operation[0]);
                }
            }

            return $ret_id ? $this->success('添加成功', $success_url) : $this->error("添加失败");
        }
    }




    // + ---------------------------------------------
    // | 编辑 - 表单
    // + ---------------------------------------------
    protected function edit($assign = array() ,$success_url = ''){
        if(IS_GET){
            return $this->render($assign['url'], $assign['params']);
        }

        if(IS_POST){
            $model = $this->_model->find($assign['id']);

            if (count($this->_onetoone) > 0){
                $onetoone_model_name = $this->_onetoone[1];

                $onetoone_model = $onetoone_model_name::find($model[$this->_onetoone[2]]);

                // 其他表操作 - [一对一]
                foreach ($this->_onetoone[0] as $one_k => $one_p){
                    $onetoone_model->$one_k = $one_p;
                }

                // 将 一对一的id 写到主表
                $onetoone_model->save();
            }

            foreach ($assign as $key => $params){
                $model->$key = $params;
            }

            $ret = $model->save();

            // 其他表操作 - [多对多]
            if (isset($this->_other_operation)){
                // 先删除所有的多对多
                DB::table($this->_other_operation[1])->where($this->_other_operation[2], $assign['id'])->delete();

                if(!empty($this->_other_operation[0])){
                    foreach ($this->_other_operation[0] as $value){
                        $data[$this->_other_operation[2]] = $assign['id'];
                        $data[$this->_other_operation[3]] = $value;

                        if(isset($this->_other_operation[4]) && $this->_other_operation[4] === true){
                            $data['created_at'] = date('Y-m-d H:i:s');
                            $data['updated_at'] = date('Y-m-d H:i:s');
                        }

                        $other_ret = DB::table($this->_other_operation[1])->insertGetId($data);
                        if(!$other_ret){
                            return $this->error("其他表操作: 更新失败");
                        }
                    }
                }
            }

            return $ret ? $this->success('更新成功', $success_url) : $this->error("更新失败");
        }
    }




    // + ---------------------------------------------
    // | 删除 - 表单
    // + ---------------------------------------------
    protected function del($id){
        $model = $this->_model->find($id);
        $model->delete();

        // 其他表操作 - [多对多]
        if (isset($this->_other_operation)){
            // 删除所有的多对多
            $del_ret = DB::table($this->_other_operation[0])->where($this->_other_operation[1], $id)->delete();

            return $del_ret ? 1 : 0;
        }

        return 1;
    }




    // + ---------------------------------------------
    // | 批量 - 删除
    // + ---------------------------------------------
    protected function list_del($ids = array()){
        foreach ($ids as $id){
            if ($this->del($id) === 0){
                return 0;
            }
        }

        return 1;
    }




    // + ---------------------------------------------
    // | 渲染 - 网页
    // + ---------------------------------------------
    protected function render($url, $url_params=array(), $tpl='tmpl/tmpl') {
        foreach ($url_params as $param => $value){
            if (!empty($value) || $value === "0") {
                $value = is_array($value) ? implode(',', $value) : $value;
                $url .= '/' . $param . '/' . $value;
            }
        }

        $this->assign('json_url', $url);
        return $this->fetch($tpl);
    }

    
    
    
    // + ---------------------------------------------
    // | 时间 - 组装 - 字符串
    // + ---------------------------------------------
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




    // + ---------------------------------------------
    // | 组装 - json
    // + ---------------------------------------------
    protected function build_json($name){
        if(!empty($this->_model)){
            return $this->_model->json_data($name);
        }
    }
}
