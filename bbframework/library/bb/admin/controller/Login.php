<?php
namespace bb\admin\controller;

use bb\admin\controller\Base;

class Login extends Base {

    protected $auth = false;

    protected $tmpl = '/login';

    protected $service = 'login_service';

    public function index() {

        if(IS_GET) {    // GET请求登录页面
            return $this->fetch($this->tmpl);
        }

        if(IS_POST) {   // POST登录
            return $this->service->login();
        }

    }

    // 退出 登录
    public function logout() {
        $this->service->logout();
        return $this->success('退出成功', $this->service->loginUrl());
    }
}
