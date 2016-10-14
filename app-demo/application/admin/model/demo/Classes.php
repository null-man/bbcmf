<?php
namespace app\admin\model\demo;

use bb\admin\model\AdminModel;

class Classes extends AdminModel {

    protected $table = 'demo_classes';

    // 数据库结构
    protected $model = array(
        'id' => array('ID', 'hidden'),
        'name' => array('班级', 'str'),
        'updated_at' => array('更新时间', 'str'),
        'created_at' => array('创建时间', 'str')
    );

    protected $field = ['name', 'student'];

    protected $front_validate = ['name'];

    protected $list = ['id', 'name', 'updated_at', 'created_at'];

    protected $manytoone = [
        'student' => ['app\\admin\\model\\demo\\Students', 'class_id']
    ];

    protected $actions = [
        ['编辑', 'edit'],
        ['删除',  'del',],
        ['日志', 'demo.Students1/index']
    ];
}