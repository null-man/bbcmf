<?php
namespace app\admin\controller;
use bb\DB;
use util\TmplUtils;
use util\TmplBaseUtils;

header("Content-type: text/html;charset=utf-8");
class Index extends HTController{

    public function __construct(){
        if(in_array(ACTION_NAME,array('menu_info'))){
            parent::_init();
        }else{
            parent::__construct();
        }
    }
    public function users(){
        $this->assign('json_url', '/admin/index/table_json');
        return $this->fetch('tmpl');
    }

    public function add_user2(){
        $this->assign('json_url', '/admin/index/add_json');
        return $this->fetch('tmpl');
    }

    public function edit_user2(){
        $this->assign('json_url', '/admin/index/edit_json');
        return $this->fetch('tmpl');
    }

    public function edit_json(){
        $sex = [
            [
                'name'=>'男',
                'value'=>'0',
                "checked"=> true
            ],
            [
                'name'=>'女',
                'value'=>'1',
                "checked"=>false
            ]
        ];

        $select = [
            [
                'name'=>'今天',
                'value'=>'1',
                'selected'=>false,
            ],
            [
                'name'=>'明天',
                'value'=>'2',
                'selected'=>true,
            ],
            [
                'name'=>'明天',
                'value'=>'3',
                'selected'=>false,
            ]
        ];

        $checkbox = [
            [
                'name'=>'篮球',
                'value'=>'1',
                'checked'=>true,
            ],
            [
                'name'=>'足球',
                'value'=>'2',
                'checked'=>true,
            ],
            [
                'name'=>'乒乓球',
                'value'=>'3',
                'checked'=>false,
            ]
        ];

        $ret_json = TmplUtils::_init()
                                ->edit()
                                ->submint_url('http://xx')
                                ->head_content(array('标签1','http://xxx',false))
                                ->head_content(array('标签2','http://xxx2',true))
                                ->body_text(array('nick_name', '昵称', true), '测试昵称')
                                ->body_radio(array('sex', '性别', true),$sex)
                                ->body_select(array('day', '今天OR明天OR后天', true),$select)
                                ->body_checkbox(array('hby', '爱好', true), $checkbox)
                                ->body_file(array('head', '上传头像', false))
                                ->body_date(array('date', '日期', true), 1463626285)
                                ->body_email(array('email', '邮箱', true), 'zx@qq.com')
                                ->body_submit(array('add', '提交'))
                                ->done();

        return json_encode($ret_json);
    }

    public function add_json(){
        $sex = [
            [
                'name'=>'男',
                'value'=>'0',
                "checked"=> true
            ],
            [
                'name'=>'女',
                'value'=>'1',
                "checked"=>false
            ]
        ];

        $select = [
            [
                'name'=>'今天',
                'value'=>'1',
                'selected'=>false,
            ],
            [
                'name'=>'明天',
                'value'=>'2',
                'selected'=>false,
            ],
            [
                'name'=>'明天',
                'value'=>'3',
                'selected'=>false,
            ]
        ];

        $checkbox = [
            [
                'name'=>'篮球',
                'value'=>'1',
                'checked'=>false,
            ],
            [
                'name'=>'足球',
                'value'=>'2',
                'checked'=>false,
            ],
            [
                'name'=>'乒乓球',
                'value'=>'3',
                'checked'=>false,
            ]
        ];

        $ret_json = TmplUtils::_init()
                                ->add()
                                ->submint_url('http://xx')
                                ->head_content(array('标签1','http://xxx',false))
                                ->head_content(array('标签2','http://xxx2',true))
                                ->body_text(array('nick_name', '昵称', true))
                                ->body_radio(array('sex', '性别', true),$sex)
                                ->body_select(array('day', '今天OR明天OR后天', true),$select)
                                ->body_checkbox(array('hby', '爱好', true), $checkbox)
                                ->body_file(array('head', '上传头像', false))
                                ->body_date(array('date', '日期', true))
                                ->body_email(array('email', '邮箱', true))
                                ->body_submit(array('add', '提交'))
                                ->done();

        return json_encode($ret_json);
    }

