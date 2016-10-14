<?php
namespace app\admin\model\demo;

use bb\admin\model\AdminModel;

class Students extends AdminModel {

    protected $table = 'demo_students';

    // 数据库结构
    protected $model = array(
        'id' => array('ID', 'hidden'),
        'name' => array('姓名', 'str'),
        'class_id'  => array('班级', 'select'),
        'sex' => array('性别', 'select'),
        'updated_at' => array('更新时间', 'str'),
        'created_at' => array('创建时间', 'str')
    );

    protected $field = ['name', 'class_id', 'sex', 'intersts'];

    protected $front_validate = ['name', 'class_id'];

    protected $list = ['id', 'name', 'class_id', 'updated_at', 'created_at', 'intersts'];

    // 外键（包括多对多)
    protected $foreign = [
        'class_id'      => ['app\\admin\\model\\demo\\Classes', 'name'],
        'intersts'      => ['app\\admin\\model\\demo\\Interests', 'name'],
        'sex' => 'sex'
    ];

    // 外键对应数组(all)
    public $sex = [
        '1'=>'女',
        '0'=>'男'
    ];

    // 多对多
    protected $manytomany = [
        'intersts' => ['兴趣', 'intersts']
    ];

    // 多对多对应的 数组
    protected $intersts =
    ['app\\admin\\model\\demo\\Interests',
    'demo_students_interests',
    'students_id',
    'interests_id'];


}