<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;

class Task extends AdminModel {

    protected $table = 'task';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;

    protected $field = ['url', 'rule', 'name', 'is_on'];
    protected $front_validate = ['url', 'rule', 'name'];
    // 需要显示 却不可操作的字段
    protected $edit_disabled = [];

    protected $auto = ['is_on' => 0];

    protected $filter_submit = 'task';

    // 数据库结构
    protected $model = array(
        'id' => array('任务ID', 'hidden'),
        'url' => array('URL', 'str'),
        'rule' => array('规则', 'crontab'),
        'type' => array('类型', 'str'),
        'name' => array('名称', 'str'),
        'description' => array('描述', 'str'),
        'is_on' => array('是否开启', 'select')
    );

    // ------------------------- table --------------------------

    // 列表显示
    protected $list = ['id', 'name', 'url', 'rule', 'is_on'];


    // ----------------------- 公共 --------------------------------

    // // 外键(表单添加、编辑)
    protected $foreign = array(
        'is_on' => 'is_on_arr'
    );

    // 外键对应数组(all)
    protected $is_on_arr = array(
        '1'=>'开启',
        '0'=>'关闭'
    );


    protected $actions = [
        'onoff',
        ['编辑', 'task_edit'],
        ['删除',  '', ['modal', '确定要删除吗?', '确定要删除吗?']],
        ['日志', 'tasklog']
    ];

    // 批量操作
    protected $list_actions = [
        ['添加','add']
    ];

    // 开关
    public function onoff($id) {
        $x = $this->find($id);
        if($x->is_on == 1) {
            return ['关闭', 'task_off', ['modal', '确定要关闭吗?', '确定要关闭吗?']];
        }
        return ['开启', 'task_on', ['modal', '确定要开启吗?', '确定要删除吗?']];
    }

}