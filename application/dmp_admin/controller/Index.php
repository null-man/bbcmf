<?php
namespace app\dmp_admin\controller;
use app\dmp_admin\controller\DmpAdminController;
use app\dmp_admin\model\Count;
use app\dmp_admin\model\CountType;
use app\dmp_admin\model\Event;
use app\dmp_admin\model\EventArgv;
use app\dmp_admin\model\Handler;
use app\dmp_admin\model\Task;
use app\dmp_admin\model\TaskConfig;
use bb\DB;
use util\TmplUtils;
use bb\File;
use bb\Str;


header("Content-type: text/html;charset=utf-8");
class Index extends DmpAdminController{

    public function __construct(){
//        if(in_array(ACTION_NAME,array('menu_info', 'test'))){
            parent::_init();
//        }else{
            parent::__construct();
//        }
    }

    // ueditor
    public function ueditor_json(){
//        json_decode($ret);
    }


    // --------------------------- 示例 ---------------------------

    public function test(){
            $f = new File();
            $f->name('/Applications/XAMPP/xamppfiles/htdocs/web/laravel/learnlaravel5/resources/views/auth');

//        $event = Event::find(1)->hasOne('app\dmp_admin\model\Task', 'id', 'cron_id')->getResults()->toArray();
//
//        // 获取道外键的所有信息
//        dump($event);
    }

    // 插入数据
    public function insert()
    {
        $data = array(
            'name'=>I('name', ''),
            'show_name' => I('show_name', ''),
            'cron_id' => I('cron_id')
//              外键
        );

//        $this->_other_operation = array(
//            $_REQUEST['cron_id'], 'event_task', 'event_id', 'task_id'
//        );

        return $this->add($data, '/dmp_admin/index/users');

    }

    // 更新数据
    public function update(){
        $data = array(
            'id'=>I('id'),
            'name'=>I('name', ''),
            'show_name' => I('show_name', ''),
            'cron_id' => I('cron_id')
        );

        $this->_other_operation = array(
            $_REQUEST['test'], 'event_task', 'event_id', 'task_id'
        );

        return $this->edit($data, '/dmp_admin/index/users');
    }

    // 删除数据
    public function delete(){
        $this->_other_operation = array(
            'event_task', 'event_id',
        );

        $this->del(I('id'));
    }


    public function users(){
        return $this->render(array('url'=>'/dmp_admin/index/table_json'));
    }

    public function add_user2(){
        return $this->add(array('url'=>'/dmp_admin/index/add_json'));
    }

    public function edit_user2(){
        return $this->edit(array('url'=>'/dmp_admin/index/edit_json', 'id'=>I('id')));
    }

