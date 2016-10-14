<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;
use bb\DB;

class Department extends AdminModel {

    protected $table = 'department';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;

    /**
     * 获得全部数据
     * @param array $where 检索条件
     * @param string $field 检索字段
     * @param string $order 排序字段
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    public function getAll($where=NULL, $field=NULL, $order=NULL) {
        // ###结果数据
        $result = NULL;


        // ###检索数据
        // 查询字段
        $field = ($field === NULL) ? $this->defaultSqlField() : $field;
        // 查询条件
        $where = ($where === NULL) ? 'd.is_del=0 ' : $where;
        // 查询排序
        $order = ($order === NULL) ? ' d.id ASC' : $order;
        // 查询记录
//        $result = $this
//            ->alias('d')
//            ->join('LEFT JOIN __PICTURE__ pic ON d.img = pic.id')
//            ->field($field)
//            ->where($where)
//            ->order($order)
//            ->select();

        $sql =
            'SELECT ' .
            $field.
            'FROM ' .
            '   bb_staff_department d ' .
            'LEFT JOIN' .
            '   bb_staff_picture pic ON d.img = pic.id ' .
            'WHERE ' .
            $where .
            'ORDER BY ' .
            $order;

        // 查询记录
        $result =DB::select($sql);
        // ###返回结果数据
        return $result;
    }


    //----------------------------------
    // 方法 - SQL片段
    //----------------------------------
    /**
     * 默认SQL查询字段
     * @return string 结果数据
     * @author Loring <597852546@qq.com>
     */
    public function defaultSqlField() {
        return 'd.id, d.name, CONCAT("' . 'http://'.$_SERVER['SERVER_NAME'] . '", pic.path) as img_path, d.info ';
    }


    //----------------------------------
    // 方法 - 检索数据
    //----------------------------------
    /**
     * 获得数据
     * @param int $id 部门ID
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    public function getById($id) {
        // ###结果数据
        $result = NULL;


        // ###检索数据
        // 查询字段
        $field = $this->defaultSqlField();
        // 查询条件
        $where = 'd.id = '.$id . ' AND d.is_del = 0 ';
        // 查询记录
//        $result = $this
//            ->alias('d')
//            ->join('LEFT JOIN __PICTURE__ pic ON d.img = pic.id')
//            ->field($field)
//            ->where($where)
//            ->find();

        $sql =
            'SELECT ' .
            $field.
            'FROM ' .
            '   bb_staff_department d ' .
            'LEFT JOIN' .
            '   bb_staff_picture pic ON d.img = pic.id ' .
            'WHERE ' .
            $where;

        // 查询记录
        $result = DB::select($sql);
        // ###返回结果数据
        return $result[0];
    }


    //----------------------------------
    // 方法 - 属性
    //----------------------------------
    /**
     * 获得首条数据
     * @return array 结果数据
     * @author Loring <597852546@qq.com>
     */
    public function getFirstId() {
        // ###结果数据
        $result = NULL;


        // ###检索数据
        // 查询字段
        $field = 'd.id ';
        // 查询条件
        $where = 'd.is_del = 0 ';
        // 查询排序
        $order = 'd.id ASC ';
        // 查询限制
        $limit = '1';
        // 查询记录
//        $result = $this
//            ->alias('d')
//            ->field($field)
//            ->where($where)
//            ->order($order)
//            ->limit($limit)
//            ->find();

        $sql =
            'SELECT ' .
            $field.
            'FROM ' .
            '   bb_staff_department d ' .
            'WHERE ' .
            $where .
            'ORDER BY ' .
            $order .
            'LIMIT ' .
            $limit;

        // 查询记录
        $result = DB::select($sql);

        // ###返回结果数据
        return ($result !== NULL) ? intval($result[0]['id']) : 0;
    }

    //----------------------------------
    // 方法 - 数据库CRUD
    //----------------------------------
    /**
     * 保存或更新数据
     * @param array $data 数据
     * @return int ID
     * @author AC <63371896@qq.com>
     */
    public function saveOrUpdateData($data) {
        // ###操作数据
        $id = $data['id'];

        // ###数据操作
        if ($id !== NULL && $id !== 0) {
            return $this->updateData($data);
        } else {
            return $this->saveData($data);
        }
    }

    /**
     * 更新数据
     * @param array $data 数据
     * @return bool
     * @author AC <63371896@qq.com>
     */
    public function updateData($data) {
        $id = $data['id'];
        $data1 = [];
        foreach ($data as $k=>$v){
            $k != 'leader_kind' && $k != 'add_time' && $v!== null && $data1[$k] = $v;
        }
        DB::table('staff_department')->where('id', $id)->update($data1);
//        $this->where('id='.$id)->save($data);
        return $id;
    }

