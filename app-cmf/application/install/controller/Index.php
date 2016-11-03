<?php
namespace app\install\controller;
use bb\Controller;
use think\Db;
/**
 * 首页
 */
class Index extends Controller {

    function _initialize(){
        if(file_exists_case("./data/install.lock")){
//            $this->redirect("http://" . $_SERVER['HTTP_HOST'] . "/index.php/admin?_app_=cmf");
        }
    }

    public function index(){
        return view();
    }

    public function step2(){
        if (file_exists_case('data/conf/config.php')) {
            @unlink('data/conf/config.php');
        }

        $data               = [];
        $data['phpversion'] = @ phpversion();
        $data['os']         = PHP_OS;
        $tmp                = function_exists('gd_info') ? gd_info() : [];
        $server             = $_SERVER["SERVER_SOFTWARE"];
        $host               = (empty($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_HOST"] : $_SERVER["SERVER_ADDR"]);
        $name               = $_SERVER["SERVER_NAME"];
        $max_execution_time = ini_get('max_execution_time');
        $allow_reference    = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $allow_url_fopen    = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $safe_mode          = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');

        $err = 0;
        if (empty($tmp['GD Version'])) {
            $gd = '<font color=red>[×]Off</font>';
            $err++;
        } else {
            $gd = '<font color=green>[√]On</font> ' . $tmp['GD Version'];
        }

        if (class_exists('pdo')) {
            $data['pdo'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['pdo'] = '<i class="fa fa-remove error"></i> 未开启';
            $err++;
        }

        if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['pdo_mysql'] = '<i class="fa fa-remove error"></i> 未开启';
            $err++;
        }

        if (extension_loaded('curl')) {
            $data['curl'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['curl'] = '<i class="fa fa-remove error"></i> 未开启';
            $err++;
        }

        if (extension_loaded('gd')) {
            $data['gd'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['gd'] = '<i class="fa fa-remove error"></i> 未开启';
            if (function_exists('imagettftext')) {
                $data['gd'] .= '<br><i class="fa fa-remove error"></i> FreeType Support未开启';
            }
            $err++;
        }

        if (ini_get('file_uploads')) {
            $data['upload_size'] = '<i class="fa fa-check correct"></i> ' . ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = '<i class="fa fa-remove error"></i> 禁止上传';
        }

        if (function_exists('session_start')) {
            $data['session'] = '<i class="fa fa-check correct"></i> 支持';
        } else {
            $data['session'] = '<i class="fa fa-remove error"></i> 不支持';
            $err++;
        }

        $folders = [
            'data',
            'data/conf',
            'data/runtime',
            'data/runtime/Cache',
            'data/runtime/Data',
            'data/runtime/Logs',
            'data/runtime/Temp',
            'data/upload',
        ];

        $new_folders = [];
        foreach ($folders as $dir) {
            $Testdir = "./" . $dir;
            sp_dir_create($Testdir);
            if (sp_testwrite($Testdir)) {
                $new_folders[$dir]['w'] = true;
            } else {
                $new_folders[$dir]['w'] = false;
                $err++;
            }
            if (is_readable($Testdir)) {
                $new_folders[$dir]['r'] = true;
            } else {
                $new_folders[$dir]['r'] = false;
                $err++;
            }
        }
        $data['folders'] = $new_folders;

        $this->assign($data);
        return view();
    }


    public function step3(){
       return view();
    }

    public function step4(){
        if(IS_POST){
            // ##创建数据库
            // 获取请求数据
            $dbconfig['type']        = "mysql";
            $dbconfig['hostname']    = I('post.dbhost');
            $dbconfig['username']    = I('post.dbuser');
            $dbconfig['password']    = I('post.dbpw');
            $dbconfig['hostport']    = I('post.dbport');

            $db     = Db::connect($dbconfig);
            $dbname = strtolower(I('post.dbname'));

            // 创建数据库
            $sql    = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
            $db->query($sql) || $this->error($db->getError());

            session('step', 4);

            echo view();

            //创建数据表
            $dbconfig['database']   = $dbname;
            $dbconfig['prefix']     = trim(I('post.dbprefix'));
            $db                     = Db::connect($dbconfig);

            $table_prefix           = I("post.dbprefix");
            sp_execute_sql($db, "bbcmf.sql", $table_prefix);

            //更新配置信息
            sp_update_site_configs($db, $table_prefix);

            //创建管理员
            sp_create_admin_account($db, $table_prefix);

            //生成网站配置文件
            sp_create_config($dbconfig);

            echo "<script type=\"text/javascript\">setTimeout(href('/index.php/install/Index/step5.html?_app_=cmf&g=install'), 2000 );</script> ";
        }else{
            exit;
        }
    }

    public function step5(){
        if (session('step') == 4) {
            @touch('./data/install.lock');
            return view("step5");
        }else{
            return $this->error("(∩•̀ω•́)⊃-*⋆  非法安装！");
        }
    }

    public function testdbpwd(){
        if(IS_POST){
            $dbconfig               = $_POST;
            $dbconfig['type']       = "mysql";
            $db                     = Db::connect($dbconfig);

            try{
                $db->query("show databases;");
            }catch (\Exception $e){
                die("");
            }
            exit("1");
        }else{
            exit("need post!");
        }

    }

}

