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
use bb\DB;
use bb\Parse;

class LoginService extends Service {


    protected $config = [
        'encry'                 => true,                    // 是否加密
        'post_name'             => 'name',                  // 用户名POST参数名
        'post_password'         => 'password',              // 密码POST参数名
        'login_url'             => '/admin/login/index',    // 登录页面url
        'login_session_name'    => 'ADMIN_ID',              // 登录sessionID
    ];


	public function __construct() {

        $config = C('admin');

		if(!empty($config)) {
			$this->config = array_merge($this->config, $config);
		}

	}


	public function login() {
		// 登录参数
		$name 		= I($this->config['post_name'], '');
        $password 	= I($this->config['post_password'], '');

        // 密码处理
        $password 	= $this->config['encry'] ? $this->encry($password) : $password;

       	// 登录验证
        $user 		= $this->validation($name, $password);

        $ret = '';
        // 返回信息
		if($user) {
			session($this->config['login_session_name'], $user['id']);
			// 登录时间写入日志
            $ret = Parse::json()->encode(['status' => 1, 'info' => $this->adminUrl()]);
		} else { // 登录失败
			$ret = Parse::json()->encode(['status' => 0, 'info' => '用户名或密码错误']);
		}

        // $this->log($user);
        return $ret;
	}


	// 登出
	public function logout() {
		session($this->config['login_session_name'], null);
	}


    public function loginUrl() {
        $url = $this->config['login_url'];
        return admin_url($url);
    }


    public function adminUrl() {
        $url = $this->config['url'];
        if(empty($url)) {
            $url = '/admin/index';
        }
        return admin_url($url);
    }


	// 验证登录信息
    public function validation($name, $password) {
        $user = DB::table('users')
        	->where([
        		'name'		=> $name, 
        		'password'	=> $password
        	])->first();
        return $user;
    }


    // 密码操作
    public function encry($password) {
        if($this->config['encry']) {
            return md5($password);
        } else {
            return $password;
        }
    }


    // 日志
    public function log($user) {
		DB::table('login_log')->insert([
            'login_time' =>  time(),
            'ip'         =>  $_SERVER["REMOTE_ADDR"], 
            'user_id'    =>  empty($user) ? null : $user['id'],
            'success'    =>  empty($user) ? 0 : 1
		]);
    }

    public function getSessionID() {
        return session($this->config['login_session_name']);
    }


    public function isLogin() {
        $id = $this->getSessionID();
        return isset($id);
    }


    public function getUserInfo($id = null) {
        if(empty($id)) $id = $this->getSessionID();
        return DB::table('users')->where('id', $id)->first();
    }


    public function checkAccess($id = null) {
        if(empty($id)) $id = $this->getSessionID();
        if(isset($id)) {
            if($this->_check_access($id)) {
                return true;
            }
        } 
        return false;
    }









    // 检查权限
    private function _check_access($uid){
        // 如果用户权限是1，则无需判断
        if($uid == 1){
            return true;
        }

        $rule = '/'.MODULE_NAME .'/'. CONTROLLER_NAME . '/' .ACTION_NAME;

        // 用户必须使用的一些操作 给权限(主页)
        // $no_need_check_rules=array($this->config['admin_url'].'/index', $this->config['admin_url'].'/main', $this->config['admin_url'].'/logout');
        // if(in_array($rule, $no_need_check_rules)){
        //     return true;
        // }

        // 获取用户
        $user = DB::table('users')->where(array('id' => $uid))->first();

        // 角色对应的权限
        $user_rule= DB::table('role_auth')->where(array('role_id'=>$user['role_id']))->get();

        // 权限flag
        $flag = false;

        foreach($user_rule as $k => $v){
            foreach($v as $key => $value){
                if($key == 'rule_id'){
                   $ret = DB::table('rule')->where(array('id'=>$value))->first();
                   if ($ret){
                       // 判断权限
                       if($ret['rule_name'] === $rule){
                           $flag = true;
                           break;
                       }
                   }
                }
            }
        }

        return $flag;
    }

}
