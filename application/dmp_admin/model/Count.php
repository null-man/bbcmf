<?php

namespace app\dmp_admin\model;

class Count extends BaseModel {

    protected $table = 'count';

    // 数据库结构
    protected $_model = array(
        'id' => array('ID', 'hidden'),
        'event_id' => array('事件ID', 'select'),
        'count_type_id' => array('计数类型', 'select'),
        'updated_at' => array('更新时间', 'str'),
        'created_at' => array('创建时间', 'str')
    );

    // ------------------------- table --------------------------

    // 列表显示
    protected $_list = array(
        'id',
        'event_id',
        'count_type_id',
        'updated_at',
        'created_at',
    );

    // 批量操作
    protected $list_actions = array(
        array('批量删除', 'alldel', '/dmp_admin/index/count_list_del')
    );

    // 单行操作
    protected $actions = array(
        array('编辑', 'edit', '/dmp_admin/index/count_edit/id'),
        array('删除', 'del', '/dmp_admin/index/count_del/id')
    );

    // 数据解析关系
    protected $relations = array(
        array('app\dmp_admin\model\Event'=>array('event_id', 'name')),
        array('app\dmp_admin\model\CountType'=>array('count_type_id', 'name')),
    );

    // 外键
    protected $form_foreign = array(
        'event_id'=>array('app\dmp_admin\model\Event', 'name'),
        'count_type_id'=>array('app\dmp_admin\model\CountType', 'name')
    );

    // 过滤字段
//    protected $filter_list = array(
//        'id'
//    );

    // ------------------------ add --------------------------------

    // 插入url
    protected $insert_url = '/dmp_admin/index/count_add';

    // 添加页面
    protected $_add = array(
        'event_id',
        'count_type_id',
    );

    // 需要验证的字段
    protected $add_validate = array(
        'event_id',
        'count_type_id',
    );

    // ------------------------ edit -------------------------------

    // 编辑url
    protected $update_url = '/dmp_admin/index/count_edit';

    // 编辑页面
    protected $_edit = array(
        'event_id',
        'count_type_id',
    );

    // 需要验证的字段
    protected $edit_validate = array(
        'event_id',
        'count_type_id',
    );

    // ----------------------- 公共 --------------------------------


}