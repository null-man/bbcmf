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

namespace bb\form;
use util\TmplUtils;

class Form extends BaseForm
{

    // 实例
    protected static $instance = null;

    // 类型解析
    public $type_map = array(
        'str' => 'text',
        'crontab' => 'crontab',
        'select' => 'select',
        'file' => 'file',
        'date' => 'date',
        'email' => 'email',
        'textarea' => 'textarea',
        'checkbox' => 'checkbox',
        'hidden'   => 'hidden'
    );

    // // 内容块 解析
    // public $block_map = array(
    //     'str' => 'block_text',
    //     'block_checkbox' => 'block_checkbox',
    //     'block_select' => 'block_select',
    //     'crontab'      => 'block_crontab',
    //     'select' => 'block_select',
    //     'checkbox' => 'block_select'
    // );

    // 多对一 解析
    public $many2one_map = array(
        'hidden' => 'many2one_hidden',
        'str' => 'many2one_text',
        'select' => 'many2one_select',
        'checkbox' => 'many2one_checkbox',
    );


    // + ---------------------------------------------
    // | 构造函数 - 初始化 tmpl 类
    // + ---------------------------------------------
    function __construct(){
        if(is_null(self::$instance)){
            self::$instance = new TmplUtils();
            self::$instance = self::$instance->_init();
        }
    }


    // + ---------------------------------------------
    // | 辅助函数 - 类型 映射 类
    // + ---------------------------------------------
    public function __type($key){
        $type = false;

        if (is_string($key)){
            $type = isset($this->type_map[$key]) ? $this->type_map[$key] : false;
        }

        return $type;
    }


    // + ---------------------------------------------
    // | 辅助函数 - 内容块 映射 类
    // + ---------------------------------------------
    public function __block_type($key){
        $type = false;

        if (is_string($key)){
            $type = isset($this->block_map[$key]) ? $this->block_map[$key] : false;
        }

        return $type;
    }


    // + ---------------------------------------------
    // | 辅助函数 - 多对一 映射 类
    // + ---------------------------------------------
    public function __many2one_type($key){
        $type = false;

        if (is_string($key)){
            $type = isset($this->many2one_map[$key]) ? $this->many2one_map[$key] : false;
        }

        return $type;
    }



