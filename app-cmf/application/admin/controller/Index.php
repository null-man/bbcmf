<?php
namespace app\admin\controller;

class Index extends Common {
    // 主页 页面
    protected $view_index       = 'index';

    function __construct(){
        parent::__construct();
        $this->noCheckAccess();
    }


    // ----------------------------------
    // 主页
    // ----------------------------------
    public function indexView(){
        // 获取系统信息
        $this->assign('uname', php_uname('s'));
        $this->assign('sapi_name', php_sapi_name());
        $this->assign('apache_info', apache_get_version());
        $this->assign('max_execution_time', ini_get("max_execution_time"));
        $this->assign('upload_max_filesize', ini_get("upload_max_filesize"));
    
        return $this->fetch('index_view');
    }

    // ----------------------------------
    // 所有菜单
    // ----------------------------------
    public function menu(){
        // 超级管理员
        $role_id = session('role_id');

        if ($role_id == 1){
            $show_data = [];
            foreach ($this->all('bbcmf_rule') as $key => $value) {
                $tmp = [];
                foreach ($value as $k => $v) {
                    $tmp[$k] = ($k == 'src') ? admin_url($v): $v;
                }
                array_push($show_data, $tmp);
            }
            $auth = json_encode(['data' => $show_data]);
        } else {
            $role_auths = $this->all('bbcmf_role_auth', ['role_id' => $role_id]);

            $rule_arr = [];
            foreach ($role_auths as $key => $role_auth) {
                $rule_info = $this->one('bbcmf_rule', ['id' => $role_auth['rule_id']]);
                $rule_info['src'] = admin_url($rule_info['src']);
                array_push($rule_arr, $rule_info);
            }

            $auth = json_encode(['data' => $rule_arr]);
        }

        return $auth;
    }



    // ----------------------------------
    // 网站配置
    // ----------------------------------
    public function siteConfig(){
        $admin_id = session('id');

        if (IS_GET) {    
            $this->assign('site_config', $this->one('bbcmf_site_config', ['admin_id' => $admin_id]));
            return $this->fetch('web_config');    
        }

        if (IS_POST) {
            if ($this->one('bbcmf_site_config', ['admin_id' => $admin_id])) {
                $ret = $this->update('bbcmf_site_config', $_POST, ['admin_id' => $admin_id]);
            } else {
                $_POST['admin_id'] = $admin_id;
                $ret = $this->insert('bbcmf_site_config', $_POST);
            }

            return $this->resultRedirect($ret, 'siteConfig');
        }
    }



    // ----------------------------------
    // 网站设置
    // ----------------------------------
    public function siteSet(){
        if (IS_GET) {
            $this->assign('site_set', $this->one('bbcmf_site_set', ['id' => 1]));
            return $this->fetch('web_set');
        }

        if (IS_POST) {
            $ret = $this->update('bbcmf_site_set', $_POST, ['id' => 1]);
            return $this->resultRedirect($ret, 'siteSet');
        }
    }



    // ----------------------------------
    // 个人信息
    // ----------------------------------
    public function userInfo(){
        if (IS_GET) {
            // 用户信息
            $userinfo = $this->one('bbcmf_admin', ['id' => session('id')]);
            $this->assign('user', $userinfo);
            // 用户角色
            $this->assign('user_role', $this->one('bbcmf_role', ['id' => $userinfo['role_id']]));
            // 用户组
            $this->assign('user_group', $this->one('bbcmf_group', ['id' => $userinfo['group_id']]));

            return $this->fetch('user');
        }

        if (IS_POST) {
            if (isset($_FILES['head'])) {
                $head = $this->uploadFile('head');
                if (!$head) {
                    return $this->resultResponseAjax(FALSE);    
                }
                $_POST['head'] = $head;
            }

            if ($_POST['head'] == 'undefined') {
                unset($_POST['head']);
            }

            unset($_POST['confirm_password']);
            if (!empty($_POST['password'])) {
                $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                unset($_POST['password']);
            }

            return $this->resultResponseAjax($this->update('bbcmf_admin', $_POST, ['id' => session('id')]));
        }
    }


    // ----------------------------------
    // 所有用户组
    // ----------------------------------
    public function groupTree(){
        // ###组装树形结构
        // 获取树形结构名称
        $name_arr = [];
        foreach (explode('|', $this->treeStyleData('bbcmf_group', "\$id - \$spacer \$group |")) as $_k => $_v) {
            if ($_v) {
                $k_v_arr = explode(' - ', $_v);
                $name_arr[$k_v_arr[0]] = $k_v_arr[1];
            }
        }

        // 重新组装名称
        $show_data = [];
        foreach ($this->all('bbcmf_group') as $key => $rule) {
            foreach ($rule as $k => $v) {
                $tmp[$k] = ($k == 'group') ? $name_arr[$rule['id']] : $v; 
            }
            array_push($show_data, $tmp);
        }

        return $show_data;
    }

}
?>