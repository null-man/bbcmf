<?php
namespace bb\admin\controller;

use bb\Controller;

use bb\admin\controller\Admin;

class Index extends Admin {

    protected $tmpl = '/index';

    protected $service = 'admin_service';
    

    // 后台主页
    public function index() {
        if($this->service->isSuperAdmin()) {
            $role_auth = $this->service->getAdminAuth();
        } else {
            $role_auth = $this->service->getUserAuth();
        }

        $this->assign('show_rule', $role_auth);
        return $this->fetch($this->tmpl);
    }


    // 默认主页
    public function main() {
        return $this->fetch('/main');
    }


    // 修改密码
    public function password() {
        if(IS_GET){
            return $this->fetch('/password');
        }

        if(IS_POST){
            $password = I('password');
            $ret = $this->service->changePassword($password);
            return $this->jump($ret, '修改', '');
        }
    }


    // 个人信息
    public function userinfo() {
        if(IS_GET){
            return $this->fetch('/userinfo');
        }

        if(IS_POST){
            $name = I('name');
            $tel= I('tel');
            $mail = I('mail');
            $data['nick_name'] = $name;
            $data['tel'] = $tel;
            $data['mail'] = $mail;
            $ret = $this->service->updateUserInfo($data);
            return $this->jump($ret, '更新', '');
        }
    }


    // 后台菜单
    public function menu() {
        $role_auth = $this->service->getMenuAuth();

        $this->assign('show_rule', $role_auth);
        return $this->fetch('/menu');
    }


    // 编辑菜单
    public function menu_info() {

        if(IS_GET){
            $id = I('id');
            // 菜单信息
            $this->assign('rule', $this->service->getMenuInfo($id));
            // 所有一级菜单
            $this->assign('parent', $this->service->getMenuParent());
            return $this->fetch('/menu_info');
        }

        if(IS_POST){

            $parentid = I('parentid');
            $id = I('id');
            $name = I('name');
            $module = I('module');
            $controller = I('controller');
            $action = I('action');
            $show = I('show',1);
            // 更新菜单
            $ret = $this->service->updateMenu($id, $parentid, $name, $module, $controller, $action, $show);

            return $this->jump($ret, '更新', '/admin/index/menu');
            
        }
    }


    // 删除菜单
    public function menu_del() {
        $id = I('id');

        $ret = $this->service->deleteMenu($id);

        return $this->jump($ret, '删除', '/admin/index/menu');
    }


    // 添加菜单
    public function menu_add() {
        if(IS_GET){
            // 获取所有的父级菜单
            $this->assign('parent_rule', $this->service->getMenuParent());
            return $this->fetch('/menu_add');
        }

        if(IS_POST){
            $parentid = I('parentid');
            $name = I('name');
            $module = I('module');
            $controller = I('controller');
            $action = I('action');
            $show = I('show',1);

            $ret = $this->service->addMenu($parentid, $name, $module, $controller, $action, $show);

            return $this->jump($ret, '添加', '/admin/index/menu');
        }
    }


    // 角色管理
    public function rbac() {
        $this->assign('role', $this->service->getAllRole());
        return $this->fetch('/rbac');
    }


    // 角色修改
    public function role_info(){
        if(IS_GET){
            $id = I('id');
            $this->assign('id', $id);
            $this->assign('user', $this->service->getRole($id));
            return $this->fetch('/role_info');
        }

        if(IS_POST){
            $id = I('id');
            $name = I('name');
            $state = I('status');
            $ret = $this->service->updateRole($id, $name, $state);
            return $this->jump($ret, '更新', '/admin/index/rbac');
        }
    }


    // 添加角色
    public function role_add() {
        if(IS_GET){
            return $this->fetch('/role_add');
        }

        if(IS_POST){
            $name = I('name');
            $state = I('status');
            $ret = $this->service->addRole($name, $state);
            return $this->jump($ret, '添加', '/admin/index/rbac');
        }
    }


    // 删除角色
    public function role_del() {
        $id = I('id');
        $ret = $this->service->deleteRole($id);
        return $this->jump($ret, '删除', '/admin/index/rbac');
    }


    // 权限设置
    public function role_auth() {
        if(IS_GET){
            $id = I('id');
            $role_auth = $this->service->getAllRoleAuth();
            $user_rule_arr = $this->service->getUserRule($id);
            $this->assign('id', $id);
            $this->assign('role', $role_auth);
            $this->assign('user_rule', $user_rule_arr);
            return $this->fetch('/role_auth');
        }

        if(IS_POST){
            $auth = I('auth');
            $id = I('id');
            $ret = $this->service->updateRoleAuth($auth, $id);
            return $ret;
        }
    }


    // 管理员
    public function user(){
        // 所有用户(除开超级管理员)
        $this->assign('users', $this->service->getAllUser());
        return $this->fetch('/users');
    }

    // 管理员修改用户信息
    public function user_info() {
        if(IS_GET){
            $id = I('id');
            $user = $this->service->getUser($id);
            // 获取所有开启的角色
            $role = $this->service->getAllRole(true);
            $this->assign('role', $role);
            $this->assign('user', $user);
            return $this->fetch('/user_info');
        }

        if(IS_POST){
            $id = I('id');
            $name = I('name');
            $password = I('password');
            $role_id = I('role');

            $data['name'] = $name;
            if(!empty($password)){
                $data['password'] = $password;
            }
            $data['role_id'] = $role_id;

            $ret = $this->service->updateUserInfo($data, $id);
            return $this->jump($ret, '更新', '/admin/index/user');
        }
    }

    // 添加用户
    public function user_add() {
        if(IS_GET){
            $this->assign('role', $this->service->getAllRole());
            return $this->fetch('/user_add');
        }

        if(IS_POST){
            $name = I('name');
            $password = I('password');
            $role = I('role');
            $ret = $this->service->addUser($name, $password, $role);
            return $this->jump($ret, '添加', '/admin/index/user');
        }
    }

    // 删除用户
    public function user_del() {
        $id = I('id');
        $ret = $this->service->deleteUser($id);
        return $this->jump($ret, '删除', '/admin/index/user');
    }


    // 根据结果跳转
    protected function jump($ret, $oper, $url) {
        if($ret) {
            if(!empty($url)) $url = admin_url($url);
            return $this->success($oper.'成功', $url);
        } else {
            return $this->error($oper.'错误');
        }
    }


}
