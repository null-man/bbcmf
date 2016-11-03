<?php

namespace app\admin\controller;
use think\Controller;
use bb\DB;

class HTController extends Controller
{


    /**
     * 不判断权限的构造函数
     */
    public function _init(){
        parent::__construct();
    }



    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        // 判断是否登录
        $id = session('ADMIN_ID');
        if(isset($id)){
            $users_obj= DB::table("users");
            $user=$users_obj->where("id", $id)->first();
            // 权限检查
            if(!$this->check_access($id)){
                $this->redirect('/admin/login/index/');
//                echo 'no access';
                exit();
            }
            $this->assign("admin",$user);
        }else{
//            $this->error('dasd','/admin/login/index/');
            $this->redirect('/admin/login/index/');
//            echo 'no login ';
            exit();
        }
    }

    // 检查权限
    private function check_access($uid){
        // 如果用户权限是1，则无需判断
        if($uid == 1){
            return true;
        }

        $rule = '/'.MODULE_NAME .'/'. CONTROLLER_NAME . '/' .ACTION_NAME;

        // 用户必须使用的一些操作 给权限(主页)
        $no_need_check_rules=array("/admin/index/index", '/admin/index/main', '/admin/index/logout');
        if(in_array($rule,$no_need_check_rules)){
            return true;
        }

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
