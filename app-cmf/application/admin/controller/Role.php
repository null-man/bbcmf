<?php
namespace app\admin\controller;

/**
 * Class Role 角色类
 * @package app\admin\controller
 */
class Role extends Common {

    // 表名
    protected $table            = 'bbcmf_role';
    // 添加 页面
    protected $view_add         = 'add_role';
    // 编辑 页面
    protected $view_edit        = 'edit_role';
    // 主页 页面
    protected $view_index       = 'role_manage';
    

    function __construct(){
        parent::__construct();
        $this->noCheckAccess(['roleData', 'menuTree', 'getParamsEdit', 'editPower']);
    }


    // ----------------------------------
    // 所有 角色
    // ----------------------------------
    public function roleData(){
        // 所有角色
        $all_role = $this->all('bbcmf_role');
        
        // 剔除超级管理员
        $final_data = [];
        foreach ($all_role as $key => $role) {
            $tmp = [];
            foreach ($role as $k => $v) {
                if ($k == 'id' && $v == 1) {
                    break;
                }
                $tmp[$k] = $v;
            }
            
            !empty($tmp) && array_push($final_data, $tmp);
        }
        return json_encode(['data' => $final_data]);
    }


    // ----------------------------------
    // 树形 角色数据
    // ----------------------------------
    public function menuTree(){
        // 获取请求数据
        $id = I('id', 0);

        if (empty($id)) {
            return false;
        }

        // ###组装树形结构菜单
        // 获取树形结构名称
        $name_arr = [];
        foreach (explode('|', $this->treeStyleData('bbcmf_rule', "\$id - \$spacer \$name |")) as $_k => $_v) {
            if ($_v) {
                $k_v_arr = explode(' - ', $_v);
                $name_arr[$k_v_arr[0]] = $k_v_arr[1];
            }
        }

        // 获取当前用户权限数据
        $user_auth = $this->all('bbcmf_role_auth', ['role_id' => $id]);
        $user_auth_id = [];
        foreach ($user_auth as $key => $value) {
            array_push($user_auth_id, $value['rule_id']);
        }
    
        // 重新组装名称
        $show_data = [];
        foreach ($this->all('bbcmf_rule') as $key => $rule) {
            foreach ($rule as $k => $v) {
                $tmp[$k] = ($k == 'name') ? $name_arr[$rule['id']] : $v; 
            }
            // 当前用户权限
            $tmp['check'] = in_array($rule['id'], $user_auth_id) ? 1 : 0;
            array_push($show_data, $tmp);
        }

        return json_encode(['data' => ['id' => $id, 'power' => $show_data]]);
    }


    // ----------------------------------
    // 编辑 角色 参数
    // ----------------------------------
    public function getParamsEdit(){
        return ['role' => I('role', '')];
    }


    // ----------------------------------
    // 权限设置
    // ----------------------------------
    public function editPower(){
        if (IS_GET) {
            return view('edit_power');
        }

        if (IS_POST) {
            // 请求数据
            $id     = I('id', 0);            
            $auth   = I('power');
            $auths  = explode(',', substr($auth, 0, -1));

            if (empty($id)) {
                return false;
            }

            // 开启事务
            $transaction_ret = $this->transaction(function() use ($auths, $id) {
                // 先删除所有角色权限
                $del_ret = $this->delete('bbcmf_role_auth', ['role_id' => $id]);
                // 重新赋予权限
                foreach ($auths as $key => $auth_id) {
                    $insert_ret = $this->insert('bbcmf_role_auth', ['role_id' => $id, 'rule_id' => $auth_id]);
                }
            });
            
            return $this->resultResponseAjax($transaction_ret, '编辑权限成功', '编辑权限失败');
        }
    }


}
?>