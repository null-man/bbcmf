<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;

use bb\Command;

class Config extends AdminModel {

    protected $table = 'config';

    // protected $validate = [
    //     'rule' => [
    //         'name' => 'email'
    //     ],
    //     'msg'  => 'aaa'
    // ];

    protected $field = ['name', 'config', 'description'];
    protected $front_validate = ['name', 'description'];

    // 需要显示 却不可操作的字段
    protected $edit_disabled = [];


    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;

    // 数据库结构
    protected $model = array(
        'id' => array('ID', 'hidden'),
        'name' => array('配置名称', 'str'),
        'config' => array('配置数据', 'str'),
        'description' => array('描述', 'str'),
    );


    // ------------------------- table --------------------------

    // 列表显示
    protected $list = ['id', 'description', 'config'];
        
    protected $actions = [
        'oper',
        ['编辑', 'config_edit']
    ];

//    protected $list_actions = [
//        ['', '', '']
//    ];


    public function oper($id) {

        $x = $this->find($id);

        if($x->name == 'switch') {
            // $z = $x->config;
            if($x->getData('config') == '0') {
                return ['开启', 'config_thread_on', ['modal', '确定开启吗?', '确定开启吗?']];
            }

            if($x->getData('config')== '1') {
                return ['关闭', 'config_thread_off',['modal', '确定关闭吗?', '确定关闭吗?']];
            }
        }


        // if($x->name == 'cron_url') {

        //     list($ret, $output) = Command::exec('crontab -l');
        //     foreach $output 


        // }

        return ['', '', ''];
    }



}