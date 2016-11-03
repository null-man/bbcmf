<?php
namespace app\bbdata\controller;
use think\Controller;
use bb\DB;

class Login extends Controller{

    public function index(){
        if(IS_GET){
            return $this->fetch('login');
        }

        if(IS_POST){
            $name = I('name');
            $password = I('password');

            $user = DB::table('users')->where(array('name'=>$name, 'password'=>$password))->first();

            if($user){
                session('USER_ID',$user['id']);
                // 登录时间写入日志
                DB::table("login_front_log")->insert(array('login_time'=>time(), 'ip'=>$_SERVER["REMOTE_ADDR"], 'user_id'=>$user['id']));
                echo 1;
            }else{
                echo 0;
            }
        }
    }

    // 退出 登录
    public function logout(){
        session('USER_ID', null);
        return $this->success('退出成功', '/bbdata/login/index');
    }
}
