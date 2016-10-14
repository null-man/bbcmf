<?php
namespace app\admin\controller;

/**
 * Class Group 用户组类
 * @package app\admin\controller
 */
class Group extends Common {

    // 表名
    protected $table            = 'bbcmf_group';
    // 添加 页面
    protected $view_add         = 'add_group';
    // 编辑 页面
    protected $view_edit        = 'edit_group';
    // 主页 页面
    protected $view_index       = 'group_manage';
    

    function __construct(){
        parent::__construct();
        $this->noCheckAccess(['groupTree', 'groupViewAdd', 'groupViewEdit', 'exportgroup', 'importGroup', 'logicDelete']);
    }


    // ----------------------------------
    // 所有用户组
    // ----------------------------------
    public function groupTree(){
        // ###组装树形结构
        // 获取树形结构名称
        $name_arr = [];
        foreach (explode('|', $this->treeStyleData($this->table, "\$id - \$spacer \$group |")) as $_k => $_v) {
            if ($_v) {
                $k_v_arr = explode(' - ', $_v);
                $name_arr[$k_v_arr[0]] = $k_v_arr[1];
            }
        }

        // 重新组装名称
        $show_data = [];
        foreach ($this->all($this->table) as $key => $rule) {
            foreach ($rule as $k => $v) {
                $tmp[$k] = ($k == 'group') ? $name_arr[$rule['id']] : $v; 
            }
            array_push($show_data, $tmp);
        }

        return json_encode(['data' => $show_data]);
    }


    // ----------------------------------
    // 用户组 添加
    // ----------------------------------
    public function groupViewAdd(){
        if (IS_GET) {
            $this->assign("select_categorys", $this->treeStyleData($this->table, "<option value='\$id'>\$spacer \$group</option>"));
            return $this->fetch($this->view_add);    
        }

        if (IS_POST) {
            // ###获取数据
            // 组名
            $group       = I('group', '');
            // 状态
            $state       = I('state', 1);
            // 上级
            $parentid    = I('parentid', 0);


            // ###数据组装
            // 组名
            $data['group']      = $group;
            // 父节点
            $data['parentid']   = $parentid;
            // 状态
            $data['state']      = $state;

            return $this->resultResponseAjax($this->insert($this->table, $data), '添加组成功', '添加组失败');
        }
    }




    // ----------------------------------
    // 用户组 编辑
    // ----------------------------------
    public function groupViewEdit(){
        if (IS_GET) {
            $this->assign("select_categorys", $this->treeStyleData($this->table, "<option value='\$id' \$selected>\$spacer \$group</option>"));
            return $this->fetch($this->view_edit);    
        }

        if (IS_POST) {
            // ###获取数据
            // id
            $id         = I('id', 0);
            // ###获取数据
            // 组名
            $group       = I('group', '');
            // 状态
            $state       = I('state', 1);
            // 上级
            $parentid    = I('parentid', 0);
            
            if (empty($id)) {
                return false;
            }

            if ($parentid == 'NaN' || $parentid == -1) {
                $parentid = 0;
            }


            // ###数据组装
            // 组名
            $data['group']      = $group;
            // 父节点
            $data['parentid']   = $parentid;
            // 状态
            $data['state']      = $state;

            return $this->resultResponseAjax($this->update($this->table, $data, ['id' => $id]), '更新组成功', '更新组失败');
        }
        
    }



    // ----------------------------------
    // 导出菜单/导出sql文件
    // ----------------------------------
    public function exportgroup(){
        $dir        = ["public", "static", "cmf", "group"];
        $file_name  = md5(time()) . ".sql";
        
        $root_path  = empty($_GET['_app_']) ? ROOT_PATH : ROOT_PATH . '../';
        $dir        = $root_path . join(DS, $dir) . DS . $file_name;

        $info  = "SET NAMES utf8;\r\n";
        $info .= "SET FOREIGN_KEY_CHECKS = 0;\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "-- Table structure for `bbcmf_group`\r\n";
        $info .= "-- ----------------------------\r\n";

        $info .= "DROP TABLE IF EXISTS `bbcmf_group`;\r\n";
        $info .= "CREATE TABLE `bbcmf_group` (\r\n";
        $info .= "`id` int(11) NOT NULL AUTO_INCREMENT,\r\n";
        $info .= "`group` varchar(128) DEFAULT NULL,\r\n";
        $info .= "`parentid` int(11) DEFAULT NULL,\r\n";
        $info .= "`state` int(11) DEFAULT NULL,\r\n";
        $info .= "PRIMARY KEY (`id`)\r\n";
        $info .= ") ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;\r\n";
        
        $info .= "-- ----------------------------\r\n";
        $info .= "--  Records of `bbcmf_group`\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "BEGIN;\r\n";
        $info .= "INSERT INTO `bbcmf_group` VALUES ";

        $data = [];
        foreach ($this->all('bbcmf_group') as $rule) {
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
        return json_encode(["status" => "1", "data" => "/static/cmf/group/" . $file_name]);
    }




    // ----------------------------------
    // 导入菜单/执行sql文件
    // ----------------------------------
    public function importGroup(){
        if (IS_GET) {
            return $this->fetch("upload");
        }

        if (IS_POST) {
            return $this->execSql(file_get_contents($this->uploadFile("file", "/static/cmf/group/", true))) == FALSE ? 1 : 0;
        }
        
    }
}
?>