<?php
namespace app\bbdata\controller;

use bb\Controller;
use bb\DB;

class Index extends Controller{

    public function index(){
        // 超级管理员
        if(session('USER_ID') == 1){
            $role_auth = DB::table('rule_front')->where('show', 1)->get();
        }else{
            // 获取用户
            $user =DB::table('users')->where(array('id'=>session('USER_ID')))->first();
            // 获取用户角色对应的rule
            $role_auth_tmp = DB::table('role_auth_front')->where(array('role_id'=>$user['role_id']))->get();

            $role_auth = array();
            foreach($role_auth_tmp as $k => $v) {
                $tmp = array();
                foreach ($v as $key => $value) {
                    $rule = DB::table('rule_front')->where(array('id' => $v['rule_front_id'], 'show' => 1))->first();
                    if (isset($rule)) {
                        $tmp['id'] = $rule['id'];
                        $tmp['name'] = $rule['name'];
                        $tmp['rule_name'] = $rule['rule_name'];
                        $tmp['cover'] = $rule['cover'];
                    }
                    break;
                }
                array_push($role_auth, $tmp);
            }
        }

        $this->assign('all_module', $role_auth);
        return $this->fetch('index');
    }

}