    public function edit_json(){
        $this->set_id(I('id'));

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/add_user2',false),
                array('首页','/dmp_admin/index/users',false)
            )
        );
        return $this->build_json('edit');
    }

    public function add_json(){
        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/add_user2',true),
                array('首页','/dmp_admin/index/users',false)
            )
        );
        return $this->build_json('add');
    }

    public function table_json(){
        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/add_user2',false),
                array('首页','/dmp_admin/index/users',true)
            )
        );
        return $this->build_json('table');
    }

    // ---------------------------------------------------------------





    // ----------------------- event ------------------------------

    // 事件主页
    public function event_index(){
        $handler_arr = isset($_REQUEST['handler']) ? $_REQUEST['handler'] :  '';

        return $this->render(
            '/dmp_admin/index/event_table_data',
            array(
                'page'      => I('page', ''),
                'name'      => I('name', ''),
                'handler'   => $handler_arr
            )
        );
    }

    public function event_table_data(){
        // 设置model
        $this->set_model(new Event());

        // 设置标签栏
        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/event_add',false),
                array('首页','/dmp_admin/index/event_index',true)
            )
        );

        // 设置过滤
        $this->set_filter(
            array(
                'name'      => I('name', ''),
                'handler'   => array(I('handler', ''))
            )
        );

        // 设置分页 当前页面, 每页显示数量
        $this->set_page(I('page', 1), 3);
        // 设置分页的url
        $this->set_page_url('/dmp_admin/index/event_index.html?xxxx=1&bb=1');

        return $this->build_json('table');
    }

    // 事件添加
    public function event_add(){
        if (IS_GET){
            return $this->add(
                array('url'=>'/dmp_admin/index/event_add_data')
            );
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new Event());

//            dump($this->date_build('show_name', true));
//            return;

            $data = array(
                'name'=>I('name', ''),
                'show_name' => I('show_name', ''),
//                'cron_id' => I('cron_id')
            );

            if (isset($_REQUEST['handler'])){
                $this->_other_operation = array(
                    $_REQUEST['handler'], 'event_handler', 'event_id', 'handler_id', true
                );
            }

            $onetoone_data = array(
                'url' => I('cron_id_url'),
                'rule' => I('cron_id_rule'),
                'name' => I('cron_id_name'),
                'is_on' => I('cron_id_is_on'),
                'type' => 'url'
            );

            $this->_onetoone = array(
                $onetoone_data,
                'app\dmp_admin\model\Task',
                'cron_id'
            );

            return $this->add($data, '/dmp_admin/index/event_index');
        }
    }

    public function event_add_data(){
        // 设置model
        $this->set_model(new Event());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/event_add', true),
                array('首页','/dmp_admin/index/event_index', false)
            )
        );

        return $this->build_json('add');
    }

    // 事件编辑
    public function event_edit(){

        if(IS_GET){
            return $this->edit(
                array(
                    'url' => '/dmp_admin/index/event_edit_data',
                    'params' =>array(
                        'id' => I('id')
                    )
                )
            );
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new Event());

            $data = array(
                'id'=>I('id'),
                'name'=>I('name', ''),
                'show_name' => I('show_name', ''),
            );

            $handler = isset($_REQUEST['handler']) ?  $_REQUEST['handler'] :  "";

            $this->_other_operation = array(
                $handler, 'event_handler', 'event_id', 'handler_id', true
            );

            $onetoone_data = array(
                'url'=>I('cron_id_url'),
                'rule'=>I('cron_id_rule'),
                'name'=>I('cron_id_name'),
                'is_on'=>I('cron_id_is_on'),
            );

            $this->_onetoone = array(
                $onetoone_data,
                'app\dmp_admin\model\Task',
                'cron_id'
            );

            return $this->edit($data, '/dmp_admin/index/event_index');
        }
    }

    public function event_edit_data(){
        // 设置model
        $this->set_model(new Event());

        $this->set_id(I('id'));

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/event_add',false),
                array('首页','/dmp_admin/index/event_index',false)
            )
        );
        return $this->build_json('edit');
    }

    // 删除
    public function event_del(){
        // 设置model
        $this->set_model(new Event());

        $status = $this->del(I('id'));
        $info = $status == 1 ? '操作成功' : '操作失败';

        $ret = [
            'status' => $status,
            'info' => $info
        ];

        return json_encode($ret);
    }

    public function event_list_del(){
        // 设置model
        $this->set_model(new Event());
        $this->list_del($_REQUEST['id']);
    }

    // ---------------------------------------------------------------

    


    // ----------------------- event_argv -----------------------------

    // 事件主页
    public function event_argv_index(){
        return $this->render(
            '/dmp_admin/index/event_argv_table_data',
            array(
                'page'      => I('page', ''),
                'event_id'  => I('event_id', ''),
                'name'      => I('name', ''),
                'type'      => I('type', '')
            )
        );
    }

    public function event_argv_table_data(){
        // 设置model
        $this->set_model(new EventArgv());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/event_argv_add',false),
                array('首页','/dmp_admin/index/event_argv_index',true)
            )
        );

        // 设置过滤
        $this->set_filter(
            array(
                'event_id'  => I('event_id', ''),
                'name'      => I('name', ''),
                'type'      => I('type', '')
            )
        );

        // 设置分页 当前页面, 每页显示数量
        $this->set_page(I('page', 1), 3);
        // 设置分页的url
        $this->set_page_url('/dmp_admin/index/event_argv_index');

        return $this->build_json('table');
    }

    // 事件添加
    public function event_argv_add(){
        if (IS_GET){
            return $this->add(array('url'=>'/dmp_admin/index/event_argv_add_data'));
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new EventArgv());

            $data = array(
                'event_id'=>I('event_id', ''),
                'name' => I('name', ''),
                'type' => I('type')
            );


            return $this->add($data, '/dmp_admin/index/event_argv_index');
        }
    }

    public function event_argv_add_data(){
        // 设置model
        $this->set_model(new EventArgv());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/event_argv_add', true),
                array('首页','/dmp_admin/index/event_argv_index', false)
            )
        );

        return $this->build_json('add');
    }

    // 事件编辑
    public function event_argv_edit(){
        if(IS_GET){
            return $this->edit(
                array(
                    'url' => '/dmp_admin/index/event_argv_edit_data',
                    'params' =>array(
                        'id' => I('id')
                    )
                )
            );
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new EventArgv());

            $data = array(
                'id' => I('id'),
                'event_id'=>I('event_id', ''),
                'name' => I('name', ''),
                'type' => I('type')
            );

            return $this->edit($data, '/dmp_admin/index/event_argv_index');
        }
    }

    public function event_argv_edit_data(){
        // 设置model
        $this->set_model(new EventArgv());

        $this->set_id(I('id'));

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/event_argv_add',false),
                array('首页','/dmp_admin/index/event_argv_index',false)
            )
        );
        return $this->build_json('edit');
    }

    // 删除
    public function event_argv_del(){
        // 设置model
        $this->set_model(new EventArgv());

        $status = $this->del(I('id'));
        $info = $status == 1 ? '操作成功' : '操作失败';

        $ret = [
            'status' => $status,
            'info' => $info
        ];
        
        return json_encode($ret);
    }

    // -----------------------------------------------------------------



    // ----------------------- handler ------------------------------
    public function handler_index(){
        return $this->render(
            '/dmp_admin/index/handler_table_data',
            array(
                'page'      => I('page', ''),
                'name'      => I('name', '')
            )
        );
    }

    public function handler_table_data(){
        // 设置model
        $this->set_model(new \app\dmp_admin\model\Handler());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/handler_add',false),
                array('首页','/dmp_admin/index/handler_index',true)
            )
        );

        // 设置过滤
        $this->set_filter(
            array(
                'name'      => I('name', ''),
            )
        );

        // 设置分页 当前页面, 每页显示数量
        $this->set_page(I('page', 1), 3);
        // 设置分页的url
        $this->set_page_url('/dmp_admin/index/handler_index');

        return $this->build_json('table');
    }

    // 事件添加
    public function handler_add(){
        if (IS_GET){
            return $this->add(array('url'=>'/dmp_admin/index/handler_add_data'));
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new Handler());

            $data = array(
                'name' => I('name', ''),
                'show_name' => I('show_name')
            );
            return $this->add($data, '/dmp_admin/index/handler_index');
        }
    }

    public function handler_add_data(){
        // 设置model
        $this->set_model(new handler());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/handler_add', true),
                array('首页','/dmp_admin/index/handler_index', false)
            )
        );

        return $this->build_json('add');
    }

    // 事件编辑
    public function handler_edit(){
        if(IS_GET){
            return $this->edit(
                array(
                    'url'=>'/dmp_admin/index/handler_edit_data',
                    'params' =>array(
                        'id' => I('id')
                    )
                )
            );
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new Handler());

            $data = array(
                'id' => I('id'),
                'name' => I('name', ''),
                'show_name' => I('show_name')
            );

            return $this->edit($data, '/dmp_admin/index/handler_index');
        }
    }

    public function handler_edit_data(){
        // 设置model
        $this->set_model(new Handler());

        $this->set_id(I('id'));

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/handler_add',false),
                array('首页','/dmp_admin/index/handler_index',false)
            )
        );
        return $this->build_json('edit');
    }

    // 删除
    public function handler_del(){
        // 设置model
        $this->set_model(new handler());

        $this->del(I('id'));
    }

    // -----------------------------------------------------------------

    // ----------------------- count -----------------------------

    // 事件主页
    public function count_index(){
        return $this->render('/dmp_admin/index/count_table_data');
    }

    public function count_table_data(){
        // 设置model
        $this->set_model(new Count());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/count_add',false),
                array('首页','/dmp_admin/index/count_index',true)
            )
        );

        return $this->build_json('table');
    }

    // 事件添加
    public function count_add(){
        if (IS_GET){
            return $this->add(array('url'=>'/dmp_admin/index/count_add_data'));
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new Count());

            $data = array(
                'event_id' => I('event_id'),
                'count_type_id' => I('count_type_id')
            );
            return $this->add($data, '/dmp_admin/index/count_index');
        }
    }

    public function count_add_data(){
        // 设置model
        $this->set_model(new Count());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/count_add', true),
                array('首页','/dmp_admin/index/count_index', false)
            )
        );

        return $this->build_json('add');
    }

    // 事件编辑
    public function count_edit(){
        if(IS_GET){
            return $this->edit(
                array(
                    'url'=>'/dmp_admin/index/count_edit_data',
                    'params' =>array(
                        'id' => I('id')
                    )
                )
            );
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new Count());

            $data = array(
                'id' => I('id'),
                'event_id' => I('event_id'),
                'count_type_id' => I('count_type_id')
            );

            return $this->edit($data, '/dmp_admin/index/count_index');
        }
    }

    public function count_edit_data(){
        // 设置model
        $this->set_model(new Count());

        $this->set_id(I('id'));

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/count_add',false),
                array('首页','/dmp_admin/index/count_index',false)
            )
        );
        return $this->build_json('edit');
    }

    // 删除
    public function count_del(){
        // 设置model
        $this->set_model(new Count());

        $this->del(I('id'));
    }

    // -----------------------------------------------------------------




    // ----------------------- count_type -----------------------------

    // 事件主页
    public function count_type_index(){
        return $this->render('/dmp_admin/index/count_type_table_data');
    }

    public function count_type_table_data(){
        // 设置model
        $this->set_model(new CountType());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/count_type_add',false),
                array('首页','/dmp_admin/index/count_type_index',true)
            )
        );

        return $this->build_json('table');
    }

    // 事件添加
    public function count_type_add(){
        if (IS_GET){
            return $this->add(array('url'=>'/dmp_admin/index/count_type_add_data'));
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new CountType());

            $data = array(
                'name' => I('name', ''),
                'show_name' => I('show_name', '')
            );
            return $this->add($data, '/dmp_admin/index/count_type_index');
        }
    }

    public function count_type_add_data(){
        // 设置model
        $this->set_model(new CountType());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/count_type_add', true),
                array('首页','/dmp_admin/index/count_type_index', false)
            )
        );

        return $this->build_json('add');
    }

    // 事件编辑
    public function count_type_edit(){
        if(IS_GET){
            return $this->edit(
                array(
                    'url'=>'/dmp_admin/index/count_type_edit_data',
                    'params' =>array(
                        'id' => I('id')
                    )
                )
            );
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new CountType());

            $data = array(
                'id' => I('id'),
                'name' => I('name', ''),
                'show_name' => I('show_name', '')
            );

            return $this->edit($data, '/dmp_admin/index/count_type_index');
        }
    }

    public function count_type_edit_data(){
        // 设置model
        $this->set_model(new CountType());

        $this->set_id(I('id'));

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/count_type_add',false),
                array('首页','/dmp_admin/index/count_type_index',false)
            )
        );
        return $this->build_json('edit');
    }

    // 删除
    public function count_type_del(){
        // 设置model
        $this->set_model(new CountType());

        $this->del(I('id'));
    }

    // -----------------------------------------------------------------




    // --------------------------------

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
                return $this->success('修改成功', '/dmp_admin/index/password');
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
                return $this->success('更新成功', '/dmp_admin/index/menu');
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
            return $this->success('删除成功', '/dmp_admin/index/menu');
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

            if ($parentid == -1){
                $show = 1;
                $parentid = 0;
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
                return $this->success('更新成功', '/dmp_admin/index/rbac');
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
            return $this->success('删除成功', '/dmp_admin/index/rbac');
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
                return $this->success('更新成功','/dmp_admin/index/user');
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
            return $this->success('删除成功','/dmp_admin/index/user');
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
                return $this->success('添加成功','/dmp_admin/index/user');
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
                return $this->success('更新成功','/dmp_admin/index/user_rinfo');
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
                return $this->success('添加成功','/dmp_admin/index/menu_front');
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
                return $this->success('更新成功','/dmp_admin/index/menu_front');
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
            return $this->success('删除成功','/dmp_admin/index/menu_front');
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

    
    // ----------------------- task_config -----------------------------

    // 事件主页
    public function task_config_index(){
        return $this->render('/dmp_admin/index/task_config_table_data');
    }

    public function task_config_table_data(){
        // 设置model
        $this->set_model(new TaskConfig());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/task_config_add',false),
                array('首页','/dmp_admin/index/task_config_index',true)
            )
        );

        return $this->build_json('table');
    }

    // 事件添加
    public function task_config_add(){
        if (IS_GET){ 
            return $this->add(array('url'=>'/dmp_admin/index/task_config_add_data'));
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new TaskConfig());

            $data = array(
                'name' => I('name', ''),
                'config' => I('config', ''),
                'description' => I('description', '')
            );
            return $this->add($data, '/dmp_admin/index/task_config_index');
        }
    }

    public function task_config_add_data(){
        // 设置model
        $this->set_model(new TaskConfig());

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/task_config_add', true),
                array('首页','/dmp_admin/index/task_config_index', false)
            )
        );

        return $this->build_json('add');
    }

    // 事件编辑
    public function task_config_edit(){
        if(IS_GET){
            return $this->edit(array('url'=>'/dmp_admin/index/task_config_edit_data', 'id'=>I('id')));
        }

        if(IS_POST){
            // 设置model
            $this->set_model(new TaskConfig());

            $data = array(
                'id' => I('id'),
                'name' => I('name', ''),
                'config' => I('config', ''),
                'description' => I('description', '')
            );

            return $this->edit($data, '/dmp_admin/index/task_config_index');
        }
    }

    public function task_config_edit_data(){
        // 设置model
        $this->set_model(new TaskConfig());

        $this->set_id(I('id'));

        $this->set_navigations(
            array(
                array('添加','/dmp_admin/index/task_config_add',false),
                array('首页','/dmp_admin/index/task_config_index',false)
            )
        );
        return $this->build_json('edit');
    }

    // 删除
    public function task_config_del(){
        // 设置model
        $this->set_model(new TaskConfig());

        $this->del(I('id'));
    }

    // -----------------------------------------------------------------



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
