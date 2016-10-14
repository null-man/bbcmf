<?php
namespace bb\admin\controller;
use bb\admin\model\Group;
use bb\admin\model\Role;
use bb\admin\model\Resource;
use think\Controller;
use bb\admin\controller\Admin;
use bb\admin\model\Department;

class Staff extends Admin{

    /**
     * 获得公司图谱
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getCompanyGraph() {
        $staff = new \bb\admin\model\Staff();
        return $staff->getCompanyGraph();
    }

    /**
     * 获得所有角色数据
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getRoles() {
        $role = new Role();
        return $role->getAll();
    }

    /**
     * 获得所有职位数据
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getPositions() {
        $r = new Resource();
        return $r->getAllPositions();
    }

    /**
     * 获得所有状态数据
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getStatuses() {
        return $this->get_staff_status();
    }

    /**
     * 获得所有管理者数据
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getLeaders() {
        // ###结果数据
        $result = array();

        // ###填充数据
        $result[] = array('id'=>0, 'name'=>'普通员工');
        $result[] = array('id'=>1, 'name'=>'部门组长');
        $result[] = array('id'=>2, 'name'=>'部门部长');
        $result[] = array('id'=>3, 'name'=>'公司领导');

        // ###返回结果数据
        return $result;
    }

    //----------------------------------
    // 权限
    //----------------------------------
    /**
     * 判断是否是管理员
     * @return boolean y/n
     * @author Loring <597852546@qq.com>
     */
    protected function isAdministrator() {
        return intval(session('ROLE_ID')) <= 5;
    }

    /**
     * 根据ID获得数据
     * @return int $id 部门ID
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getStaff($id) {
        $staff = new \bb\admin\model\Staff();
        return $staff->getById($id);
    }

    /**
     * 获取员工状态映射
     * @return array 数组
     * @author AC <63371896@qq.com>
     */
    function get_staff_status() {
        return array(
            0 => '离职',
            1 => '在职',
            2 => '实习',
            3 => '留职'
        );
    }

    
    /**
     * 获得部门组别联动
     * @return array $fields 检索字段
     * @author Loring <597852546@qq.com>
     */
    public function getDepartmentGroupLinkage($staff) {
        // ###结果数据
        $result = array();


        // ###操作数据
        // 部门数据
        $d = new Department();
        $departments = $d->getAll();


        // ###逻辑处理
        // 数据封装
        foreach ($departments as $key => $value) {
            // ###构建部门
            // 部门ID
            $result[$key]['id']     = $value['id'];
            // 部门名称
            $result[$key]['name']   = $value['name'];

            // 部门选中
            if ($value['id'] == $staff['department_id']) {
                $result[$key]['checked'] = true;
            }


            // ###构建组别
            // 组别集合
            $result[$key]['child'] = array();
            $g = new Group();
            // 部门组别
            $groups = $g->getAll('department_id=' . $value['id'], 'g.id, g.name ');

            // 遍历所有组别
            if ($groups !== NULL && count($groups) > 0) {
                foreach($groups as $key1 => $value1) {
                    // 组别ID
                    $result[$key]['child'][$key1]['id']     = $value1['id'];
                    // 组别名称
                    $result[$key]['child'][$key1]['name']   = $value1['name'];

                    // 组别选中
                    if ($value1['id'] == $staff['group_id']) {
                        $result[$key]['child'][$key1]['checked'] = true;
                    }
                }
            }

            // 添加"无组别"
            if (true) {
                array_unshift($result[$key]['child'], array('id'=>0, 'name'=>'无组别'));
            }
        }
        $result = array_merge(array(
            array(
                'id'    => '',
                'name'  => '无部门',
                "child" => "无组别"
            )
        ), $result);


        // ###返回结果数据
        return $result;
    }

    //----------------------------------
    // 页面输出
    //----------------------------------
    /**
     * 发送响应数据[JSON]
     * @param enum $status 状态
     * @param string $message 消息
     * @param string $key1 键1
     * @param object $value1 值1
     * @param string $key2 键2
     * @param object $value2 值2
     * @param string $key3 键3
     * @param object $value3 值3
     * @return
     * @author AC <63371896@qq.com>
     */
    protected function responseJson($status, $message, $key1, $value1, $key2 = '', $value2 = '', $key3 ='', $value3 = '') {
        // ###输出结果
        // 设置错误报告级别
        error_reporting(E_ALL|E_STRICT);
        // 输出至页面
        $data = array('status' => $status, 'message'=>$message);

        if ($key1 !== NULL) {
            $data[$key1] = $value1;
        }
        if ($key2 !== NULL) {
            $data[$key2] = $value2;
        }
        if ($key3 !== NULL) {
            $data[$key3] = $value3;
        }

        $this->ajaxReturn($data, 'JSON');

    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data,$type='',$json_option=0) {
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data,$json_option));
            default :
        }
    }




    //----------------------------------
    // 工具方法
    //----------------------------------
    /**
     * 获得所有数据
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getAll() {
        $d = new Department();
        return $d->getAll();
    }

    /**
     * 根据ID获得数据
     * @return int $id 部门ID
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    protected function getDepartment($id) {
        $d = new Department();
        return $d->getById($id);
    }

    /**
     * 获得首条数据ID
     * @return int ID
     * @author Loring <597852546@qq.com>
     */
    protected function getFirstId() {
        $d = new Department();
        return $d->getFirstId();
    }
}
