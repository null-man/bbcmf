<?php

namespace app\dmp_admin\model;

class TaskConfig extends BaseModel {

    protected $table = 'task_config';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;

    // 数据库结构
    protected $_model = array(
        'id' => array('ID', 'hidden'),
        'name' => array('配置名称', 'str'),
        'config' => array('配置数据', 'str'),
        'description' => array('描述', 'str'),
    );

    // ------------------------- table --------------------------

    // 列表显示
    protected $_list = array(
        'id',
        'name',
        'config',
        'description'
    );

    // 批量操作
    protected $list_actions = array(
        array('批量删除', 'alldel', 'xx')
    );

    // 单行操作
    protected $actions = array(
        array('编辑', 'edit', '/dmp_admin/index/task_config_edit/id'),
        array('删除', 'del', '/dmp_admin/index/task_config_del/id')
    );


    // ------------------------ add --------------------------------

    // 插入url
    protected $insert_url = '/dmp_admin/index/task_config_add';

    // 添加页面
    protected $_add = array(
        'name',
        'config',
        'description'
    );

    // 需要验证的字段
    protected $add_validate = array(
        'name',
//        'config',
        'description'
    );


    // ------------------------ edit -------------------------------

    // 编辑url
    protected $update_url = '/dmp_admin/index/task_config_edit';

    // 编辑页面
    protected $_edit = array(
        'name',
        'config',
        'description'
    );

    // 需要验证的字段
    protected $edit_validate = array(
        'name',
//        'config',
        'description'
    );

}