    // + ---------------------------------------------
    // | 表单 - 开启
    // + ---------------------------------------------
    public function open($type){
        if (empty($type)) {
            $this->__empty_exception('open', 'type');
        }

        self::$instance->$type();

        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - 关闭
    // + ---------------------------------------------
    public function close(){
        return self::$instance->done();
    }


    // + ---------------------------------------------
    // | 表单 - submit url
    // + ---------------------------------------------
    public function url($url = ''){
        self::$instance->submit_url($url);
        return $this;
    }




    // + ---------------------------------------------
    // | 列表 - 标签栏
    // + ---------------------------------------------
    public function navs($params = array()){
        foreach ($params as $nav){
            self::$instance->head_content($nav);
        }

        return $this;
    }





    // + ---------------------------------------------
    // | 表单 - add/edit
    // + ---------------------------------------------
    public function add($type, $field, $name, $value = '', $validate = false, $disabled = false){
        $method = $this->__type($type);

        if(is_array($value)){
            $value = $this->__relation_data($type, $value);
        }

        $this->$method(array($field, $name, $validate, $disabled), $value);

        return $this;
    }




    // + ---------------------------------------------
    // | 表单 - 内容块 add/edit
    // + ---------------------------------------------
    public function add_block($type, $filed, $body, $value = ''){
        // $method = $this->__block_type($type);

        if($type == 'str') $type = 'text';

        $method = 'body_block_' . $type;

        if(method_exists(self::$instance, $method)) {
            if(is_array($value)){
                $value = $this->__relation_data($type, $value);
            }
            self::$instance->$method($filed, $body, $value);
        }

        return $this;
    }


    // + ---------------------------------------------
    // | 表单 - 多对一
    // + ---------------------------------------------
//    public function many2one_name($name = ''){
//        self::$instance->many2one_config(['name'=>$name]);
//
//        return $this;
//    }




    // + ---------------------------------------------
    // | 表单 - 多对一
    // + ---------------------------------------------
    public function add_many2one($type, $block_name, $filed, $body, $value = ''){
        $method = $this->__many2one_type($type);

        if(is_array($value)){
            $value = $this->__relation_data($type, $value);
        }

        $this->$method($block_name, $filed, $body, $value);

        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - 多对一 text
    // + ---------------------------------------------
    public function many2one_hidden($block_name, $name, $body, $value = ''){
        self::$instance->many2one_hidden($block_name, $name, $body, $value);
        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - 多对一 text
    // + ---------------------------------------------
    public function many2one_text($block_name, $name, $body, $value = ''){
        self::$instance->many2one_text($block_name, $name, $body, $value);
        return $this;
    }




    // + ---------------------------------------------
    // | 表单 - 多对一 select
    // + ---------------------------------------------
    public function many2one_select($block_name, $name, $body, $value = ''){
        self::$instance->many2one_select($block_name, $name, $body, $value);
        return $this;
    }




    // + ---------------------------------------------
    // | 表单 - 多对一 checkbox
    // + ---------------------------------------------
    public function many2one_checkbox($block_name, $name, $body, $value = ''){
        self::$instance->many2one_checkbox($block_name, $name, $body, $value);
        return $this;
    }








    // + ---------------------------------------------
    // | 表单 - 关系解析 [返回组装数据]
    // + ---------------------------------------------
    public function __relation_data($type, $value) {
        $_value = array();
        switch ($type){
            case 'select':
                $_type = 'selected';
                break;
            case 'block_checkbox' || 'checkbox':
                $_type = 'checked';
                break;
            default:
        }


        foreach ($value as $v) {

            $tmp_arr['name'] = isset($v['name']) ? $v['name'] : $v[0];
            $tmp_arr['value'] = isset($v['value']) ? $v['value'] : $v[1];
            if(isset($v[$_type])) {
                $tmp_arr[$_type] = $v[$_type];
            } elseif(isset($v[2])) {
                $tmp_arr[$_type] = $v[2];
            } else {
                $tmp_arr[$_type] = false;
            }

            array_push($_value, $tmp_arr);
        }

        return $_value;
    }



//    // + ---------------------------------------------
//    // | 表单 - 关系解析 relation
//    // + ---------------------------------------------
//    public function __relation($type, $name, $value = '', $selected = false){
//        switch ($type){
//            case 'select':
//                $_type = 'selected';
//                break;
//            case 'block_checkbox' || 'checkbox':
//                $_type = 'checked';
//                break;
//            default:
//        }
//
//
//        $tmp_arr['name'] = $name;
//        $tmp_arr['value'] = $value;
//        $tmp_arr[$_type] = $selected;
//
//        return $tmp_arr;
//    }





//
//
//
//
//    // + ---------------------------------------------
//    // | 表单 - 关系解析 select
//    // + ---------------------------------------------
//    public function relation_select($name, $value = '', $selected = false){
//        return $this->__relation('select', $name, $value, $selected);
//    }
//
//
//
//    // + ---------------------------------------------
//    // | 表单 - 关系解析 checkbox
//    // + ---------------------------------------------
//    public function relation_checkbox($name, $value = '', $selected = false){
//        return $this->__relation('checkbox', $name, $value, $selected);
//    }






    // + ---------------------------------------------
    // | 表单 - text
    // + ---------------------------------------------
    public function text($params = array(), $value = ''){
        self::$instance->body_text($params, $value);
        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - email
    // + ---------------------------------------------
    public function email($params = array(), $value = ''){
        self::$instance->body_email($params, $value);
        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - file
    // + ---------------------------------------------
    public function file($params = array(), $value){
        self::$instance->body_file($params, '');
        return $this;
    }


    // + ---------------------------------------------
    // | 表单 - checkbox
    // + ---------------------------------------------
    public function checkbox($params = array(), $value = ''){
        self::$instance->body_checkbox($params, $value);
        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - radio
    // + ---------------------------------------------
    public function radio($params = array(), $value = ''){
        self::$instance->body_radio($params, $value);
        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - select
    // + ---------------------------------------------
    public function select($params = array(), $value = ''){
        self::$instance->body_select($params, $value);
        return $this;
    }




    // + ---------------------------------------------
    // | 表单 - hidden
    // + ---------------------------------------------
    public function hidden($params = array(), $value = ''){
        self::$instance->body_hidden($params, $value);
        return $this;
    }




    // + ---------------------------------------------
    // | 表单 - date
    // + ---------------------------------------------
    public function date($params = array(), $value = ''){
        self::$instance->body_date($params, $value);
        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - crontab
    // + ---------------------------------------------
    public function crontab($params = array(), $value = ''){
        self::$instance->body_crontab($params, $value);
        return $this;
    }




    // + ---------------------------------------------
    // | 表单 - submit
    // + ---------------------------------------------
    public function submit($params = array('submit','提交')){
        self::$instance->body_submit($params);
        return $this;
    }


    public function block_crontab($name, $body, $value = '') {
        self::$instance->body_block_crontab($name, $body, '');
        return $this;
    }


    // + ---------------------------------------------
    // | 内容块 - text
    // + ---------------------------------------------
    public function block_text($name, $body, $value = ''){
        self::$instance->body_block_text($name, $body, $value);
        return $this;
    }


    // + ---------------------------------------------
    // | 内容块 - checkbox
    // + ---------------------------------------------
    public function block_checkbox($name, $body, $value = ''){
        self::$instance->body_block_checkbox($name, $body, $value);
        return $this;
    }


    // + ---------------------------------------------
    // | 内容块 - select
    // + ---------------------------------------------
    public function block_select($name, $body, $value = ''){
        self::$instance->body_block_select($name, $body, $value);
        return $this;
    }
    
    
    // ===================

    public function addBlock() {
        
    }

    public function addMulti($mType, $add, $data = []) {
        
        // $type, $field, $name, $value = '', $validate = false, $disabled = false

        // ->add_many2one('str', 'hehe', 0, ['m_name', '姓名'], 'xxx')
        // ->add_many2one('select', 'hehe', 0, ['m_hby', '爱好'],
        //  [
        //      ['足球', '1'],
        //      ['篮球', '2', true],
        //      ['乒乓球', '3']
        //  ])

        if(empty($data)) {
            foreach($add as $x) {
                $type   = $x[0];
                $field  = $x[1];
                $name   = $x[2];
                $value  = $x[3];
                $this->add_many2one($type, $mType, 'add', [$field, $name], $value);
            }
        } else {


            foreach ($data as $j => $d) {
                foreach($add as $i => $x) {
                    $type   = $x[0];
                    $field  = $x[1];
                    $name   = $x[2];
                    $value  = $x[3];

                    if(is_array($value)) {
                        $_ = [];
                        foreach($value as $v) {
                            if(strval($d[$i]) == strval($v[1])) {
                                $_[] = [$v[0], $v[1], true];
                            } else {
                                $_[] = [$v[0], $v[1]];
                            }
                        }
                        $value = $_;
                    } else {
                        $value = $d[$i];
                    }

                    $this->add_many2one($type, $mType, $j, [$field, $name], $value);
                }
            }

        }

        return $this;

    }
    
}