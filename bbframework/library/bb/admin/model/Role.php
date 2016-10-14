<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;
use bb\DB;

class Role extends AdminModel {

    protected $table = 'staff_role';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;

    //----------------------------------
    // 方法 - 数据库CRUD
    //----------------------------------
    /**
     * 获得所有数据[角色]
     * @param array $conditions 检索条件
     * @param string $fields 检索字段
     * @author 老金宝 <597852546@qq.com>
     */
    public function getAll($conditions = NULL, $fields=NULL) {
        // ###检索条件
        // 查询字段
        $fields = ($fields === NULL) ? 'id, name ' : $fields;
        // 查询条件
        $where 	= $conditions;
        // 查询排序
        $order 	= 'sort DESC, id ASC';

        $sql =
            'SELECT ' .
            $fields.
            'FROM ' .
            '   bb_staff_role ' .
//            'WHERE ' .
//            $where .
            'ORDER BY ' .
            $order;

        // 查询记录
        $data =DB::select($sql);

        // ###数据操作
        // ###返回结果数据
        return $data;
    }
}