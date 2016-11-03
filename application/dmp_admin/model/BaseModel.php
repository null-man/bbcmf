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

namespace app\dmp_admin\model;

use app\dmp_admin\model\base\MBase;
use app\dmp_admin\model\base\MString;
//use util\TmplUtils;


class BaseModel extends MBase
{
    // Tmpl 单例
//    private static $instance = null;

    // 数据库结构
    protected $_model = array();

    // 额外字段
    protected $_extra = array();

    // 添加
    protected $_add = array();

    // 编辑
    protected $_edit = array();

    // 添加页面校验
    protected $add_validate = array();

    // 编辑页面校验
    protected $edit_validate = array();

    // 需要显示 却不可操作的字段
    protected $edit_disabled = array();

    // 多对多关系(列表、添加、编辑)
    protected $_manytomany = array();

    // 多对一关系
    protected $manytoone = array();

    // 提交url
    protected $submit_url = '';

    // 插入url
    protected $insert_url = '';

    // 更新url
    protected $update_url = '';

    // 列表
    protected $_list = array();

    // 批量操作
    protected $list_actions = array();

    // 数据解析关系
    protected $relations = array();

    // 表单结构
    protected $from_field = array();

    // 外键(添加 或者 编辑)
    protected $form_foreign = array();

    // 提交按钮
    protected $from_submit = array();

    // 一对一关系(添加、编辑、列表)
    protected $onetoone = array();

    // 过滤字段
    protected $filter_list = array();

    // 过滤提交url
    protected $filter_submit = "";




    // 标签栏
    public $navs = array();

    // 具体数据的id(给更新使用)
    public $_id = null;

    // 分页 - 页面显示数量
    public $page_num = null;

    // 分页 - 当前页数
    public $page_now = null;

    // 分页 - 分页url
    public $page_url = '';

    // 数据记录总数
    public $page_count = null;

    // 过滤条件
    public $filter = array();

    // 查询
    public $search = null;


    // 构造函数 初始化tmpl类
    // + ---------------------------------------------
    // | 构造函数 - 初始化 tmpl 类
    // + ---------------------------------------------
    function __construct()
    {
        parent::__construct();
//
//        if(is_null(self::$instance)){
//            self::$instance = new TmplUtils();
//            self::$instance = self::$instance->_init();
//        }
    }




    // + ---------------------------------------------
    // | 初始化配置 - 设置显示类型
    // + ---------------------------------------------
    protected function type($type = null){
        if(is_null($type)){
            return false;
        }
        self::$instance->$type();

        return $this;
    }




    // + ---------------------------------------------
    // | 初始化配置 - 设置标签栏
    // + ---------------------------------------------
    protected function navigations(){
        foreach ($this->navs as $nav){
            self::$instance->head_content($nav);
        }

        return $this;
    }




    // + ---------------------------------------------
    // | 初始化配置 -  设置submit url
    // + ---------------------------------------------
    protected function submit_url(){
        self::$instance->submit_url($this->submit_url);
        return $this;
    }




    // + ---------------------------------------------
    // | 初始化设置 - 更新url
    // + ---------------------------------------------
    protected function update_url(){
        self::$instance->submit_url($this->update_url);
        return $this;
    }




    // + ---------------------------------------------
    // | 初始化设置 - 添加url
    // + ---------------------------------------------
    protected function insert_url(){
        self::$instance->submit_url($this->insert_url);
        return $this;
    }





    // ------------------------ table ----------------

    // + ---------------------------------------------
    // | table - 设置表头
    // + ---------------------------------------------
    protected function table_body_head(){

        $thead_arr = array();
        foreach ($this->_list as $key){
            // 获取数据结构的字段
            if (array_key_exists($key,  $this->_model)){
                // onetoone另外组装
                if ($this->_model[$key][1] != 'onetoone' && $this->_model[$key][1] != 'hidden'){
                    array_push($thead_arr, $this->_model[$key][0]);
                }
            }

            // 获取额外字段(预留)
//            if (array_key_exists($key, $this->_extra)){
//                array_push($thead_arr, '操作1');
//            }

            // 多对多
            if (array_key_exists($key, $this->_manytomany)){
                array_push($thead_arr, $this->_manytomany[$key][0]);
            }
        }

        // 一对一关系 组装表头
        foreach ($this->onetoone as $key => $value){
            $onetoone_model_data = $this->onetoone[$key];
            $model = new $onetoone_model_data[0];

            foreach ($onetoone_model_data[1] as $value){
                if(array_key_exists($value, $model->get_model())){
                    array_push($thead_arr, $model->get_model()[$value][0]);
                }
            }
        }

        array_push($thead_arr, '操作');
        self::$instance->table_body_thead($thead_arr);

        return $this;
    }




