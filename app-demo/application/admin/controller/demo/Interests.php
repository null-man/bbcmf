<?php
namespace app\admin\controller\demo;

class Interests extends Index {

	protected $model = '\\app\\admin\\model\\demo\\Interests';

    protected $page = 20;

    protected $filter = ['name'];


}