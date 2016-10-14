<?php
namespace app\admin\controller\demo;

class Students extends Index {

	protected $model = '\\app\\admin\\model\\demo\\Students';

    protected $page = 20;

    protected $filter = ['name', 'class_id', 'intersts'];

}