    // + ---------------------------------------------
    // | table - 设置列操作
    // + ---------------------------------------------
    protected function list_action(){
        foreach ($this->list_actions as $list_action){
            self::$instance->table_operation($list_action);
        }

        return $this;
    }




    // + ---------------------------------------------
    // | table - 设置数据
    // + ---------------------------------------------
    protected function datas($data){
        self::$instance->table_body_tbody($data);
        return $this;
    }




    // + ---------------------------------------------
    // | table - 设置解析关系
    // + ---------------------------------------------
    protected function relation(){
        // 主表解析
        foreach ($this->relations as $relation){
            foreach ($relation as $model => $params){

                // 解析model格式
                if(method_exists($model, 'all')){

                    $_models = $model::all()->toArray();
                    $params_arr = array();

                    foreach ($_models as $_model){
                        foreach ($_model as $value){
                            $params_arr[$_model['id']] = $_model[$params[1]];
                        }
                    }
                    self::$instance->table_body_relation($params[0], '', $params_arr);
                    unset($params_arr);

                // 解析数组格式
                }else{
                    foreach ($this->$params as $key => $value){

                        $params_arr[$key] =  $value;
                    }
                    self::$instance->table_body_relation($model, '', $params_arr);
                    unset($params_arr);
                }
            }
        }

        // 设置 一对一 解析
        if(!empty($this->onetoone)){
            foreach ($this->onetoone as $_k => $value){
                $onetoone_model = new $value[0];

                foreach ($onetoone_model->get_relations() as $o_key => $o_model_arrays){
                    foreach ($o_model_arrays as $o_model_field => $o_model_array){
                        $method = 'get_'.$o_model_array;

                        $join_key = array($_k, $o_model_field);
                        self::$instance->table_body_relation(join('_', $join_key), '',  $onetoone_model->$method());
                    }

                }
            }
        }

        return $this;
    }




    // + ---------------------------------------------
    // | table - 设置过滤显示列表
    // + ---------------------------------------------
    protected function filter(){
        if(!empty($this->filter_list)){
            foreach ($this->filter_list as $value){

                // 过滤参数
                $filter_value = isset($this->filter[$value]) ? $this->filter[$value] : "";

                // 主表字段处理
                if(array_key_exists($value,$this->_model)){
                    $filed_name = $this->_model[$value][0];
                    $type = $this->_model[$value][1];

                    switch ($type){
                        case "str":
                            self::$instance->table_body_filter($value, $filed_name, $type, $filter_value);
                            break;

                        case "select":
                            self::$instance->table_body_filter($value, $filed_name, $type, $filter_value);
                            break;

                        case "radio":
                            self::$instance->table_body_filter($value, $filed_name, $type, $filter_value);
                            break;

                        default:
                            self::$instance->table_body_filter($value, $filed_name, $type, $filter_value);
                            break;
                    }
                }

                // 多对多处理
                if(array_key_exists($value, $this->_manytomany)){
                    $manytomany_arr = $this->_manytomany[$value];
                    self::$instance->table_body_filter($value, $manytomany_arr[0], 'checkbox', $filter_value);
                }
            }

            self::$instance->table_body_filter_url($this->filter_submit);
        }

        return $this;
    }




    // + ---------------------------------------------
    // | table manytomany 解析对应的数组
    // + ---------------------------------------------
    protected function list_manytomany($key, $id){
        $params = $this->_manytomany[$key];

        // 解析manytomany 对应的数组
        $v = $this->$params[1];
        $ret = $this->find($id)->belongsToMany($v[0], $v[1], $v[2], $v[3])->getResults()->toArray();

        $push_data = array();
        foreach ($ret as $value){
            array_push($push_data, strval($value['id']));
        }

        $ret = array(
            "type" => "checkbox",
            "value" => $push_data
        );

        return $ret;
    }




