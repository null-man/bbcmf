<?php

namespace app\dmp_admin\model;

class Handler extends BaseModel {

    protected $table = 'handler';

    // 数据库结构
    protected $_model = array(
        'id' => array('处理系统ID', 'hidden'),
        'name' => array('处理系统名称', 'str'),
        'show_name' => array('处理系统显示名称', 'str'),
        'updated_at' => array('更新时间', 'str'),
        'created_at' => array('创建时间', 'str')
    );

    // ------------------------- table --------------------------

    // 列表显示
    protected $_list = array(
        'id',
        'name',
        'show_name',
        'updated_at',
        'created_at',
    );

    // 批量操作
    protected $list_actions = array(
        array('批量删除', 'alldel', 'http://xx1')
    );

    // 单行操作
    protected $actions = array(
        array('编辑', 'edit', '/dmp_admin/index/handler_edit/id'),
        array('删除', 'del', '/dmp_admin/index/handler_del/id')
    );

    // 过滤字段
    protected $filter_list = array(
        'name'
    );

    // ------------------------ add --------------------------------

    // 插入url
    protected $insert_url = '/dmp_admin/index/handler_add';

    // 添加页面
    protected $_add = array(
        'name',
        'show_name',
    );

    // 需要验证的字段
    protected $add_validate = array(
        'name',
        'show_name',
    );

    // ------------------------ edit -------------------------------

    // 编辑url
    protected $update_url = '/dmp_admin/index/handler_edit';

    // 编辑页面
    protected $_edit = array(
        'id',
        'name',
        'show_name',
    );

    // 需要验证的字段
    protected $edit_validate = array(
        'name',
        'show_name',
    );

}