    public function table_json(){
        $users = DB::table("Users")->get();

        $json_users = array();
        foreach ($users as $user){
            $tmp_arr = array();
            foreach ($user as $key => $value){
                $values = array();
                if($key == 'name' || $key == 'nick_name' || $key == 'tel' || $key == 'mail' || $key=='id'){
                    $values['type'] = 'string';
                    $values['value'] = $value;
                }else if($key == 'role_id'){
                    $values['type'] = 'select';
                    $values['value'] = $value;
                }else if($key == 'reg_time'){
                    $values['type'] = 'date';
                    $values['value'] = $value;
                }else if($key == 'head'){
                    $values['type'] = 'img';
                    $values['value'] = $value;
                }else if($key == 'password'){
                    $values['type'] = 'link';
                    $values['showName'] = $value;
                    $values['link'] = 'http://xxxxxx';
                }

                $tmp_arr[$key]=$values;

            }

            $tmp_arr['operation'] = [
                [
                    'type'=>'link',
                    'showName'=>'编辑',
                    'opType'=>'edit',
                    'url'=>'http://xxx'.$user['id']
                ],
                [
                    'type'=>'link',
                    'showName'=>'删除',
                    'opType'=>'del',
                    'url'=>'del'.$user['id']
                ]
            ];
            array_push($json_users, $tmp_arr);
        }

//        dump($json_users);

        $ret_json = TmplUtils::_init()
                                ->table()
                                ->submint_url('http://xx')
                                ->head_content(array('添加','/admin/index/add_user2',false))
                                ->head_content(array('更新','/admin/index/edit_user2',false))
                                ->head_content(array('首页','/admin/index/users',true))
                                ->table_operation(array('批量删除', 'alldel', 'http://xx1'))
                                ->table_operation(array('按ID排序', 'order', 'http://xx2'))
                                ->table_body_thead(array('id', '名称', '密码', '角色id', '注册时间', '头像', '昵称', '电话', '邮箱', '操作'))
                                ->table_body_tbody($json_users)
                                ->table_body_relation('role_id', 'select', array('1' =>'角色1', '3'=>'角色2', '6'=>'角色3'))
                                ->done();


//        dump($ret_json);

        return json_encode($ret_json);

//        dump($tmpl->done());
    }


    public function index(){
        // 超级管理员
        if(session('ADMIN_ID') == 1){
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
        }else{
            // 获取用户
            $user =DB::table('users')->where(array('id'=>session('ADMIN_ID')))->first();
            // 获取用户角色对应的rule
            $role_auth_tmp = DB::table('role_auth')->where(array('role_id'=>$user['role_id']))->get();

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
        }

        // 组装树型结构数组
        $role_auth = $this->get_tree_array(0, $role_auth);

//        dump($role_auth);
        $this->assign('show_rule', $role_auth);
        return $this->fetch('index');
    }

    // 修改密码
    public function password()
    {
        if(IS_GET){
            return $this->fetch('change_password');
        }

        if(IS_POST){
            $password = I('password');

            $ret = DB::table('users')->where(array('id'=>session('ADMIN_ID')))->update(array('password'=>$password));

            if($ret){
                return $this->success('修改成功', '/admin/index/password');
            }else{
                return $this->error('修改错误');
            }
        }
    }

    // 默认主页
    public function main()
    {
        return $this->fetch();
    }

    // 菜单模块
    public function menu(){
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

//        dump($role_auth);

        $this->assign('show_rule', $role_auth);
        return $this->fetch('menu');
    }

    // 修改 菜单
    public function menu_info(){
        if(IS_GET){
            $id = I('id');
            $rule = DB::table('rule')->where(array("id" => $id))->first();
            // 所有父级菜单
            $parent = DB::table('rule')->where(array("parentid" => 0))->get();

            $this->assign('rule', $rule);
            $this->assign('parent', $parent);
            return $this->fetch();
        }

        if(IS_POST){
            $parentid = I('parentid');
            $id = I('id');
            $name = I('name');
            $module = I('module');
            $controller = I('controller');
            $action = I('action');
            $show = I('show',1);

            $data['name'] = $name;
            $data['rule_name'] = "/$module/$controller/$action";
            if(isset($parentid)){
                $data['parentid'] = $parentid;
            }
            $data['show'] = $show;

            $ret = DB::table('rule')->where("id", $id)->update($data);

            if($ret){
                return $this->success('更新成功', '/admin/index/menu');
            }else{
//                return $this->error('更新错误');
            }
        }
    }

    // 删除 菜单
    public function menu_del(){
        $id = I('id');

        $rule = DB::table('rule')->where(array('id'=>$id))->first();
        $flag = false;
        if($rule['parentid'] == 0){

            $ret = DB::table('rule')->where(array('id'=>$id))->delete();
            if(!$ret){
                return $this->error('删除错误');
            }
            $ret2 = DB::table('rule')->where(array('parentid'=>$id))->delete();
            $flag = $ret2 ? true : false;
        }else{
            $ret = DB::table('rule')->where(array('id'=>$id))->delete();
            $flag = $ret ? true : false;
        }

        if($flag){
            return $this->success('删除成功', '/admin/index/menu');
        }else{
            return $this->error('删除错误');
        }
    }