    // + ---------------------------------------------
    // | table - 单行操作
    // + ---------------------------------------------
    protected function actions($id){
        $tmp_arr = array();
        foreach ($this->actions as $action){
            $action_arr['type'] = 'link';
            $action_arr['showName'] = $action[0];
            $action_arr['opType'] = $action[1];
            $action_arr['url'] = $action[2] . '/' . $id;

            $action_arr['modal']['type'] = isset($action[3][0]) ? $action[3][0] : 'modal';
            $action_arr['modal']['title'] = isset($action[3][1]) ? $action[3][1] : '';
            $action_arr['modal']['desc'] = isset($action[3][2]) ? $action[3][2] : '';


            array_push($tmp_arr, $action_arr);
        }

        return $tmp_arr;
    }




    // + ---------------------------------------------
    // | table - 一对一
    // + ---------------------------------------------
    protected function table_onetoone($id, $key){
        $onetoone_model_data = $this->onetoone[$key];
        return $this->find($id)->hasOne($onetoone_model_data[0], 'id', $key)->select($onetoone_model_data[1])->getResults()->toArray();
    }




    // + ---------------------------------------------
    // | table - 数据装载
    // + ---------------------------------------------
    public function data_build(){

        // 当前模型的指针(显示指定的值)
        $select = array();
        foreach ($this->_list as $field){
            if(array_key_exists($field, $this->_model)){
                array_push($select, $field);
            }
        }

        $model_pointer = $this->select($select);

        // 过滤
        if (!empty($this->filter)) {
            dump($this->filter);
            foreach ($this->filter as $field => $flr){

                // 主表过滤 处理
                if(isset($this->_model[$field])){
                    $type = $this->_model[$field];

                    switch ($type){
                        case "str":
                            $model_pointer = $model_pointer->where($field, 'like', '%' . $flr . '%');
                            break;

                        case "select":
                            $model_pointer = $model_pointer->where($field, 'like', '%' . $flr . '%');
                            break;

                        default:
                            $model_pointer = $model_pointer->where($field, 'like', '%' . $flr . '%');
                            break;
                    }
                }


                // 多对多 过滤
                if(isset($this->_manytomany[$field])){

                    // 用来处理多对多数据
                    $tmp_model = $model_pointer->get();
                    $tmp_datas = $tmp_model->toArray();

                    $type = $this->_manytomany[$field];

                    // 解析 manytomany 对应的数组
                    $manytomany_arr = $this->$type[1];

                    $manytomany_fliter_arr = array();

                    if (empty($flr) || !is_array($flr)){
                        return;
                    }

                    foreach ($flr as $id){

                        // 获取多对多 过滤
                        $ret = $manytomany_arr[0]::find($id)->belongsToMany($this, $manytomany_arr[1], $manytomany_arr[3], $manytomany_arr[2])->getResults()->toArray();

                        // 组装所有符合条件的数据 (这里以id为主)
                        foreach ($ret as $value){
                            $flag = false;

                            foreach ($tmp_datas as $tmp_value){
                                if($tmp_value['id'] === $value['id']){
                                    $flag = true;
                                }
                            }

                            // 防止重复插入数据
                            if (!in_array($manytomany_fliter_arr, $value) && $flag) {
                                array_push($manytomany_fliter_arr, $value);
                            }
                        }
                    }


                    // 只有一个过滤值的特殊处理
                    if (count($flr) === 1){
                        $id_arr = array();
                        // 给查询指针添加过滤条件 这里以id为whereIn条件过滤
                        foreach ($manytomany_fliter_arr as $value){
                             array_push($id_arr, $value['id']);
                        }

                        $model_pointer = $model_pointer->whereIn('id', $id_arr);
                    }else{
                        // 多对多的id 集合
                        $manytomany_id_arr = array();
                        // 先取出所有符合条件的数据
                        foreach ($manytomany_fliter_arr as $value){

                            // 计数
                            $_count = 0;
                            foreach ($manytomany_fliter_arr as $_value){

                                if($value['id'] === $_value['id']){

                                    $_count += 1;

                                    if($_count === count($flr)){
                                        // 不重复添加 id
                                        if (!in_array($_value['id'], $manytomany_id_arr)){
                                            array_push($manytomany_id_arr, $_value['id']);
                                        }

                                        // 重置计数
                                        $_count = 0;
                                    }
                                }
                            }
                        }

                        // 给查询指针添加过滤条件 这里以id为whereIn条件过滤
                        $id_arr = array();
                        foreach ($manytomany_id_arr as $id){
                            array_push($id_arr, $id);
                        }

                        $model_pointer = $model_pointer->whereIn('id', $id_arr);
                    }
                }
            }
        }

        // 分页
        if (!is_null($this->page_num) && !is_null($this->page_now)){
            $page_now = ($this->page_now-1)*$this->page_num;

            // 记录总数
            $page_count = $model_pointer->count();

            // 分页数据
            $model_pointer = $model_pointer->skip($page_now)->take($this->page_num);

            // 计算总页数 = 记录总数 / 每页显示数量 有小数点则进一位
            $total_page = ceil($page_count/$this->page_num);

            self::$instance->table_body_page($total_page, $this->page_now, $this->page_url);
        }

        // 结束指针
        $final_pointer = $model_pointer->get();

        // 组装数据(根据list顺序来显示)
        $show_datas = array();
        foreach ($final_pointer->toArray() as $data){
            $tmp_arr = array();
            foreach ($data as $key => $value){
                // 配置 list里面的字段
                if (in_array($key, $this->_list)){
                    $values = array();
                    $value = strval($value);

                    switch ($this->_model[$key][1]){
                        case 'str':
                            $values['type'] = 'string';
                            $values['value'] = $value;
                            break;
                        case 'hidden':
                            $values['type'] = 'hidden';
                            $values['value'] = $value;
                            break;
                        case 'select':
                            $values['type'] = 'select';
                            $values['value'] = $value;
                            break;
                        case 'radio':
                            $values['type'] = 'radio';
                            $values['value'] = $value;
                            break;
                        case 'checkbox':
                            $values['type'] = 'checkbox';
                            $tmp = array();
                            array_push($tmp, $value);
                            $values['value'] = $tmp;
                            break;
                        case 'date':
                            $values['type'] = 'date';
                            $values['value'] = $value;
                            break;
                        case 'img':
                            $values['type'] = 'img';
                            $values['value'] = $value;
                            break;
                        case 'link':
                            $values['type'] = 'link';
                            $values['showName'] = $value;
                            $values['link'] = $value;
                            break;
                        case 'onetoone':
                            break;
                        default:
                            $values['type'] = 'string';
                            $values['value'] = $value;
                            break;
                    }

                    $tmp_arr[$key]=$values;
                }
            }
            
            foreach ($this->_manytomany as $key => $value){
                // 配置多对多里面的字段
                if (in_array($key, $this->_list)){
                    $tmp_arr[$key]=$this->list_manytomany($key, $data['id']);
                }
            }

            foreach ($this->onetoone as $key=>$value){
                $onetoone_data = $this->table_onetoone($data['id'], $key);

                $onetoone_model_data = $this->onetoone[$key];
                $model = new $onetoone_model_data[0];

                foreach ($onetoone_data as $_key=>$_value){
                    // onetoone 组装数据
                    if(in_array($_key, $onetoone_model_data[1])){

                        $type = $model->get_model()[$_key][1];
                        $_values = array();
                        $_value = strval($_value);

                        switch ($type){
                            case 'str':
                                $_values['type'] = 'string';
                                $_values['value'] = $_value;
                                break;
                            case 'select':
                                $_values['type'] = 'select';
                                $_values['value'] = $_value;
                                break;
                            case 'radio':
                                $_values['type'] = 'radio';
                                $_values['value'] = $_value;
                                break;
                            case 'checkbox':
                                $_values['type'] = 'checkbox';
                                $tmp = array();
                                array_push($tmp, $_value);
                                $_values['value'] = $tmp;
                                break;
                            case 'date':
                                $_values['type'] = 'date';
                                $_values['value'] = $_value;
                                break;
                            case 'img':
                                $_values['type'] = 'img';
                                $_values['value'] = $_value;
                                break;
                            case 'link':
                                $_values['type'] = 'link';
                                $_values['showName'] = $_value;
                                $_values['link'] = $_value;
                                break;
                            default:
                                $_values['type'] = 'string';
                                $_values['value'] = $_value;
                        }

                        // 外键的数据 防止和其他的数据冲突 所以重新处理key 将一对一的字段 下划线 拼接字段名
                        $join_key = array($key, $_key);
                        $tmp_arr[join('_',$join_key)]=$_values;
                    }
                }
            }

            $tmp_arr['operation'] = $this->actions($data['id']);

            array_push($show_datas, $tmp_arr);
        }

        return $show_datas;
    }

