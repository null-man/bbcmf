<?php
namespace app\admin\controller\demo;

class Classes extends Index {

	protected $model = '\\app\\admin\\model\\demo\\Classes';

    protected $page = 20;

    protected $filter = ['name'];

}