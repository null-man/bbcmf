<?php
// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZergL <lin029011@163.com>
// +----------------------------------------------------------------------

namespace bb\service;

use bb\Service;
use bb\service\LoginService;
use bb\DB;

class AdminService extends Service {

	// 是否超级管理员
	public function isSuperAdmin() {
		$login = new LoginService();
		$id = $login->getSessionID();		
		return $id == 1;
	}


    public function getAllRoleAuth() {
        // 全部的rule
        $modules = DB::table('rule')->get();

        $role_auth = array();
        foreach($modules as $k => $v){
            $tmp = array();
            foreach($v as $key => $value){
                $tmp['id'] = $v['id'];
                $tmp['name'] = $v['name'];
                $tmp['parentid'] = $v['parentid'];
                $tmp['rule_name'] = $v['rule_name'];
                break;
            }
            array_push($role_auth, $tmp);
        }

        // 组装树型结构数组
        $role_auth = $this->get_tree_array(0, $role_auth);
        return $role_auth;

            
    }

    // 获得用户菜单权限
    public function getUserRule($id) {
        // 角色的rule
        $user_rule = DB::table('role_auth')->where(array('role_id'=>$id))->get();

        $user_rule_arr = array();
        foreach($user_rule as $k => $v){
            foreach($v as $key => $value){
                if($key == 'rule_id'){
                    $ret = DB::table('rule')->where(array('id'=>$value))->first();
                    array_push($user_rule_arr,$ret['id']);
                }
            }
        }
        return $user_rule_arr;
    }

    // 更新用户菜单权限
    public function updateRoleAuth($auth, $id) {
        $auth_arr = explode(",", $auth);
        // 更新权限
        DB::table('role_auth')->where(array('role_id'=>$id))->delete();
        foreach($auth_arr as $k => $v){
            if($v != ''){
                $data['role_id'] = $id;
                $data['rule_id'] = $v;
                $ret = DB::table('role_auth')->insert($data);
                if(!$ret){
                   return 0;
               }
            }
        }
        return 1;
    }


	// 获得管理权限
	public function getAdminAuth() {
		$role_auth_tmp = DB::table('rule')->where('show', 1)->get();

        $role_auth = array();
        foreach($role_auth_tmp as $k => $v){
            $tmp = array();
            foreach($v as $key => $value){
                $tmp['id'] = $v['id'];
                $tmp['name'] = $v['name'];
                $tmp['parentid'] = $v['parentid'];
                $tmp['rule_name'] = $v['rule_name'];
                $tmp['icon'] = $v['icon'];
                break;
            }
            array_push($role_auth, $tmp);
        }
        return $this->get_tree_array(0, $role_auth);
	}


    // 获得用户权限
    public function getUserAuth() {

        $login = new LoginService();
        $id = $login->getSessionID();

        // 获取用户
        $user =DB::table('users')->where('id', $id)->first();
        // 获取用户角色对应的rule
        $role_auth_tmp = DB::table('role_auth')->where('role_id', $user['role_id'])->get();

        $role_auth = array();
        foreach($role_auth_tmp as $k => $v){
            $tmp = array();
            foreach($v as $key => $value){
                $rule = DB::table('rule')->where(array('id'=>$v['rule_id'], 'show'=>1))->first();
                if(isset($rule)){
                    $tmp['id'] = $rule['id'];
                    $tmp['name'] = $rule['name'];
                    $tmp['parentid'] = $rule['parentid'];
                    $tmp['rule_name'] = $rule['rule_name'];
                    $tmp['icon'] = $rule['icon'];
                }
                break;
            }

            if(count($tmp) > 0){
                array_push($role_auth, $tmp);
            }
        }
        return $this->get_tree_array(0, $role_auth);
    }


    // 修改密码
    public function changePassword($password) {
        $data['password'] = $password;
        $this->updateUserInfo($data);
    }


    // 更新个人信息
    public function updateUserInfo($data, $id = null) {
        $login = new LoginService();
        if(empty($id))                  $id = $login->getSessionID();
        if(isset($data['password']))    $data['password'] = $login->encry($password);
        return DB::table('users')->where('id', $id)->update($data);
    }

    // 获得菜单
    public function getMenuAuth() {
        // 所有权限
        $modules = DB::table('rule')->get();

        $role_auth = array();
        foreach($modules as $k => $v){
            $tmp = array();
            foreach($v as $key => $value){
                $tmp['id'] = $v['id'];
                $tmp['name'] = $v['name'];
                $tmp['parentid'] = $v['parentid'];
                $tmp['rule_name'] = $v['rule_name'];
                $tmp['show'] = $v['show'];
                break;
            }
            array_push($role_auth, $tmp);
        }

        // 组装树型结构数组
        $role_auth = $this->get_tree_array(0, $role_auth);
        return $role_auth;
    }

