<?php
namespace app\admin\controller;

/**
 * Class Rbac 用户管理
 * @package app\admin\controller
 */
class Rbac extends Common {

    // 表名
    protected $table            = 'admin';
    // 添加 页面
    protected $view_add         = 'add_admin';
    // 主页 页面
    protected $view_index       = 'user_manage';
    // 编辑 页面
    protected $view_edit        = 'edit_admin';
    

    function __construct(){
        parent::__construct();
        $this->noCheckAccess(['addAssign', 'role', 'editAssign', 'getParamsAdd', 'userData']);
    }


    // ----------------------------------
    // 添加 assign
    // ----------------------------------
    public function addAssign(){
        $this->assign("role", $this->all('role'));
        $this->assign("group", $this->treeStyleData("group", "<option value='\$id' \$selected>\$spacer \$group</option>"));
    }

    // ----------------------------------
    // 角色 下拉数据
    // ----------------------------------
    public function role(){
        return json_encode(['data' => $this->all('role')]);
    }

    // ----------------------------------
    // 编辑 assign
    // ----------------------------------
    public function editAssign(){
        $this->assign("role", $this->all('role'));
        $this->assign("group", $this->treeStyleData("group", "<option value='\$id' \$selected>\$spacer \$group</option>"));
    }


    // ----------------------------------
    // 参数 添加
    // ----------------------------------
    public function getParamsAdd(){
        // 获取请求数据
        $user_name = I('username', '');
        $group_id  = I('group_id', 0);
        $role_id   = I('role_id', 0);

        return [
            'username'      => $user_name,
            'group_id'      => $group_id,
            'role_id'       => $role_id,
            'head'          => '/static/cmf/upload/default.png',
            'nikname'       => 'anonymous',
            'create_time'   => time(),
            'password'      => password_hash('123456', PASSWORD_DEFAULT)
        ];
    }




    // ----------------------------------
    // 用户信息
    // ----------------------------------
    public function userData(){
        $userinfos = $this->all('admin');

        $ret_data_arr = [];
        foreach ($userinfos as $key => $userinfo) {
            $role_info      = $this->one('role',  ['id' => $userinfo['role_id']]);
            $grouup_info    = $this->one('group', ['id' => $userinfo['group_id']]);

            $tmp_arr['id']          = $userinfo['id'];
            $tmp_arr['username']    = $userinfo['username'];
            $tmp_arr['role_id']     = $userinfo['role_id'];
            $tmp_arr['group_id']    = $userinfo['group_id'];
            $tmp_arr['role']        = $role_info['role'];
            $tmp_arr['group']       = $grouup_info['group'];

            array_push($ret_data_arr, $tmp_arr);
        }

        return json_encode(['data' => $ret_data_arr]);
    }

}
?>