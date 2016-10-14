<?php
namespace bb\admin\controller;

use bb\Controller;
use bb\service\LoginService;

class Base extends Controller {

    protected $auth = true;

    // service
    protected $service = '';


    public function __construct() {
        parent::__construct();

        if($this->auth) {

            $login_service = new LoginService();

            // 判断登录以及权限检查
            $access = $login_service->checkAccess();

            if($access === false) {
                $this->redirect($login_service->loginUrl());
                exit();
            } else {
                $user = $login_service->getUserInfo();
                $this->assign('admin', $user);

                $this->assign('title', C('admin.title'));

                $root = STATIC_PATH . C('admin.static');
                $this->assign('STATIC_ROOT', substr($root, 1));

            }

        }

        
    }



    protected function view() {
        $template = C('template');
        $sp = STATIC_PATH . C('admin.static') . DS;
        $template['view_path'] = '.' . $sp;
        $replace = C('view_replace_str');
        $replace['"./'] = '"' . $sp;
        return \think\View::instance($template, $replace);
    }


    public function _initialize() {
        $cls = C('admin.'.$this->service);
        $this->service = new $cls();
    }

}
