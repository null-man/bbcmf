<?php
namespace app\admin\model\demo;

use bb\admin\model\AdminModel;

class Interests extends AdminModel {

    protected $table = 'demo_interests';

    // 数据库结构
    protected $model = array(
        'id' => array('ID', 'hidden'),
        'name' => array('兴趣', 'str'),
        'updated_at' => array('更新时间', 'str'),
        'created_at' => array('创建时间', 'str')
    );

    protected $field = ['name'];

    // protected $front_validate = ['name'];

    protected $list = ['id', 'name', 'updated_at', 'created_at'];

}