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

namespace bb\admin\model;

use bb\form\ListForm;
use bb\form\Form;
use bb\Model;
use think\Exception;

class AdminModel extends Model
{

    // 数据库结构
    protected $model = array();

    // 额外字段
    protected $_extra = array();

    // 前端验证
    protected $front_validate = [];

    // 多对多关系(列表、添加、编辑)
    protected $manytomany = array();

    // 多对一关系
    protected $manytoone = array();

    // 提交url
    protected $submit_url = '';

    // 插入url
    protected $insert_url = 'add';

    // 更新url
    protected $update_url = 'edit';

    protected $actions = [
        ['编辑', 'edit', ''],
        ['删除', 'del',  ['modal', '警告', '确定是否删除？']]
    ];

    // 批量操作
    protected $list_actions = [];

    // 列表
    protected $list = array();

    // 数据不可重复
    protected $duplication = false;

    // 数据解析关系
    protected $relations = array();

    // 表单结构
    protected $from_field = array();

    // 外键(添加 或者 编辑)
    protected $foreign = array();

    // 提交按钮
    protected $from_submit = array();

    // 一对一关系(添加、编辑、列表)
    protected $onetoone = array();

    // 过滤字段
    protected $filter_list = array();

    // 仅显示字段
    protected $edit_disabled = array();

