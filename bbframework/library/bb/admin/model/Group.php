<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;
use bb\DB;

class Group extends AdminModel {

    protected $table = 'staff_group';

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
        $field  = ($field === NULL) ? $this->defaultSqlField() : $field;
        // 查询条件
        $where  = ($where === NULL) ? 'g.is_del = 0 AND d.is_del = 0' : $where;
        $order  = 'g.id ASC';

        $sql =
            'SELECT ' .
                $field.
            'FROM ' .
            '   bb_staff_group g ' .
            'LEFT JOIN' .
            '   bb_staff_department d ON g.department_id = d.id ' .
            'LEFT JOIN' .
            '   bb_staff_picture pic ON d.img = pic.id ' .
            'WHERE ' .
                $where .
            ' ORDER BY ' .
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
        return 'g.id, g.name, g.department_id, d.name as department_name, CONCAT("' . 'http://'.$_SERVER['SERVER_NAME'] . '", pic.path) as img_path, g.info ';
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
        $where = 'g.id=' . $id . ' AND g.is_del=0 AND d.is_del=0';
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
            '   bb_staff_group g ' .
            'LEFT JOIN' .
            '   bb_staff_department d ON g.department_id = d.id ' .
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
        DB::table('staff_group')->where('id', $id)->update($data1);
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
        return DB::table('staff_group')->insertGetId($data1);
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
        return DB::table('staff_group')->where('id', $id)->delete();
//        return $this->where('id='.$id)->delete();
    }

    /**
     * 删除数据[假]
     * @param int $id ID
     * @return bool y/n
     * @author AC <63371896@qq.com>
     */
    public function deleteDataFalse($id) {
        return DB::table('staff_group')->where('id', $id)->update(array('is_del'=>1));
//        return $this->where('id='.$id)->save(array('is_del'=>VAL_YES));
    }
}