    // 添加菜单
    public function add_menu(){
        if(IS_GET){
            // 获取所有的父级菜单
            $parent_rule = DB::table('rule')->where(array('parentid'=>0))->get();

            $this->assign('parent_rule', $parent_rule);
            return $this->fetch('add_menu');
        }

        if(IS_POST){
            $parentid = I('parentid');
            $name = I('name');
            $module = I('module');
            $controller = I('controller');
            $action = I('action');
            $show = I('show',1);

            if ($parentid == 0){
                $show = 1;
            }

            $data['name'] = $name;
            $data['rule_name'] = "/$module/$controller/$action";
            $data['parentid'] = $parentid;
            $data['show'] = $show;



            $rule = DB::table('rule')->where($data)->first();

            if($rule){
                return $this->error('该数据已存在');
            }

            $ret = DB::table('rule')->insert($data);

            if($ret){
                return $this->success('添加成功', 'add_menu');
            }else{
                return $this->error('添加错误');
            }
        }
    }

    // 角色管理
    public function rbac(){
        // 所有角色
        $role = DB::table('role')->get();

        $this->assign('role', $role);
        return $this->fetch('rbac');
    }

    // 角色修改
    public function role_info(){
        if(IS_GET){
            $id = I('id');
            $user = DB::table('role')->where(array('id'=>$id))->first();

            $this->assign('id', $id);
            $this->assign('user', $user);
            return $this->fetch();
        }

        if(IS_POST){
            $id = I('id');
            $name = I('name');
            $state = I('status');

            $ret = DB::table('role')->where(array('id'=>$id))->update(array('name'=>$name, 'state'=>$state));

            if($ret){
                return $this->success('更新成功', '/admin/index/rbac');
            }else{
                return $this->error('更新错误');
            }
        }
    }

    // 角色删除
    public function role_del(){
        $id = I('id');

        $ret = DB::table('role')->where(array('id'=>$id))->delete();

        if($ret){
            return $this->success('删除成功', '/public/razor/index/rbac');
        }else{
            return $this->error('删除错误');
        }
    }

    // 添加角色
    public function add_role(){
        if(IS_GET){
            return $this->fetch('add_role');
        }

        if(IS_POST){
            $name = I('name');
            $state = I('status');

            $data['name'] = $name;
            $data['state'] = $state;
            $ret = DB::table('role')->insert($data);

            if($ret){
                return $this->success('添加成功','rbac');
            }else{
                return $this->error('添加失败');
            }
        }
    }

    // 权限设置
    public function role_auth(){
        if(IS_GET){
            $id = I('id');
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

            $this->assign('id', $id);
            $this->assign('role', $role_auth);
            $this->assign('user_rule', $user_rule_arr);
            return $this->fetch('role_auth');
        }

        if(IS_POST){
            $auth = I('auth');
            $id = I('id');
            $auth_arr = explode(",",$auth);

            // 更新权限
            DB::table('role_auth')->where(array('role_id'=>$id))->delete();
            foreach($auth_arr as $k => $v){
                if($v != ''){
                    $data['role_id'] = $id;
                    $data['rule_id'] = $v;

                    $ret = DB::table('role_auth')->insert($data);
                    if(!$ret){
                       echo 0;
                       return;
                   }
                }
            }

            echo 1;
        }
    }

    // 管理员
    public function user(){
        // 所有用户(除开超级管理员)
        $users = DB::table('users')->where('id', '<>', 1)->get();

        $this->assign('users', $users);
        return $this->fetch('users');
    }

    // 管理员 修改
    public function user_info(){
        if(IS_GET){
            $id = I('id');
            $user = DB::table('users')->where(array('id' => $id))->first();

            // 获取所有开启的角色
            $role = DB::table('role')->where(array('state'=>1))->get();

            $this->assign('role', $role);
            $this->assign('user', $user);
            return $this->fetch();
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

            $ret = DB::table('users')->where('id', $id)->update($data);

            if($ret){
                return $this->success('更新成功','/admin/index/user');
            }else{
                return $this->error('更新失败');
            }
        }
    }

    // 管理员 删除
    public function del_user(){
        $id = I('id');

        $ret = DB::table('users')->where('id', $id)->delete();

        if($ret){
            return $this->success('删除成功','/admin/index/user');
        }else{
            return $this->error('删除失败');
        }
    }

    // 添加 管理员
    public function add_user(){
        if(IS_GET){
            $role = DB::table('role')->get();

            $this->assign('role', $role);
            return $this->fetch();
        }

        if(IS_POST){
            $name = I('name');
            $password = I('password');
            $role = I('role');

            $data['name'] = $name;
            $data['password'] = $password;
            $data['role_id'] = $role;
            $data['reg_time'] = time();

            $ret = DB::table('users')->insert($data);
            if($ret){
                return $this->success('添加成功','/admin/index/user');
            }else{
                return $this->error('添加失败');
            }
        }
    }