    // -----------------------------------------------










    // ------------------- add  ----------------------

    // + ---------------------------------------------
    // | 添加 - 表单字段
    // + ---------------------------------------------
    protected function from_add(){

        foreach ($this->_add as $key => $filed){

            // 是否需要校验
            $validate = in_array($filed, $this->add_validate);

            // 把需要添加的字段 拿出来 (从数据库结构)
            if(array_key_exists($filed, $this->_model)){

                $body = $this->_model[$filed];

                $body_arr = array($filed, $body[0], $validate, false);

                switch ($body[1]){
                    case "str":
                        MString::form_input($body_arr);
                        break;
                    case "radio":
                        $this->__relation($filed, $body[0], 'body_radio', $validate);
                        break;
                    case "select":
                        $this->__relation($filed, $body[0], 'body_select', $validate);
                        break;
                    case "checkbox":
                        $this->__relation($filed, $body[0], 'body_checkbox', $validate);
                        break;
                    case "file":
                        self::$instance->body_file($body_arr);
                        break;
                    case "date":
                        self::$instance->body_date($body_arr);
                        break;
                    case "email":
                        self::$instance->body_email($body_arr);
                        break;
                    case "onetoone":
                        $this->_from_onetoone_add();
                        break;
                    case "textarea":
                        self::$instance->body_textarea($body_arr, false);
                        break;
                    case "crontab":
                        self::$instance->body_crontab($body_arr);
                        break;
                    default:
                        self::$instance->body_text($body_arr);
                }
            }

            // 额外字段
            if(array_key_exists($filed, $this->_manytomany)){
                $body = $this->_manytomany[$filed];

                $this->__relation($filed, $body[0], 'body_block_checkbox', $validate);
            }
        }

        return $this;
    }


