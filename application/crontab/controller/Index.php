<?php
namespace app\crontab\controller;

use bb\Controller;

use util\CronUtils;


class Index extends Controller
{

    // 调度任务
    public function task(){
        CronUtils::cron($_REQUEST['id']);
    }

    public function database_backup(){
        echo 'success';
//        $myfile = fopen("/Applications/XAMPP/xamppfiles/htdocs/web/bbframework/public/static/log.log", "a") or die("Unable to open file!");
        $myfile = fopen("/home/www/bbframework/public/static/log.log", "a") or die("Unable to open file!");
        $txt = date("Y:m:d H:i:s", time())."\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    public function index()
    {
        $params = array(
            'min' => '*',
            'hour' => '*',
            'day' => '*',
            'mon' => '*',
            'week' => '*',
            'module_name' => 'index',
            'controller_name' => 'index',
            'action_name' => 'database_backup',
            'paramas' => '',
            'name' => '打印log299999',
            'type' => 'url',
            'description' => '描述299999',
        );

        $params2 = array(
            'module_name' => 'index222',
            'controller_name' => 'index333',
//            'action_name' => 'database_backup444',
//            'paramas'=>'555',
            'rule' => '* 1 1 * *'
        );

//        CronUtils::min($params, 10);
//        CronUtils::huor($params);
//        CronUtils::day($params);
//        CronUtils::mon($params);
//        CronUtils::week($params);
//
//        // 更新
//        CronUtils::update_cron($params2, 29);
//        // 任务开关
//        CronUtils::switch_cron(0, 11);
//        // 任务删除
//        echo CronUtils::del_cron(8);

        $params = array(
            'min' => '*',
            'hour' => '*',
            'day' => '*',
            'mon' => '*',
            'week' => '*',
            'module_name' => 'index',
            'controller_name' => 'index',
            'action_name' => 'database_backup',
            'paramas'=>'',
            'name' => '打印log',
            'type' => 'url',
            'description' => '描述',
        );

    }
}
