<?php

namespace app\dmp_admin\model;

class EventArgv extends BaseModel {

    protected $table = 'event_argv';

    // 数据库结构
    protected $_model = array(
        'id' => array('事件参数ID', 'hidden'),
        'event_id' => array('事件', 'select'),
        'name' => array('参数名称', 'str'),
        'type' => array('参数类型', 'select'),
        'updated_at' => array('更新时间', 'str'),
        'created_at' => array('创建时间', 'str')
    );

    // ------------------------- table --------------------------

    // 列表显示
    protected $_list = array(
        'id',
        'event_id',
        'name',
        'type',
        'updated_at',
        'created_at',
    );

    // 批量操作
    protected $list_actions = array(
        array('批量删除', 'alldel', 'http://xx1',  array('info', '标题', '描述'))
    );

    // 单行操作
    protected $actions = array(
        array('编辑', 'edit', '/dmp_admin/index/event_argv_edit/id', array('redirect')),
        array('删除', 'del', '/dmp_admin/index/event_argv_del/id', array('modal', '删除', '确定删除吗?'))
    );

    // 数据解析关系
    protected $relations = array(
        array('app\dmp_admin\model\Event' => array('event_id', 'name')),
        array('type' => 'type_arr')
    );

    // 过滤字段
    protected $filter_list = array(
        'event_id',
        'name',
        'type'
    );

    // 过滤提交 url
    protected $filter_submit = "./event_argv_index";

    // ------------------------ add --------------------------------

    // 插入url
    protected $insert_url = '/dmp_admin/index/event_argv_add';

    // 添加页面
    protected $_add = array(
        'event_id',
        'name',
        'type',
    );

    // 需要验证的字段
    protected $add_validate = array(
        'event_id',
        'name',
    );

    // ------------------------ edit -------------------------------

    // 编辑url
    protected $update_url = '/dmp_admin/index/event_argv_edit';

    // 编辑页面
    protected $_edit = array(
        'id',
        'event_id',
        'name',
        'type',
    );

    // 需要验证的字段
    protected $edit_validate = array(
        'event_id',
        'name',
    );


    // ----------------------- 公共 --------------------------------

    // 外键(表单添加、编辑)
    protected $form_foreign = array(
        'event_id'=>array('app\dmp_admin\model\Event', 'name'),
        'type'=>'type_arr'
    );

    // 外键对应数组(all)
    protected $type_arr = array(
        '1'=>'int',
        '0'=>'str'
    );

}