<?php
namespace app\admin\controller;

use think\Config;
/**
 * Class Menu 菜单类
 * @package app\admin\controller
 */
class Menu extends Common {

    // 表名
    protected $table            = 'bbcmf_rule';
    // 添加 页面
    protected $view_add         = 'add_menu';
    // 编辑 页面
    protected $view_edit        = 'edit_menu';
    // 主页 页面
    protected $view_index       = 'menu_manage';



    function __construct(){
        parent::__construct();
        $this->noCheckAccess(['menuTree', 'menuViewAdd', 'menuViewEdit', 'exportMenu', 'importMenu']);
    }

    // ----------------------------------
    // 所有菜单
    // ----------------------------------
    public function menuTree(){
        // ###组装树形结构菜单
        // 获取树形结构名称
        $name_arr = [];
        foreach (explode('|', $this->treeStyleData($this->table, "\$id - \$spacer \$name |")) as $_k => $_v) {
            if ($_v) {
                $k_v_arr = explode(' - ', $_v);
                $name_arr[$k_v_arr[0]] = $k_v_arr[1];
            }
        }

        // 重新组装名称
        $show_data = [];
        foreach ($this->all('bbcmf_rule') as $key => $rule) {
            foreach ($rule as $k => $v) {
                $tmp[$k] = ($k == 'name') ? $name_arr[$rule['id']] : $v; 
            }
            array_push($show_data, $tmp);
        }

        return json_encode(['data' => $show_data]);
    }



    // ----------------------------------
    // 菜单添加
    // ----------------------------------
    public function menuViewAdd(){
        if (IS_GET) {
            $this->assign("select_categorys", $this->treeStyleData($this->table, "<option value='\$id'>\$spacer \$name</option>"));
            return $this->fetch($this->view_add);    
        }

        if (IS_POST) {
            // ###获取数据
            // 菜单名
            $menu_name  = I('group', '');
            // 模块 
            $moudle     = I('app', '');
            // 控制器
            $controller = I('control', '');
            // 方法
            $method     = I('fn', '');
            // 参数
            $params     = I('param', '');
            //状态
            $menu_state = I('menu_state', 1);
            // 上级
            $parent     = I('parentid', 0);

            // 参数处理
            $url_arr = [$moudle, $controller, $method];

            !empty($params) && array_push($url_arr, $params);

            // ###数据组装
            // 菜单名
            $data['name']       = $menu_name;
            // 页面路径
            $data['src']        = ($parent == 0) ? '/default/default/default' : '/' . join("/", $url_arr);
            // 是否显示
            $data['show']       = $menu_state;
            // 上级
            $data['parentid']   = $parent;
            // 图标
            $data['icon']       = '';

            return $this->resultResponseAjax($this->insert('bbcmf_rule', $data), '添加菜单成功', '添加菜单失败');
        }
    }




    // ----------------------------------
    // 角色 编辑
    // ----------------------------------
    public function menuViewEdit(){
        if (IS_GET) {
            $this->assign("select_categorys", $this->treeStyleData($this->table, "<option value='\$id' \$selected>\$spacer \$name</option>"));
            return $this->fetch($this->view_edit);    
        }

        if (IS_POST) {
            // ###获取数据
            // id
            $id         = I('id', 0);
            // 菜单名
            $menu_name  = I('group', '');
            // 模块 
            $moudle     = I('app', '');
            // 控制器
            $controller = I('control', '');
            // 方法
            $method     = I('fn', '');
            // 参数
            $params     = I('param', '');
            //状态
            $menu_state = I('menu_state', 1);
            // 上级
            $parent     = I('parentid', 0);

            if (empty($id)) {
                return false;
            }

            if ($parent == 'NaN' || $parent < 0) {
                $parent = 0;
            }

            // 参数处理
            $url_arr = [$moudle, $controller, $method];
            $url_arr = empty($params) ?  $url_arr : array_push($url_arr, $params);
            

            // ###数据组装
            // 菜单名
            $data['name']       = $menu_name;
            // 页面路径
            $data['src']        = ($parent == 0) ? '/default/default/default' : '/' . join("/", $url_arr);
            // 是否显示
            $data['show']       = $menu_state;
            // 上级
            $data['parentid']   = $parent;

            return $this->resultResponseAjax($this->update('bbcmf_rule', $data, ['id' => $id]), '添加更新成功', '添加更新失败');
        }
        
    }



    // ----------------------------------
    // 导出菜单/导出sql文件
    // ----------------------------------
    public function exportMenu(){
        $dir        = ["public", "static", "cmf", "menu"];
        $file_name  = md5(time()) . ".sql";
        
        // 判断是单应用还是多应用
        $root_path  = empty($_GET['_app_']) ? ROOT_PATH : ROOT_PATH . '../';
        $dir        = $root_path . join(DS, $dir) . DS . $file_name;

        $info  = "SET NAMES utf8;\r\n";
        $info .= "SET FOREIGN_KEY_CHECKS = 0;\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "-- Table structure for `bbcmf_rule`\r\n";
        $info .= "-- ----------------------------\r\n";

        $info .= "DROP TABLE IF EXISTS `bbcmf_rule`;\r\n";
        $info .= "CREATE TABLE `bbcmf_rule` (\r\n";
        $info .= "`id` int(11) NOT NULL AUTO_INCREMENT,\r\n";
        $info .= "`name` varchar(128) DEFAULT NULL,\r\n";
        $info .= "`parentid` int(128) DEFAULT NULL,\r\n";
        $info .= "`src` varchar(128) DEFAULT NULL,\r\n";
        $info .= "`show` int(11) DEFAULT NULL,\r\n";
        $info .= "`icon` varchar(128) DEFAULT NULL,\r\n";
        $info .= "PRIMARY KEY (`id`)\r\n";
        $info .= ") ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;\r\n";
        
        $info .= "-- ----------------------------\r\n";
        $info .= "--  Records of `bbcmf_rule`\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "BEGIN;\r\n";
        $info .= "INSERT INTO `bbcmf_rule` VALUES ";

        $data = [];
        foreach ($this->all('bbcmf_rule') as $rule) {
            $tmp = [];
            foreach ($rule as $key => $value) {
                array_push($tmp, "'" . $value . "'");
            }
            array_push($data, '(' . join(', ', $tmp) . ')');
        }

        $info .= join(', ', $data) . ";\r\n";
        $info .= "COMMIT;\r\n";
        $info .= "SET FOREIGN_KEY_CHECKS = 1;";
        
        //生成sql文件
        file_put_contents($dir, $info, FILE_APPEND);
        return json_encode(["status" => "1", "data" => "/static/cmf/menu/" . $file_name]);
    }




    // ----------------------------------
    // 导入菜单/执行sql文件
    // ----------------------------------
    public function importMenu(){
        if (IS_GET) {
            return $this->fetch("upload");
        }

        if (IS_POST) {
            return $this->execSql(file_get_contents($this->uploadFile("file", "/static/cmf/menu/", true))) == FALSE ? 1 : 0;
        }
        
    }

}
?>