    // + ---------------------------------------------
    // | [辅助] 获取表单一对一字段 add
    // + ---------------------------------------------
    protected function _from_onetoone_add(){
        foreach ($this->onetoone as $filed => $model){
            // 一对一 名称
            $filed_name = $this->_model[$filed][0];

            // 一对一 表单字段
            $_model = new $model[0];

            foreach ($_model->get_add() as $value){
                if(array_key_exists($value, $_model->get_model())){
                    // 是否需要校验
                    $validate = in_array($value, $_model->get_add_validate());

                    $type = $_model->get_model()[$value][1];

                    $name = $_model->get_model()[$value][0];

                    $join_key = array($filed, $value);
                    $body_arr = array(join('_',$join_key), $name, $validate, false);

                    switch ($type){
                        case "str":
                            self::$instance->body_block_text($filed_name, $body_arr);
                            break;
                        case "crontab":
                            self::$instance->body_block_crontab($filed_name, $body_arr);
                            break;
                        case "select":
                            $relation = array();

                            foreach ($_model->get_relations() as $o_key => $o_model_arrays){
                                foreach ($o_model_arrays as $o_model_field => $o_model_array){

                                    // 数组的方式获取数据
                                    $method = 'get_'.$o_model_array;

                                    foreach ($_model->$method() as $key => $value){
                                        $tmp['name'] = $value;
                                        $tmp['value'] = $key;

                                        array_push($relation, $tmp);
                                    }
                                }
                            }

                            self::$instance->body_block_select($filed_name, $body_arr, $relation);
                            break;
                        default:
                            self::$instance->body_block_text($filed_name, $body_arr);
                    }
                }
            }
        }
    }

    // -----------------------------------------------




    // -------------- edit ---------------------------

