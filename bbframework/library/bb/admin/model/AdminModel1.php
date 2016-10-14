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

use bb\admin\model\base\MBase;

class AdminModel extends MBase
{
    // Tmpl 单例
//    private static $instance = null;

    // 数据库结构
    protected $_model = array();

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
        ['删除', 'del',  '']
    ];

    // 批量操作
    protected $list_actions = [];

    // 列表
    protected $_list = array();

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

//        TmplUtils已经全部丢到Mbase做处理了

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
        if(empty($this->update_url)) {
            $url = admin_url('edit');
        } else {
            $url = admin_url($this->update_url, ['render' => I('render', '')]);
        }
        self::$instance->submit_url($url);
        return $this;
    }




    // + ---------------------------------------------
    // | 初始化设置 - 添加url
    // + ---------------------------------------------
    protected function insert_url(){
        if(empty($this->insert_url)) {
            $url = admin_url('add');
        } else {
            $url = admin_url($this->insert_url, ['render' => I('render', '')]);
        }
        self::$instance->submit_url($url);
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
        self::$instance->table_body_thead($thead_arr);

        return $this;
    }




    // + ---------------------------------------------
    // | table - 设置列操作
    // + ---------------------------------------------
    protected function list_action(){
        foreach ($this->list_actions as $list_action){

//            if(isset($list_action[2]) && !empty($list_action[2])) {
//                $list_action[2] = admin_url($list_action[2]);
//            } else {
//                if($list_action[1] == 'alldel') {
//                    $list_action[2] = admin_url('del');
//                } else {
//                    $list_action[2] = admin_url($list_action[1]);
//                }
//            }

            $list_action[1] = admin_url($list_action[1]);

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
    protected function relation() {
        // 主表解析

        $this->relations = $this->get_relations();

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
                        $method = '_rget_'.$o_model_array;

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

                    $cls = $this->__type_map($type);

                    $cls::filter($value, $filed_name, $type, $filter_value);
                }

                // 多对多处理
                if(array_key_exists($value, $this->manytomany)){
                    $manytomany_arr = $this->manytomany[$value];
                    \bb\admin\model\base\MCheckbox::filter($value, $manytomany_arr[0], $filter_value);
                }
            }

//            dump(self::$instance);

            if(empty($this->filter_submit)) {
                $this->filter_submit = 'index';
            }

            self::$instance->table_body_filter_url(admin_url($this->filter_submit));
        }

        return $this;
    }




    // + ---------------------------------------------
    // | table manytomany 解析对应的数组
    // + ---------------------------------------------
    protected function list_manytomany($key, $id){
        $params = $this->manytomany[$key];

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

            if(is_string($action)) {
                $action = call_user_func_array([$this, $action], [$id]);
            }

            $action_arr['type'] = 'link';
            $action_arr['showName'] = $action[0];
            $action_arr['opType'] = '';

//            if(isset($action[2]) && !empty($action[2])) {
//                $action_arr['url'] = admin_url($action[2], ['id' => $id]);
//            } else {
//                $action_arr['url'] = admin_url($action[1], ['id' => $id]);
//            }

            $action_arr['url'] = admin_url($action[1], ['id' => $id]);

            $action_arr['modal']['type'] = isset($action[2][0]) ? $action[2][0] : 'redirect';
            $action_arr['modal']['title'] = isset($action[2][1]) ? $action[2][1] : '';
            $action_arr['modal']['desc'] = isset($action[2][2]) ? $action[2][2] : '';


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

        $select = array();
        foreach ($this->_list as $field){
            if(array_key_exists($field, $this->_model)){
                array_push($select, $field);
            }
        }

        $model_pointer = $this->select($select);

        // 过滤
        if (!empty($this->filter)) {
            foreach ($this->filter as $field => $flr){

                // 主表过滤 处理
                if(isset($this->_model[$field])){
                    $type = $this->_model[$field];

                    $cls = $this->__type_map($type);
                    $cls ? $cls::sql_where($model_pointer, $field, $flr) : $model_pointer = $model_pointer->where($field, 'like', '%' . $flr . '%');
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

                    // 多个条件
                    if(is_string($flr)){
                        $flr = explode(',', $flr);
                    }

//                    dump($flr);

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
//                            !in_array($value, $manytomany_fliter_arr)
//                            dump($value);
//                            dump($manytomany_fliter_arr);
//                            dump($this->in_array_2d($value, $manytomany_fliter_arr));

                            if ($flag) {
                                array_push($manytomany_fliter_arr, $value);
                            }
                        }
                    }
//                    dump($manytomany_fliter_arr);

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

//                        dump($id_arr);
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

        // 组装数据
        $show_datas = array();
        foreach ($final_pointer->toArray() as $data){
            $tmp_arr = array();
            foreach ($data as $key => $value){
                // 配置 list里面的字段
                if (in_array($key, $this->_list)){
                    $value = strval($value);

                    $type = $this->_model[$key][1];

                    $cls = $this->__type_map($type);
                    $tmp_arr[$key]=$cls::set_value($value);

                }
            }

            foreach ($this->manytomany as $key => $value){
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
                    if(in_array($_key, $model->get_list())){

                        $type = $model->get_model()[$_key][1];
                        $_value = strval($_value);
                        $cls = $this->__type_map($type);

                        // 外键的数据 防止和其他的数据冲突 所以重新处理key 将一对一的字段 下划线 拼接字段名
                        $join_key = array($key, $_key);
                        $tmp_arr[join('_',$join_key)]=$cls::set_value($_value);

                    }
                }
            }

            $tmp_arr['operation'] = $this->actions($data['id']);

            array_push($show_datas, $tmp_arr);
        }

        return $show_datas;
    }



    // + ---------------------------------------------
    // | 辅助函数 - 二维数组判断 该数组是否在此二维数组中
    // + ---------------------------------------------
    protected function in_array_2d($exist_array, $array_2d = array()){
        foreach($array_2d as $array){
            if($array['id'] == $exist_array['id']){
                return true;
            }
        }

        return false;
    }
    // -----------------------------------------------




    // ------------------- add  ----------------------

    // + ---------------------------------------------
    // | 添加 - 表单字段
    // + ---------------------------------------------
    protected function from_add(){

        foreach ($this->field as $key => $filed){

            // 是否需要校验
            $validate = in_array($filed, $this->front_validate);

            // 把需要添加的字段 拿出来 (从数据库结构)
            if(array_key_exists($filed, $this->_model)){

                $body = $this->_model[$filed];

                $body_arr = array($filed, $body[0], $validate, false);

                $type = $body[1];

                $cls = $this->__type_map($type);

                if ($type == 'select' || $type == 'radio' || $type == 'checkbox'){
                    $this->__relation($filed, $body[0], 'body_' . $type, $validate);
                }else if($type == 'onetoone'){
                    $this->_from_onetoone_add();
                }else{
                    $cls::form($body_arr);
                }
            }

            // 额外字段
            if(array_key_exists($filed, $this->manytomany)){
                $body = $this->manytomany[$filed];

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
                    $cls = $this->__type_map($type);
                    $cls::block($filed_name, $body_arr, $relation);
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
        $body_arr = array('id', $body[0], false);

        self::$instance->body_hidden($body_arr, $this->_id);

        // 设置编辑显示的字段和值
        foreach ($this->field as $key => $filed){
            // 是否需要校验
            $validate = in_array($filed, $this->front_validate);

            // 是否需要 仅显示
            $disabled = in_array($filed, $this->edit_disabled);

            // 额外字段(多对多)
            if(array_key_exists($filed, $this->manytomany)){
                $params = $this->manytomany[$filed];

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

                    $tmp_arr['name'] = $param[$this->foreign[$filed][1]];
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

                $type = $body[1];
                $cls = $this->__type_map($type);

                if ($type == 'select' || $type == 'radio' || $type == 'checkbox'){
                    $this->__relation($filed, $body[0], 'body_select', $validate, $disabled, $value);
                }else if($type == 'onetoone'){
                    $this->_from_onetoone_edit();
                }else{
                    $cls::form($body_arr, $value);
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
                    $cls = $this->__type_map($type);

                    $cls::block($filed_name, $body_arr, $relation);
                    unset($relation);
                }
            }
        }
    }

    // -----------------------------------------------




    




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





    // + ---------------------------------------------
    // | 辅助函数 - [表单] - 返回关系解析数据(外键)
    // + ---------------------------------------------
    protected function __relation($filed, $name, $body_type, $validate = false, $disabled = false, $selected_id = false){

        // 从外键找
        if(array_key_exists($filed, $this->foreign)){
            $model = $this->foreign[$filed];

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


    // 给一对一关系的model 使用
    public function get_list() {
        return $this->_list;
    }

    // 给一对一关系的model 使用
    public function get_model() {
        return $this->_model;
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