    // 过滤提交url
    protected $filter_submit = 'index';






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
    }



    // ------------------------ table ----------------

    // + ---------------------------------------------
    // | table - 设置表头
    // + ---------------------------------------------
    protected function table_body_head(){

        $thead_arr = array();
        foreach ($this->list as $key){
            // 获取数据结构的字段
            if (array_key_exists($key,  $this->model)){
                // onetoone另外组装
                if ($this->model[$key][1] != 'onetoone' && $this->model[$key][1] != 'hidden'){
                    array_push($thead_arr, $this->model[$key][0]);
                }
            }

            // 多对多
            if (array_key_exists($key, $this->manytomany)){
                array_push($thead_arr, $this->manytomany[$key][0]);
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

//        dump($thead_arr);
        return $thead_arr;
    }




    // + ---------------------------------------------
    // | table - 设置解析关系
    // + ---------------------------------------------
    protected function relation($form) {
        // 主表解析
        $this->relations = $this->get_relations();

        foreach ($this->relations as $relation){

            foreach ($relation as $model => $params){

                $params_arr = [];
                // 解析model格式
                if(method_exists($model, 'all')){
                    $_models = $model::all()->toArray();

                    foreach ($_models as $_model){
                        foreach ($_model as $value){
                            $params_arr[$_model['id']] = $_model[$params[1]];
                        }
                    }
                    $name = $params[0];
                // 解析数组格式
                }else{
                    foreach ($this->$params as $key => $value){
                        $params_arr[$key] =  $value;
                    }
                    $name = $model;
                }

                $form->relation($name, $params_arr);
                unset($params_arr);
            }
        }

        // 设置 一对一 解析
        if(!empty($this->onetoone)){
            foreach ($this->onetoone as $_k => $value){
                $onetoone_model = new $value[0];

                foreach ($onetoone_model->get_relations() as $o_key => $o_model_arrays){
                    foreach ($o_model_arrays as $o_model_field => $o_model_array){
                        $method = '_rget_'.$o_model_array;

                        $join_key = array($_k, $o_model_field);
                        // dump($join_key);
                        // dump($onetoone_model->$method());
                        $form->relation(join('_', $join_key), $onetoone_model->$method());
                    }

                }
            }
        }

        return $this;
    }




    // + ---------------------------------------------
    // | table - 设置过滤显示列表
    // + ---------------------------------------------
    protected function filter($form){
        if(!empty($this->filter_list)){
            foreach ($this->filter_list as $value){
                // 过滤参数
                $filter_value = isset($this->filter[$value]) ? $this->filter[$value] : "";

                // 主表字段处理
                if(array_key_exists($value,$this->model)){
                    $filed_name = $this->model[$value][0];
                    $type = $this->model[$value][1];


                    $form->filter($value, $filed_name, $type, $filter_value);
                }
                
                // 多对多处理
                if(array_key_exists($value, $this->manytomany)){
                    $manytomany_arr = $this->manytomany[$value];

                    $form->filter($value, $manytomany_arr[0], 'checkbox', $filter_value);

                }
            }

            if(empty($this->filter_submit)) {
                $this->filter_submit = 'index';
            }

        }

        return $this;
    }




    // + ---------------------------------------------
    // | table manytomany 解析对应的数组
    // + ---------------------------------------------
    public function list_manytomany($key, $id){
        $params = $this->manytomany[$key];

        // 解析manytomany 对应的数组
        $v = $this->$params[1];
        $ret = $this->find($id)->belongsToMany($v[0], $v[1], $v[2], $v[3])->getResults()->toArray();

        $push_data = array();
        foreach ($ret as $value){
            array_push($push_data, strval($value['id']));
        }

//        $ret = array(
//            "type" => "checkbox",
//            "value" => $push_data
//        );
        $ret = array('checkbox', $push_data);

        return $ret;
    }




    // + ---------------------------------------------
    // | table - 单行操作
    // + ---------------------------------------------
    public function actions($id, $form){
        $tmp_arr = array();
        foreach ($this->actions as $action){
            
            if(is_string($action)) {
                $action = call_user_func_array([$this, $action], [$id]);
            }

            $action[1] = admin_url($action[1], ['id' => $id]);

            array_push($tmp_arr, $form->__action($action));
            
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
    // | table - 过滤 - 分页
    // + ---------------------------------------------
    public function handle($form){
        $select = array();

        // 根据model顺序显示
        foreach ($this->list as $field){
            if(array_key_exists($field, $this->model)){
                array_push($select, $field);
            }
        }

        $model_pointer = $this->select($select);

        // 过滤
        if (!empty($this->filter)) {
            foreach ($this->filter as $field => $flr){

                // 主表过滤 处理
                if(isset($this->model[$field])){
                    $type = $this->model[$field][1];

                    if($type == 'select' || $type == 'radio'){
                        $model_pointer = $model_pointer->where($field, $flr);
                    }else{
                        $model_pointer = $model_pointer->where($field, 'like', '%' . $flr . '%');
                    }
                }

                $_manytomany = $this->get_manytomany();
                // 多对多 过滤
                if(isset($_manytomany[$field])){

                    // 用来处理多对多数据
                    $tmp_model = $model_pointer->get();
                    $tmp_datas = $tmp_model->toArray();

                    $_manytomany_config = $_manytomany[$field];

                    // 多对多 model
                    $m_model = $_manytomany_config[0];
                    // 多对多 关系表名
                    $m_mid = $_manytomany_config[1];
                    // 多对多 主表id
                    $m_id = $_manytomany_config[2];
                    // 多对多 外键表id
                    $m_fid = $_manytomany_config[3];

                    $manytomany_fliter_arr = array();

                    if (empty($flr)){
                        return;
                    }

                    if(is_string($flr)){
                        $flr = explode(',', $flr);
                    }

                    foreach ($flr as $id){

                        // 获取多对多 过滤
                        $ret = $m_model::find($id)->belongsToMany($this, $m_mid, $m_fid, $m_id)->getResults()->toArray();

                        // 组装所有符合条件的数据 (这里以id为主)
                        foreach ($ret as $value){
                            $flag = false;

                            foreach ($tmp_datas as $tmp_value){
                                if($tmp_value['id'] === $value['id']){
                                    $flag = true;
                                }
                            }

                            // 防止重复插入数据
                            if ($flag) {
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

            $page = [
                $total_page,
                $this->page_now,
                $this->page_url
            ];

            $form->page($page);
        }

        // 结束指针
        return $model_pointer->get()->toArray();

    }


    // + ---------------------------------------------
    // | table - 数据装载
    // + ---------------------------------------------
    public function data_build($form, $data_array){
        // 组装数据
        foreach ($data_array as $data){
            $tmp_arr = array();
            foreach ($data as $key => $value){
                // 配置 list里面的字段
                if (in_array($key, $this->list)){
                    $value = strval($value);
                    $type = $this->model[$key][1];

                    $tmp_arr[$key]= $form->__row($type, $value);
                }
            }

            foreach ($this->manytomany as $key => $value){
                // 配置多对多里面的字段
                if (in_array($key, $this->list)){
                    $tmp_arr[$key]=$this->list_manytomany($key, $data['id']);
                }
            }

            foreach ($this->onetoone as $key=>$value){
                $onetoone_data = $this->table_onetoone($data['id'], $key);

                $onetoone_model_data = $this->onetoone[$key];
                $model = new $onetoone_model_data[0];

                foreach ($onetoone_data as $_key=>$_value){
                    // onetoone 组装数据
                    if(in_array($_key, $model->get_list())){

                        $type = $model->get_model()[$_key][1];
                        $_value = strval($_value);

                        // 外键的数据 防止和其他的数据冲突 所以重新处理key 将一对一的字段 下划线 拼接字段名
                        $join_key = array($key, $_key);
                        $tmp_arr[join('_',$join_key)] = $form->__row($type, $_value);

                    }
                }
            }

            $tmp_arr['operation'] = $this->actions($data['id'], $form);


            $form->line($tmp_arr);
            unset($tmp_arr);
        }

        return $this;
    }



    public function table(){
        $form = new ListForm();

        $form->open()
             ->list_action($this->list_actions)
             ->navs($this->navs)
             ->filter_url(admin_url($this->filter_submit))
             ->thead($this->table_body_head());

        $this->data_build($form, $this->handle($form))
             ->relation($form)
             ->filter($form);

        return $form->close();
    }

    // -----------------------------------------------




    // ------------------- add  ----------------------

    // + ---------------------------------------------
    // | 添加 - 表单字段
    // + ---------------------------------------------
    protected function add(){

        if(empty($this->insert_url)) {
            $url = admin_url('add');
        } else {
            $url = admin_url($this->insert_url, ['render' => I('render', '')]);
        }

        $form = new Form();
        $form->open('add')
             ->url($url)
             ->navs($this->navs);


        foreach ($this->field as $key => $field) {
            // 是否需要校验
            $validate = in_array($field, $this->front_validate);

            if (array_key_exists($field, $this->model)) {
                $body = $this->model[$field];
                $type = $body[1];
                $name = $body[0];
                $body_type = '' . $type;
                

                if ($type == 'select' || $type == 'radio' || $type == 'checkbox') {
                    $this->__relation($field, $name, $body_type, $form, $validate);
                } else if ($type == 'onetoone') {
                    $this->__add_onetoone($form);
                } else {
                    $form->add($type, $field, $name, '', $validate);
                }
            }


            // 额外字段
            if(array_key_exists($field, $this->manytomany)){
                $body = $this->manytomany[$field];

                $this->__relation($field, $body[0], 'block_checkbox', $form, $validate);
            }

            // 多对一
            if(array_key_exists($field, $this->get_manytoone())){
                $this->__add_manytoone($form, $field);
            }

//                foreach ($manytoone->get_model() as $_field => $_value){
//                    $name = $_value[0];
//                    $type = $_value[1];
//
//                    if (in_array($_field, $manytoone->get_field()) && $_field != $_manytoone[1]){
//                        if($type == 'radio' || $type == 'select'){
//                            $_model = $manytoone->get_foreign()[$_field];
//                            $model_data = $_model[0]::all()->toArray();
//
//                            $push_data = array();
//                            foreach ($model_data as $value){
//                                $tmp = array();
//                                foreach ($value as $k => $v){
//                                    if ($k == $_model[1]){
//                                        array_push($tmp, $v, $value['id']);
//                                    }
//                                }
//                                array_push($push_data, $tmp);
//                            }
//                        }else{
//                            $push_data = '';
//                        }
//
//                        $form->add_many2one($type, '', [$_field, $name], $push_data);
//                    }
//                }
//            }
        }

        return $form->submit()
                    ->close();
    }


    // + ---------------------------------------------
    // |
    // + ---------------------------------------------
    public function __add_manytoone($form, $field){
        $_manytoone = $this->get_manytoone()[$field];

        $manytoone = new $_manytoone[0];
//        $form->many2one_name($field);

        foreach ($manytoone->get_field() as $_value){
            // 主表字段
            if (array_key_exists($_value, $manytoone->get_model()) && $_value != $_manytoone[1]){
                $name = $manytoone->get_model()[$_value][0];
                $type = $manytoone->get_model()[$_value][1];
                $push_data = array();

                if($type == 'radio' || $type == 'select'){
                    $_model = $manytoone->get_foreign()[$_value];
                    if(method_exists('all', $_model)){
                        $model_data = $_model[0]::all()->toArray();

                        foreach ($model_data as $value){
                            $tmp = array();
                            foreach ($value as $k => $v){
                                if ($k == $_model[1]){
                                    array_push($tmp, $v, $value['id']);
                                }
                            }
                            array_push($push_data, $tmp);
                        }
                    }else{
                        foreach ($manytoone->$_model as $_k => $_v){
                            $tmp = array();
                            array_push($tmp, $_v, $_k);
                            array_push($push_data, $tmp);
                        }
                    }
                }else{
                    $push_data = '';
                }

                $form->add_many2one($type, $field, 'add', [$_value, $name], $push_data);
                unset($push_data);
            }

            // 多对多处理
            if(array_key_exists($_value, $manytoone->manytomany)){
                $name = $manytoone->manytomany[$_value][0];
                $config = $manytoone->manytomany[$_value][1];

                $config = $manytoone->$config;

                $manytomany_data = $config[0]::all()->toArray();
                $push_data = array();
                foreach ($manytomany_data as $value){
                    $tmp = array();
                    foreach ($value as $k => $v){
                        if ($k == $manytoone->get_foreign()[$_value][1]){
                            array_push($tmp, $v, $value['id']);
                        }
                    }
                    array_push($push_data, $tmp);
                }

                $form->add_many2one('checkbox', $field, 'add', [$_value, $name], $push_data);
            }

        }
    }

    // + ---------------------------------------------
    // | onetoone
    // + ---------------------------------------------
    public function __add_onetoone($form){

        if(!isset($form)){
            throw new Exception('__add_onetoone : param[form] not set!');
        }

        foreach ($this->onetoone as $filed => $model) {
            // 一对一 名称
            $filed_name = $this->model[$filed][0];

            // 一对一 表单字段
            $_model = new $model[0];

            foreach ($_model->get_field() as $value){
                if(array_key_exists($value, $_model->get_model())){
                    // 是否需要校验
                    $validate = in_array($value, $_model->get_front_validate());

                    $type = $_model->get_model()[$value][1];

                    $name = $_model->get_model()[$value][0];

                    $join_key = array($filed, $value);
                    $body_arr = array(join('_',$join_key), $name, $validate, false);

                    if($type == 'select'){
                        $relation = array();

                        foreach ($_model->get_relations() as $o_key => $o_model_arrays){
                            foreach ($o_model_arrays as $o_model_field => $o_model_array){

                                // 数组的方式获取数据
                                $method = '_rget_'.$o_model_array;

                                foreach ($_model->$method() as $key => $value){
                                    $tmp['name'] = $value;
                                    $tmp['value'] = $key;

                                    array_push($relation, $tmp);
                                }
                            }
                        }
                    }
                    $relation = isset($relation) ? $relation : '';

                    $form->add_block($type, $filed_name, $body_arr, $relation);

                    unset($relation);

                }
            }
        }
    }


    // -----------------------------------------------




    // -------------- edit ---------------------------

    // + ---------------------------------------------
    // | 编辑 - 表单字段
    // + ---------------------------------------------
    protected function edit(){
        if(empty($this->update_url)) {
            $url = admin_url('edit');
        } else {
            $url = admin_url($this->update_url, ['render' => I('render', '')]);
        }

        $form = new Form();
        $form->open('edit')
             ->url($url)
             ->navs($this->navs);

        // 设置id
        $body = $this->model['id'];
        $form->hidden(array('id', $body[0], false), $this->_id);

        // 设置编辑显示的字段和值
        foreach ($this->field as $key => $filed){
            // 是否需要校验
            $validate = in_array($filed, $this->front_validate);

            // 是否需要 仅显示
            $disabled = in_array($filed, $this->edit_disabled);

            // 额外字段(多对多)
            if(array_key_exists($filed, $this->manytomany)){
                $_params = $this->manytomany[$filed];

                $v = $this->get_edit_manytomany_relation($_params);

                // 解析manytomany 对应的数组
                $ret = $this->get_edit_manytomany_data($this->_id, $_params);

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

                    $tmp_arr['name'] = $param[$this->foreign[$filed][1]];
                    $tmp_arr['value'] = $param['id'];
                    $tmp_arr['checked'] = in_array($param['id'], $manytomany_data)? true : false;

                    array_push($relation, $tmp_arr);
                }

                $form->block_checkbox($_params[0], array($filed . '[]', $_params[0], $validate, $disabled), $relation);
            }

            if(array_key_exists($filed, $this->model)){
                // 更新前需要查询该数据 再填充
                $model_data = $this->get_edit_data($this->_id);

                $value = $model_data[$filed];
                $body = $this->model[$filed];
                $type = $body[1];
                $name = $body[0];


                if ($type == 'select' || $type == 'radio' || $type == 'checkbox'){
                    $this->__relation($filed, $name, $type, $form, $validate, $disabled, $value);
                }else if($type == 'onetoone'){
                    $this->__edit_onetoone($form);
                }else{
                    $form->add($type, $filed, $name, $value, $validate, $disabled);
                }

            }


            // 多对一
            if(array_key_exists($filed, $this->get_manytoone())){
                $_manytoone = $this->get_manytoone()[$filed];

                $manytoone = new $_manytoone[0];
//                $form->many2one_name($filed);
                $model_data = $manytoone::where($_manytoone[1], $this->_id)->get()->toArray();

                // 编辑时 数据为空时的处理
                if(empty($model_data)){
                    $this->__add_manytoone($form, $filed);
                }else{
                    foreach ($model_data as $key => $value){
                        $form->add_many2one('hidden', $filed, $key+1, ['id', 'id'], $value['id']);

                        foreach ($value as $k => $v){
                            // 主表数据
                            if(in_array($k, $manytoone->get_field()) && $k != $_manytoone[1]){
                                $_type = $manytoone->get_model()[$k][1];
                                $_name = $manytoone->get_model()[$k][0];
                                $push_data = array();

                                if($_type == 'radio' || $_type == 'select' || $_type == 'checkbox'){
                                    $_model = $manytoone->get_foreign()[$k];
                                    if(method_exists('all', $_model)){
                                        $model_data = $_model[0]::all()->toArray();
                                        foreach ($model_data as $value){
                                            $tmp = array();
                                            foreach ($value as $k => $v){
                                                if ($k == $_model[1]){
                                                    array_push($tmp, $v, $value['id']);
                                                }
                                            }
                                            array_push($push_data, $tmp);
                                        }
                                    }else{
                                        foreach ($manytoone->$_model as $_k => $_v){
                                            $tmp = array();
                                            $flag = false;
                                            if ($_k == $v){
                                                $flag = true;
                                            }
                                            array_push($tmp, $_v, $_k, $flag);
                                            array_push($push_data, $tmp);
                                        }
                                    }
                                    
                                    $v = $push_data;
                                }


                                $form->add_many2one($_type, $filed, $key+1, [$k, $_name], $v);
                                unset($push_data);
                            }
                        }

                        foreach ($manytoone->get_field() as $_value){
                            if(array_key_exists($_value, $manytoone->manytomany)){
                                $name = $manytoone->manytomany[$_value][0];
                                $config = $manytoone->manytomany[$_value][1];

                                $config = $manytoone->$config;

                                $ret = $manytoone::find($value['id'])->belongsToMany($config[0], $config[1], $config[2], $config[3])->getResults()->toArray();


                                // model 对应的 所有多对多的数据
                                $ret_data = array();
                                foreach ($ret as $value){
                                    array_push($ret_data, strval($value['id']));
                                }

                                $manytomany_data = $config[0]::all()->toArray();
                                $push_data = array();

                                foreach ($manytomany_data as $__value){
                                    $tmp = array();
                                    foreach ($value as $k => $v){
                                        if ($k == $manytoone->get_foreign()[$_value][1]){
                                            $flag = false;
                                            if (in_array($__value['id'], $ret_data)){
                                                $flag = true;
                                            }
                                            array_push($tmp, $__value[$k], $__value['id'], $flag);
                                        }
                                    }
                                    array_push($push_data, $tmp);
                                }
                                
                                $form->add_many2one('checkbox', $filed, $key+1, [$_value, $name], $push_data);
                            }

                        }
                        unset($push_data);
                    }

                }

            }
        }
        
        return $form->submit()
                    ->close();

    }



    // + ---------------------------------------------
    // | [辅助] 获取表单一对一字段 edit
    // + ---------------------------------------------
    protected function __edit_onetoone($form){

        foreach ($this->onetoone as $filed => $model){
            // 一对一 名称
            $filed_name = $this->model[$filed][0];

            // 更新前需要查询该数据 再填充
            $model_data = $this->get_edit_data($this->_id);
            // 获取 一对一 数据的id
            $_model_id = $model_data[$filed];

            // 一对一 model
            $_model_edit = $model[0]::find($_model_id)->toArray();

            // 调用执行方法的model
            $_model = new $model[0];

            foreach ($_model->get_field() as $value){

                if(array_key_exists($value, $_model->get_model())){
                    // 是否需要校验
                    $validate = in_array($value, $_model->get_front_validate());

                    // 是否是 只显示
                    $disabled = in_array($value, $_model->get_edit_disabled());

                    $type = $_model->get_model()[$value][1];

                    $name = $_model->get_model()[$value][0];

                    $join_key = array($filed, $value);
                    $body_arr = array(join('_',$join_key), $name, $validate, $disabled);

                    if($type == 'select'){
                        $relation = array();

                        foreach ($_model->get_relations() as $o_key => $o_model_arrays){
                            foreach ($o_model_arrays as $o_model_field => $o_model_array){
                                // 数组的方式获取数据
                                $method = '_rget_'.$o_model_array;

                                foreach ($_model->$method() as $t_key => $t_value){
                                    $tmp['name'] = $t_value;
                                    $tmp['value'] = $t_key;

                                    $tmp['selected'] = $_model_edit[$value] === $t_key ? true : false;
                                    array_push($relation, $tmp);
                                }
                            }
                        }
                    }

                    $relation = isset($relation) ? $relation : $_model_edit[$value];

                    $form->add_block($type, $filed_name, $body_arr, $relation);

                    unset($relation);
                }
            }
        }
    }
    



    // + ---------------------------------------------
    // | 辅助函数 - 返回编辑方法 多对多数据
    // + ---------------------------------------------
    public function get_edit_manytomany_data($id, $params){
        $v = $this->get_edit_manytomany_relation($params);
        return $this->find($id)->belongsToMany($v[0], $v[1], $v[2], $v[3])->getResults()->toArray();
    }




    // + ---------------------------------------------
    // | 辅助方法 - 返回编辑方法 数据
    // + ---------------------------------------------
    public function get_edit_data($id){
        return $this->find(intval($id))->toArray();
    }



    // + ---------------------------------------------
    // | 辅助方法 -  返回 多对多关系数组
    // + ---------------------------------------------
    public function get_edit_manytomany_relation($params){
        return $this->$params[1];
    }

    // -----------------------------------------------


    // + ---------------------------------------------
    // | 数据 - 组装成json
    // + ---------------------------------------------
    public function json_data($type){
//        $f = new Form();
//
//        $ret = $f->open('edit')
//                 ->url(admin_url('t'))
//                 ->navs(
//                    [
//                        ['首页', 'url', true],
//                        ['第二页', 'url2', false]
//                    ]
//                 )
//                ->add('str', 'name', '姓名', 111)
//                ->add(
//                    'select', 'sel', "班级",
//                    [
//                        ['一班','1'],
//                        ['二班','2', true]
//                    ],
//                    true,
//                    true
//                )
//                ->add(
//                    'checkbox', 'ckb', "兴趣",
//                    [
//                        ['足球', '1', true],
//                        ['篮球', '2', true],
//                        ['乒乓球', '3', true]
//                    ]
//                    )
//                ->add_block('block_select', 'xxx2', ['hby', '兴趣'],
//                    [
//                        ['足球', '1'],
//                        ['篮球', '2', true],
//                        ['乒乓球', '3']
//                    ]
//                )
//                ->add_block('block_checkbox', 'xxx2', ['hby', '爱好'],
//                    [
//                        ['足球', '1'],
//                        ['篮球', '2', ],
//                        ['乒乓球', '3']
//                    ]
//                )
//                ->many2one_name('多对一')
//                ->add_many2one('str','模块1', ['m_name', '姓名'],'xxx')
//                ->add_many2one('select', '模块1', ['m_hby', '爱好'],
//                [
//                    ['足球', '1'],
//                    ['篮球', '2', true],
//                    ['乒乓球', '3']
//                ])
//                ->add_many2one('checkbox', '模块1', ['m_mhby', '爱好'],
//                [
//                    ['足球', '1'],
//                    ['篮球', '2', ],
//                    ['乒乓球', '3']
//                ])
//                ->add_many2one('str','模块2', ['m_name', '姓名'],'xxx')
//                ->add_many2one('select', '模块2', ['m_hby', '爱好'],
//                    [
//                        ['足球', '1'],
//                        ['篮球', '2', true],
//                        ['乒乓球', '3']
//                    ])
//                ->add_many2one('checkbox', '模块2', ['m_mhby', '爱好'],
//                    [
//                        ['足球', '1'],
//                        ['篮球', '2', ],
//                        ['乒乓球', '3']
//                    ])
//                ->submit()
//                ->close();
//
//        return json_encode($ret);




//        $form = new ListForm();
//
//        $datas = [
//            [
//                'id' => 3,
//                'name' => 'null',
//                'sel' => 7,
//                'checkbox'=>[1]
//            ],
//            [
//                'id' => 7,
//                'name' => 'edison',
//                'sel' => 7,
//                'checkbox'=>[1]
//            ]
//        ];
//
//        $operation = [
//            ['编辑', 'url', ],
//            ['删除', 'url', ['modal', '删除', '确定删除吗?']]
//        ];
//
//        $ret = $form->open()
//                    ->list_action()
//                    ->navs(
//                        [
//                            ['首页', 'url', true],
//                            ['第二页', 'url2', false]
//                        ]
//                    )
//                    ->filter('name', 'show_name', 'str', '')
//                    ->filter('sel', 'select', 'select', '')
//                    ->filter_url()
//                    ->thead(['名字', 'select', 'checkbox', '操作'])
//                    ->format(['hidden', 'str', 'select', 'checkbox', 'operation'], $datas, $operation)
////                    ->line(
////                        [
////                            'id'=>$form->__row('hidden', 1),
////                            'name' =>$form->__row('str', 'name'),
////                            'sel'=>$form->__row('select', '7'),
////                            'checkbox'=>$form->__row('checkbox', [1]),
////                            'operation'=> [
////                                $form->__action(['编辑', 'xxx', '']),
////                                $form->__action(['删除', 'xxx', ''])
////                            ]
////                         ]
////                     )
////                    ->line(
////                        [
////                            'id'=>$form->__row('hidden', 1),
////                            'name' =>$form->__row('str', 'name'),
////                            'sel'=>$form->__row('select', '7'),
////                            'checkbox'=>$form->__row('checkbox', [1]),
////                            'operation'=> [
////                                $form->__action(['编辑', 'xxx', '']),
////                                $form->__action(['删除', 'xxx', ''])
////                            ]
////                        ]
////                    )
//                    ->relation('sel',['3'=>'一把', '5'=>'二八', '7'=>'sel - 7'])
//                    ->relation( 'checkbox',['1'=>'checkbox - 1', '5'=>'55', '7'=>'77'])
//                    ->close();
//
//        return json_encode($ret);



        return json_encode($this->$type());
    }


    // + ---------------------------------------------
    // | 辅助函数 - [表单] - 返回关系解析数据(外键)
    // + ---------------------------------------------
    protected function __relation($filed, $name, $body_type, $form, $validate = false, $disabled = false, $selected_id = false){
        if(!isset($form)){
            throw new Exception('__relation : param[form] not set!');
        }

        // 从外键找
        if(array_key_exists($filed, $this->foreign)){
            $model = $this->foreign[$filed];

            // 解析数组
            $relation = array();

            // 解析model格式
            if(method_exists($model[0], 'all')){
                $model_data = $model[0]::all()->toArray();

                foreach ($model_data as $params){
                    array_push($relation, array($params[$model[1]], $params['id'], $params['id'] === $selected_id ? true : false));
                }

                // 解析自定义数组模式
            }else{
                foreach ($this->$model as $key => $value){
                    array_push($relation, array($value, $key, $key === $selected_id ? true : false));
                }
            }

            $filed = ($body_type === 'block_checkbox') ? $filed . '[]' : $filed;
            $flag = strpos($body_type, 'block_');

            if ($flag !== false ) {
                $form->add_block($body_type, $name, array($filed, $name, $validate, $disabled), $relation);
            } else {
                $form->add($body_type, $filed, $name, $relation, $validate);
            }

        }
    }


    // 给一对一关系的model 使用
    public function get_list() {
        return $this->list;
    }


    // 给一对一关系的model 使用
    public function get_model() {
        return $this->model;
    }


    public function get_relations() {
        if(empty($this->relations)) {
            $this->relations = [];
            foreach ($this->foreign as $key => $value) {
                if(is_array($value)) {
                    $_key = $value[0];
                    $_value = [$key, $value[1]];
                } else {
                    $_key = $key;
                    $_value = $value;
                }
                $this->relations[] = [$_key => $_value];
            }
        }
        return $this->relations;
    }


    // 一对一 被使用
    public function get_field() {
        return $this->field;
    }

    // 一对一 被使用
    public function get_front_validate() {
        return $this->front_validate;
    }


    public function get_manytomany() {
        $ret = [];
        foreach($this->manytomany as $field => $info) {
            $attr = $info[1];
            $ret[$field] = $this->$attr;
        }
        return $ret;
    }


    public function get_onetoone() {
        return $this->onetoone;
    }


    public function get_manytoone(){
        return $this->manytoone;
    }

    public function get_foreign(){
        return $this->foreign;
    }


    public function get_edit_disabled(){
        return $this->edit_disabled;
    }


    public function get_duplication() {
        return $this->duplication;
    }


    public function __call($method, $params) {
        if(strpos($method, '_rget_') === 0 && empty($params)) {
            $var = substr($method, 6);
            return $this->$var;
        }

        return parent::__call($method, $params);
    }


    public function set_filter_list($filter) {
        $this->filter_list = $filter;
    }

}