    // + ---------------------------------------------
    // | 编辑 - 表单字段
    // + ---------------------------------------------
    protected function from_edit(){

        // 设置id
        $body = $this->_model['id'];
        $body_arr = array('id', $body[0], false, false);

        self::$instance->body_hidden($body_arr, $this->_id);

        // 设置编辑显示的字段和值
        foreach ($this->_edit as $key => $filed){
            // 是否需要校验
            $validate = in_array($filed, $this->edit_validate);

            // 是否需要 仅显示
            $disabled = in_array($filed, $this->edit_disabled);

            // 额外字段(多对多)
            if(array_key_exists($filed, $this->_manytomany)){
                $params = $this->_manytomany[$filed];

                // 解析manytomany 对应的数组
                $v = $this->$params[1];
                $ret = $this->find($this->_id)->belongsToMany($v[0], $v[1], $v[2], $v[3])->getResults()->toArray();

                // model 对应的 所有多对多的数据
                $manytomany_data = array();
                foreach ($ret as $value){
                    array_push($manytomany_data, strval($value['id']));
                }

                $model_data = $v[0]::all()->toArray();

                // 解析数组
                $relation = array();

                foreach ($model_data as $param){
                    $tmp_arr = array();

                    $tmp_arr['name'] = $param['name'];
                    $tmp_arr['value'] = $param['id'];
                    $tmp_arr['checked'] = in_array($param['id'], $manytomany_data)? true : false;

                    array_push($relation, $tmp_arr);
                }

                self::$instance->body_block_checkbox($params[0], array($filed . '[]', $params[0], $validate, $disabled), $relation);
            }

            if(array_key_exists($filed, $this->_model)){
                // 更新前需要查询该数据 再填充
                $model_data = $this->find(intval($this->_id))->toArray();
                $value = $model_data[$filed];
                $body = $this->_model[$filed];

                $body_arr = array($filed, $body[0], $validate, $disabled);

                switch ($body[1]){
                    case "str":
                        MString::form_input($body_arr, $value);
                        break;
                    case "radio":
                        $this->__relation($filed, $body[0], 'body_radio', $validate, $disabled, $value);
                        break;
                    case "select":
                        $this->__relation($filed, $body[0], 'body_select', $validate, $disabled, $value);
                        break;
                    case "checkbox":
                        $this->__relation($filed, $body[0], 'body_checkbox', $validate, $disabled, $value);
                        break;
                    case "file":
                        self::$instance->body_file($body_arr, $value);
                        break;
                    case "date":
                        self::$instance->body_date($body_arr, $value);
                        break;
                    case "email":
                        self::$instance->body_email($body_arr, $value);
                        break;
                    case "onetoone":
                        $this->_from_onetoone_edit();
                        break;
                    case "textarea":
                        self::$instance->body_textarea($body_arr, false, $value);
                        break;
                    case "crontab":
                        self::$instance->body_crontab($body_arr, $value);
                        break;
                    default:
                        self::$instance->body_text($body_arr, $value);
                }
            }
        }

        return $this;
    }



