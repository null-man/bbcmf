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

class ListForm extends BaseForm
{

    // 实例
    protected static $instance = null;
    // 表数据
    protected $data = [];

    // 列表类型  解析
    public $list_map = array(
        'str' => 'string',
        'hidden' => 'hidden',
        'select' => 'select',
        'crontab' => 'string',
        'date' => 'date',
        'email' => 'email',
        'img' => 'img',
        'link' => 'link',
        'radio' => 'radio',
        'textarea' => 'textarea',
        'checkbox' => 'checkbox'
    );

    // + ---------------------------------------------
    // | 构造函数 - 初始化 tmpl 类
    // + ---------------------------------------------
    function __construct(){
        if(is_null(self::$instance)){
            self::$instance = new TmplUtils();
            self::$instance = self::$instance->_init();
            self::$instance->table();
        }
    }



    // + ---------------------------------------------
    // | 辅助函数 - 列表 映射 类
    // + ---------------------------------------------
    public function __list_type($key){
        $type = false;

        if (is_string($key)){
            $type = isset($this->list_map[$key]) ? $this->list_map[$key] : false;
        }

        return $type;
    }



    // + ---------------------------------------------
    // | 表单 - 开启
    // + ---------------------------------------------
    public function open(){
        self::$instance->submit_url('');

        return $this;
    }



    // + ---------------------------------------------
    // | 表单 - 关闭
    // + ---------------------------------------------
    public function close(){
        self::$instance->table_body_tbody($this->data);
        return self::$instance->done();
    }




    public function __row($type, $value = ''){
        return $this->__value($this->__list_type($type), $value);
    }




    // + ---------------------------------------------
    // | 列表 - 行
    // + ---------------------------------------------
    public function line($rows){
//        ['id'=>['hidden',''], ['name' =>['xxx', '']
        $row_data = array();
        foreach ($rows as $key => $row){
            // 不处理操作operation 的数据
            if ($key == 'operation'){
                $row_data[$key] = $row;
            }else{
                $row_data[$key] = ['type' => $row[0], 'value' => isset($row[1]) ? $row[1] : ''];
            }
        }

//        dump($row_data);
        array_push($this->data, $row_data);

        return $this;
    }



    // + ---------------------------------------------
    // | 列表 - 格式 数据 装填数据
    // + ---------------------------------------------
    public function format($format = array(), $datas = array(), $operations = array()){
        foreach ($datas as $data){
            $line_data = array();
            $i = 0;
            foreach ($data as $k => $value){
                $line_data[$k] = $this->__row($format[$i], $value);
                $i++;
            }

            if(in_array('operation', $format)){
                $operation_data = array();
                foreach ($operations as $operation){
                    $operation[1] = admin_url($operation[1], ['id' => $data['id']]);
                    array_push($operation_data, $this->__action($operation));
                }

                $line_data['operation'] = $operation_data;
            }
            $this->line($line_data);
        }

        return $this;
    }




    // + ---------------------------------------------
    // | 列表 - list_action
    // + ---------------------------------------------
    public function list_action($params = array()){
        foreach ($params as $list_action){
            $list_action[1] = admin_url($list_action[1]);

            self::$instance->table_operation($list_action);
        }

        return $this;
    }




    // + ---------------------------------------------
    // | 列表 - 返回action数据
    // + ---------------------------------------------
    public function __action($params = array()){
//        $value = isset($params[2][3]) ? $params[2][3] : [];
        $value = [];

        if(isset($params[2][3]) && isset($params[2][4])){
            $value['id'] = isset($params[2][3]) ? $params[2][3] : time();
            $value['name'] = isset($params[2][4]) ? $params[2][4] : time();
        }

        return self::$instance->table_action($params, $value);
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
    // | 列表 - filter_url
    // + ---------------------------------------------
    public function filter_url($url = ''){
        self::$instance->table_body_filter_url($url);

        return $this;
    }





    // + ---------------------------------------------
    // | 列表 - 表头
    // + ---------------------------------------------
    public function thead($params = array()){
        self::$instance->table_body_thead($params);

        return $this;
    }
    



    // + ---------------------------------------------
    // | 列表 - 过滤
    // + ---------------------------------------------
    public function filter($name, $show_name, $type, $value =''){
        self::$instance->table_body_filter($name, $show_name, $type, $value);
        return $this;
    }





    // + ---------------------------------------------
    // | 列表 - 设置值
    // + ---------------------------------------------
    public function __value($type = 'string', $value = ''){
//        $values['type'] = $type;
//
//        if($type == 'checkbox'){
//            $tmp = array();
//            array_push($tmp, $value);
//            $values['value'] = $tmp;
//        }else{
//            $values['value'] = $value;
//        }

        $values = array();
        array_push($values, $type);

        if($type == 'checkbox'){
            $tmp = array();
            array_push($tmp, $value);
            array_push($values, $tmp);
        }else{
            array_push($values, $value);
        }

        return $values;
    }




    // + ---------------------------------------------
    // | 列表 - 关系
    // + ---------------------------------------------
    public function relation($name, $value){
        self::$instance->table_body_relation($name, '', $value);

        return $this;
    }




    // + ---------------------------------------------
    // | 列表 - 分页
    // + ---------------------------------------------
    public function page($params = array()){
        $total_page = $params[0];
        $page_now = $params[1];
        $page_url = $params[2];

        self::$instance->table_body_page($total_page, $page_now, $page_url);
        return $this;
    }




//    // + ---------------------------------------------
//    // | 列表 - 设置数据
//    // + ---------------------------------------------
//    public function line_done(){
//        self::$instance->table_body_tbody($this->data);
//        return $this;
//    }




}