    // 用户信息
    public function user_rinfo(){
        if(IS_GET){
            return $this->fetch();
        }

        if(IS_POST){
            $id = session('ADMIN_ID');
            $name = I('name');
            $tel= I('tel');
            $mail = I('mail');

            $data['nick_name'] = $name;
            $data['tel'] = $tel;
            $data['mail'] = $mail;

            $ret = DB::table('users')->where('id', $id)->update($data);

            if($ret){
                return $this->success('更新成功','/admin/index/user_rinfo');
            }else{
                return $this->error('更新失败');
            }
        }
    }

    // 前台菜单
    public function menu_front(){
        // 所有权限
        $rule_front = DB::table('rule_front')->get();
        $this->assign('rule_front', $rule_front);

        return $this->fetch();
    }

    // 添加前台菜单
    public function add_menu_front(){
        if (IS_GET){
            return $this->fetch();
        }

        if(IS_POST){
            $name = I('name');
            $rule_name = I('rule_name');
            $description = I('description');
            $path = $this->_upload_file('cover');
            $show = I('show');

            $params['name'] = $name;
            $params['rule_name'] = $rule_name;
            $params['cover'] = $path;
            $params['show'] = $show;
            $params['description'] = $description;

            $ret = DB::table('rule_front')->insertGetId($params);

            if($ret){
                return $this->success('添加成功','/admin/index/menu_front');
            }else{
                return $this->error('添加失败');
            }
        }
    }

    // 编辑前台菜单
    public function edit_menu_front(){
        $id = I('id');

        if(IS_GET){
            $menu_front = DB::table('rule_front')->where(array('id'=>$id))->first();
            $this->assign('rule_front', $menu_front);

            return $this->fetch();
        }

        if(IS_POST){
            $name = I('name');
            $rule_name = I('rule_name');
            $description = I('description');

            if(!empty($_FILES['cover']['name'])){
                $path = $this->_upload_file('cover');
                $params['cover'] = $path;
            }

            $show = I('show');
            $params['name'] = $name;
            $params['rule_name'] = $rule_name;

            $params['show'] = $show;
            $params['description'] = $description;

            $ret = DB::table('rule_front')->where('id', $id)->update($params);

            if($ret){
                return $this->success('更新成功','/admin/index/menu_front');
            }else{
                return $this->error('更新失败');
            }
        }
    }

    // 删除前台菜单
    public function del_menu_front(){
        $id = I('id');
        $ret = DB::table('rule_front')->where('id', $id)->delete();

        if($ret){
            return $this->success('删除成功','/admin/index/menu_front');
        }else{
            return $this->error('删除失败');
        }
    }

    // 角色对应前台的权限
    public function role_auth_front(){
        $id = I('id');

        if(IS_GET){
            // 获取角色id
            $user = DB::table('users')->where('id', $id)->first();
            // 角色id对应的权限
            $auths = DB::table('role_auth_front')->where('role_id', $user['role_id'])->get();
            // 所有权限
            $all_auth = DB::table('rule_front')->get();

            // 权限对应的id数组 用来显示那些权限被选中
            $auth_ids = array();
            foreach ($auths as $auth){
                $tmp = array();
                foreach ($auth as $key=>$value){
                    if ($key == 'rule_front_id'){
                        $rule_front = DB::table('rule_front')->where('id', $value)->first();
                        array_push($auth_ids, $rule_front['id']);
                    }
                }
            }

            $this->assign('id', $id);
            $this->assign('auth_ids', $auth_ids);
            $this->assign('all_auth', $all_auth);
            return $this->fetch();
        }

        if(IS_POST){
            $auth = I('auth');
            $auth_arr = explode(",",$auth);

            // 更新权限
            DB::table('role_auth_front')->where(array('role_id'=>$id))->delete();
            foreach($auth_arr as $k => $v){
                if($v != ''){
                    $data['role_id'] = $id;
                    $data['rule_front_id'] = $v;

                    $ret = DB::table('role_auth_front')->insert($data);
                    if(!$ret){
                        echo 0;
                        return;
                    }
                }
            }

            echo 1;
        }
    }



    //---------------------------------------- 辅助函数(tree) ----------------------------------------
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

    // 上传图片
    public function _upload_file($name)
    {
        if (!empty($_FILES[$name])) {
            $tempFile = $_FILES[$name]['tmp_name'];
//            $uploadDir = ROOT_PATH.'public/static/bbdata/cover/';
            $d = '.';
            $uploadDir = '/static/bbdata/cover/';
            $r_time = strval(time());
            $targetFile = $d . $uploadDir . $r_time . $_FILES[$name]['name'];
        }

        move_uploaded_file($tempFile, $targetFile);

        return $uploadDir . $r_time . $_FILES[$name]['name'];;
    }
}