    // + ---------------------------------------------
    // | [辅助] 获取表单一对一字段 edit
    // + ---------------------------------------------
    protected function _from_onetoone_edit(){
        foreach ($this->onetoone as $filed => $model){
            // 一对一 名称
            $filed_name = $this->_model[$filed][0];

            // 更新前需要查询该数据 再填充
            $model_data = $this->find($this->_id)->toArray();
            // 获取 一对一 数据的id
            $_model_id = $model_data[$filed];

            // 一对一 model
            $_model_edit = $model[0]::find($_model_id)->toArray();

            // 调用执行方法的model
            $_model = new $model[0];

            // 设置id (可以使用主表的id 获取此id 所以可以暂时不设置)
//            $body_arr = array($filed . '_id', '', false);
//            self::$instance->body_hidden($body_arr, $_model_id);

            foreach ($_model->get_edit() as $value){
                if(array_key_exists($value, $_model->get_model())){
                    // 是否需要校验
                    $validate = in_array($value, $_model->get_edit_validate());

                    // 是否是 只显示
                    $disabled = in_array($value, $_model->get_edit_disabled());

                    $type = $_model->get_model()[$value][1];

                    $name = $_model->get_model()[$value][0];

                    $join_key = array($filed, $value);
                    $body_arr = array(join('_',$join_key), $name, $validate, $disabled);

                    switch ($type){
                        case "str":
                            MString::form_block_input($filed_name, $body_arr, $_model_edit[$value]);
                            break;
                        case "crontab":
                            self::$instance->body_block_crontab($filed_name, $body_arr, $_model_edit[$value]);
                            break;
                        case "select":
                            $relation = array();

                            foreach ($_model->get_relations() as $o_key => $o_model_arrays){
                                foreach ($o_model_arrays as $o_model_field => $o_model_array){
                                    // 数组的方式获取数据
                                    $method = 'get_'.$o_model_array;

                                    foreach ($_model->$method() as $t_key => $t_value){
                                        $tmp['name'] = $t_value;
                                        $tmp['value'] = $t_key;

                                        $tmp['selected'] = $_model_edit[$value] === $t_key ? true : false;
                                        array_push($relation, $tmp);
                                    }
                                }
                            }
                            self::$instance->body_block_select($filed_name, $body_arr, $relation);
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }

    // -----------------------------------------------




    // + ---------------------------------------------
    // | [辅助函数] - [表单] - 返回关系解析数据(外键)
    // + ---------------------------------------------
    private function __relation($filed, $name, $body_type, $validate = false, $disabled = false, $selected_id = false){

        // 从外键找
        if(array_key_exists($filed, $this->form_foreign)){
            $model = $this->form_foreign[$filed];

            // 解析数组
            $relation = array();

            // 解析model格式
            if(method_exists($model[0], 'all')){
                $model_data = $model[0]::all()->toArray();

                foreach ($model_data as $params){
                    $tmp_arr = array();

                    $tmp_arr['name'] = $params[$model[1]];
                    $tmp_arr['value'] = $params['id'];

                    if($body_type === 'body_select'){
                        $tmp_arr['selected'] = $params['id'] === $selected_id ? true : false;
                    }else{
                        $tmp_arr['checked'] = $params['id'] === $selected_id ? true : false;
                    }

                    array_push($relation, $tmp_arr);
                }

            // 解析自定义数组模式
            }else{
                foreach ($this->$model as $key => $value){
                    $tmp['name'] = $value;
                    $tmp['value'] = $key;

                    if($body_type === 'body_select'){
                        $tmp['selected'] = $key === $selected_id ? true : false;
                    }else{
                        $tmp['checked'] = $key === $selected_id ? true : false;
                    }

                    array_push($relation, $tmp);
                }
            }

            $filed = ($body_type === 'body_block_checkbox') ? $filed . '[]' : $filed;

            if ($body_type === 'body_block_checkbox') {
                self::$instance->$body_type($name, array($filed, $name, $validate, $disabled), $relation);
            } else {
                self::$instance->$body_type(array($filed, $name, $validate, $disabled), $relation);
            }

        }
    }




    // + ---------------------------------------------
    // | 表单 - 提交
    // + ---------------------------------------------
    protected function from_submit(){
//        self::$instance->body_submit($this->from_submit);
        self::$instance->body_submit(array('submit','提交'));
        return $this;
    }




    // ------------------ 数据组装 --------------------

    // + ---------------------------------------------
    // | 数据 - 组装完成
    // + ---------------------------------------------
    protected function _done(){
        return self::$instance->done();
    }




    // + ---------------------------------------------
    // | 数据 - 组装成json
    // + ---------------------------------------------
    public function json_data($type){
        $ret = $this->type($type);

        switch ($type){
            case 'table':
                $ret = $ret->submit_url()
                           ->navigations()
                           ->list_action()
                           ->table_body_head()
                           ->datas($this->data_build())
                           ->relation()
                           ->filter()
                           ->_done();
                break;
            case 'add':
                $ret = $ret->insert_url()
                           ->navigations()
                           ->from_add()
                           ->from_submit()
                           ->_done();
                break;
            case 'edit':
                $ret = $ret->update_url()
                           ->navigations()
                           ->from_edit()
                           ->from_submit()
                           ->_done();
                break;
        }

//        dump($ret);
        return json_encode($ret);
    }

}