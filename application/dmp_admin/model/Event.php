<?php

namespace app\dmp_admin\model;

class Event extends BaseModel {

    protected $table = 'event';

    // 数据库结构
    protected $_model = array(
        'id' => array('事件ID', 'hidden'),
        'name' => array('事件名称', 'str'),
        'show_name' => array('事件显示名称', 'date'), // textarea
        'cron_id' => array('任务', 'onetoone'),
        'updated_at' => array('更新时间', 'date'),
        'created_at' => array('创建时间', 'str')
    );

    // ------------------------- table --------------------------

    // 列表显示
    protected $_list = array(
        'id',
        'name',
        'show_name',
//        'cron_id', // 如果是一对一的话 不需要这里写 写到$onetoone的数组
//        'created_at',
        'updated_at',
        'handler',
    );

    // 批量操作
//    protected $list_actions = array(
//        array('批量删除', '/dmp_admin/index/event_list_del', array('info', '标题', '描述'))
//    );

    // 单行操作
    protected $actions = array(
        array('编辑', 'edit', '/dmp_admin/index/event_edit/id', array('redirect')),
        array('删除', 'del', '/dmp_admin/index/event_del/id', array('modal', '删除', '确定删除吗?'))
    );

    // 数据解析关系
    protected $relations = array(
        array('app\dmp_admin\model\Handler'=>array('handler', 'name')),
    );

    // 过滤字段
    protected $filter_list = array(
        'name',
        'handler'
    );

    // 过滤提交 url
    protected $filter_submit = "/dmp_admin/index/event_index/";

    // ------------------------ add --------------------------------

    // 插入url
    protected $insert_url = '/dmp_admin/index/event_add';

    // 添加页面
    protected $_add = array(
        'name',
        'show_name',
        'cron_id',
        'handler'
    );

    // 需要验证的字段
    protected $add_validate = array(
        'name',
        'show_name',
    );

    // ------------------------ edit -------------------------------

    // 编辑url
    protected $update_url = '/dmp_admin/index/event_edit';

    // 编辑页面
    protected $_edit = array(
        'name',
        'show_name',
        'updated_at',
        'cron_id',
        'handler'
    );

    // 需要验证的字段
    protected $edit_validate = array(
        'name',
        'show_name'
    );

    // 需要显示 却不可操作的字段
    protected $edit_disabled = array(
//        'name',
        'show_name'
    );


    // ----------------------- 公共 --------------------------------

    // 一对一
    protected $onetoone = array(
        'cron_id'=>array(
            'app\dmp_admin\model\Task',array(
                'name', 'url', 'is_on'
            )
        )
    );

    // 外键
    protected $form_foreign = array(
        'handler'=>array('app\dmp_admin\model\Handler', 'name')
    );


    // 多对多关系 (表单、添加、编辑)
    protected $_manytomany = array(
        'handler'=>array('处理系统', 'handler')
    );

    // 多对多对应的 数组
    protected $handler = array('app\dmp_admin\model\Handler', 'event_handler', 'event_id', 'handler_id');
}