    //----------------------------------
    // 方法 - 数据库CRUD
    //----------------------------------
    /**
     * 保存数据
     * @param array $data 数据
     * @return int ID
     * @author AC <63371896@qq.com>
     */
    public function saveData($data) {
        $data1 = [];
        foreach ($data as $k=>$v){
            $k != 'leader_kind' && $k != 'add_time' && $v!== null && $data1[$k] = $v;
        }
        return DB::table('staff_department')->insertGetId($data1);
    }

    /**
     * 保存或更新数据[员工-领导]
     * @param array $data 员工数据
     * @param int $id 员工ID
     * @param int $leader_kind 管理者类型
     * @return id
     * @author AC <63371896@qq.com>
     */
    public function saveOrUpdateHeadData($data, $id, $leader_kind) {
        // ###结果数据
        $result = NULL;


        // ###操作数据
        // 模型对象
//        $model          = D('RStaffHead');
        // 部门ID
        $department_id  = $data['department_id'];
        // 组别ID
        $group_id       = $data['group_id'];


        // ###逻辑处理
        // 删除数据
        $id = DB::table('staff_r_head')->where('staff_id', $id)->delete();
//        $model->where('staff_id=' . $id)->delete();
        // 添加数据
        switch ($leader_kind) {
            case 1:
                $result = DB::table('staff_r_head')->insertGetId(array('staff_id'=>$id, 'department_id'=>$department_id, 'group_id'=>$group_id));
//                $result = $model->data(array('staff_id'=>$id, 'department_id'=>$department_id, 'group_id'=>$group_id))->add();
                break;
            case 2:
                $result = DB::table('staff_r_head')->insertGetId(array('staff_id'=>$id, 'department_id'=>$department_id, 'group_id'=>0));
//                $result = $model->data(array('staff_id'=>$id, 'department_id'=>$department_id, 'group_id'=>0))->add();
                break;
            case 3:
                $result = DB::table('staff_r_head')->insertGetId(array('staff_id'=>$id, 'department_id'=>0, 'group_id'=>0));
//                $result = $model->data(array('staff_id'=>$id, 'department_id'=>0, 'group_id'=>0))->add();
                break;
            case 0:
            default:
                break;
        }


        // ###返回结果数据
        return $result;
    }

    //----------------------------------
    // 方法 - 存在判断
    //----------------------------------
    /**
     * 判断部门下是否存在员工
     * @return int $id 部门ID
     * @return bool y/n
     * @author Loring <597852546@qq.com>
     */
    public function existsStaff($id) {
        // ###结果数据
        $result = NULL;


        // ###检索数据
        // 查询条件
        $where = 'd.id = ' .$id .' AND d.is_del = 0 AND s.is_del = 0';

        $sql =
            'SELECT COUNT(*) ' .
            'FROM ' .
            '   bb_staff_department d ' .
            'LEFT JOIN' .
            '    bb_staff s ON s.department_id = d.id ' .
            'WHERE ' .
            $where;

        // 查询记录
        $result = DB::select($sql);
        // 查询记录
//        $result = $this
//            ->alias('d')
//            ->join('LEFT JOIN __STAFF__ s ON s.department_id = d.id')
//            ->where($where)
//            ->count();
        // ###返回结果数据
        return intval($result[0]['COUNT(*)']) > 0;
    }


    /**
     * 删除数据
     * @param int $id ID
     * @return bool y/n
     * @author AC <63371896@qq.com>
     */
    public function deleteData($id, $true=FALSE) {
        if ($true === FALSE) {
            return $this->deleteDataFalse($id);
        } else {
            return $this->deleteDataTrue($id);
        }
    }

    /**
     * 删除数据[真]
     * @param int $id ID
     * @return bool y/n
     * @author AC <63371896@qq.com>
     */
    public function deleteDataTrue($id) {
        return DB::table('staff_department')->where('id', $id)->delete();
//        return $this->where('id='.$id)->delete();
    }

    /**
     * 删除数据[假]
     * @param int $id ID
     * @return bool y/n
     * @author AC <63371896@qq.com>
     */
    public function deleteDataFalse($id) {
        return DB::table('staff_department')->where('id', $id)->update(array('is_del'=>1));
//        return $this->where('id='.$id)->save(array('is_del'=>VAL_YES));
    }
}