<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;

class TaskLog extends AdminModel {

    protected $table = 'task_log';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;

    protected $field = ['task_id', 'time', 'keyword', 'state'];
    // protected $front_validate = ['url', 'rule', 'name'];

    protected $filter_submit = 'tasklog';

    // 数据库结构
    protected $model = array(
        'id' => array('ID', 'hidden'),
        'task_id' => array('任务', 'select'),
        'time' => array('时间', 'date'),
        'keyword' => array('keyword', 'str'),
        'state' => array('状态', 'str')
    );

    // ------------------------- table --------------------------

    // 列表显示
    protected $list = ['id', 'task_id', 'time', 'keyword', 'state'];

    protected $actions = [
        ['编辑', 'tasklog_edit'],
        ['删除', 'tasklog_del', ['modal', '确定要删除吗?', '']],
    ];


    // 外键
    protected $foreign = [
        'task_id' => array('bb\\admin\\model\\Task', 'name')
    ];


}