    // 获得menu信息
    public function getMenuInfo($id) {
        $rule = DB::table('rule')->where("id", $id)->first();
        return $rule;
    }

    // 获得所有一级菜单
    public function getMenuParent() {
        $parent = DB::table('rule')->where("parentid", 0)->get();
        return $parent;
    }

    // 更新菜单
    public function updateMenu($id, $parentid, $name, $module, $controller, $action, $show) {
        $data['name'] = $name;
        $data['rule_name'] = "/$module/$controller/$action";
        if(isset($parentid)) {
            $data['parentid'] = $parentid;
        }
        $data['show'] = $show;
        $ret = DB::table('rule')->where("id", $id)->update($data);
        return $ret;
    }

    // 删除菜单
    public function deleteMenu($id) {

        $rule = DB::table('rule')->where('id', $id)->first();
        $flag = false;
        if($rule['parentid'] == 0){

            $ret = DB::table('rule')->where('id', $id)->delete();
            if(!$ret){
                return false;
            }
            $ret2 = DB::table('rule')->where('parentid', $id)->delete();
            $flag = $ret2 ? true : false;
        }else{
            $ret = DB::table('rule')->where('id', $id)->delete();
            $flag = $ret ? true : false;
        }

        return $flag;
    }


    // 添加菜单
    public function addMenu($parentid, $name, $module, $controller, $action, $show) {

        if ($parentid == -1){
            $show = 1;
            $parentid = 0;
        }

        $data['name'] = $name;
        $data['rule_name'] = "/$module/$controller/$action";
        $data['parentid'] = $parentid;
        $data['show'] = $show;

        $rule = DB::table('rule')->where($data)->first();

        if($rule) {
            return false;
        }

        $ret = DB::table('rule')->insert($data);
 
        return $ret;
    }


    // 获得所有角色
    public function getAllRole($isopen = false) {

        if($isopen) {
            return DB::table('role')->where('state', 1)->get();
        }
        return DB::table('role')->get();
    }        
        
    // 获得角色
    public function getRole($id) {
        $user = DB::table('role')->where('id', $id)->first();
        return $user;
    }  

    // 更新角色
    public function updateRole($id, $name, $state) {
        $ret = DB::table('role')->where('id', $id)->update(['name' => $name, 'state' => $state]);
        return $ret;
    }

    // 添加角色
    public function addRole($name, $state) {
        $data['name'] = $name;
        $data['state'] = $state;
        $ret = DB::table('role')->insert($data);
        return $ret;
    }

    // 删除角色
    public function deleteRole($id) {
        $ret = DB::table('role')->where('id', $id)->delete();
        return $ret;
    }


    // 获得所有用户
    public function getAllUser($exceptSuper = true) {
        if($exceptSuper) {
            return DB::table('users')->where('id', '<>', 1)->get();
        } else {
            return DB::table('users')->get();
        }
    }

    // 获得用户
    public function getUser($id) {
        return DB::table('users')->where('id', $id)->first();
    }

    // 添加用户
    public function addUser($name, $password, $role) {

        $login = new LoginService();

        $data['name'] = $name;
        $data['password'] = $login->encry($password);
        $data['role_id'] = $role;
        $data['reg_time'] = time();

        $ret = DB::table('users')->insert($data);
        return $data;
    }

    // 删除用户
    public function deleteUser($id) {
        $ret = DB::table('users')->where('id', $id)->delete();
        return $ret;
    }



	/**
     * 得到子级数组
     * @param int
     * @param array
     * @return array
     */
    public function get_child($myid, $arr) {
        $a = $newarr = array();
        if (is_array($arr)) {
            foreach ($arr as $id => $a) {
                if ($a['parentid'] == $myid)
                    $newarr[$id] = $a;
            }
        }
        return $newarr ? $newarr : false;
    }

    /**
     * 得到树型结构数组
     * @param int ID，表示获得这个ID下的所有子级
     * @param array 数组
     * @return array
     */
    public function get_tree_array($myid, $arr) {
        $retarray = array();
        //一级栏目数组
        $child = $this->get_child($myid, $arr);
        if (is_array($child)) {
            foreach ($child as $id => $value) {
                @extract($value);
                $retarray[$value['id']] = $value;
                $retarray[$value['id']]["child"] = $this->get_tree_array($id, $arr);
            }
        }
        return $retarray;
    }

}