<?php
namespace app\index\controller;

use think\Controller;


class Index extends Controller
{

    protected $beforeActionList = ['abc' => ['only' => 'index']];

    public function abc() {

        echo 'beforeActionList<br/>';
    }

    public function index() {
        dump('aaa');
        return $a;
    }



}
