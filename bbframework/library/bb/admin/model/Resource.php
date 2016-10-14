<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;
use bb\DB;

class Resource extends AdminModel {

    protected $table = 'staff_resource';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;

    //----------------------------------
    // 常量
    //----------------------------------
    // 标签[职位]
    const TAG_POSITION      = 'position';
    // 标签[任务-优先级]
    const TAG_TASKPRIORITY  = 'taskpriority';
    // 标签[任务-单子类型]
    const TAG_TASKKIND      = 'taskkind';
    // 标签[任务-进度]
    const TAG_TASKPROCESS   = 'taskprocess';
    // 标签[任务-操作]
    const TAG_TASKOPERATE   = 'taskoperate';
    // 标签[任务-状态]
    const TAG_TASKSTATUS    = 'taskstatus';


    //----------------------------------
    // 检索数据
    //----------------------------------
    /**
     * 获得所有数据[职位]
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getAllPositions() {
        return $this->getAllByTag(self::TAG_POSITION);
    }

    /**
     * 获得所有数据[标识]
     * @param string $tag 标识
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getAllByTag($tag) {
        $result = array();

        $data = $this->getAll('tag = "' . $tag . '"');
        if (!empty($data)) {
            foreach ($data as $key => $item) {
                $result[] = array('id' => $item['tag_id'], 'name' => $item['name']);
            }
        }

        return $result;
    }

    /**
     * 获得所有数据
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getAll($condition=NULL) {
        $sql =
            'SELECT * ' .
            'FROM ' .
            '   bb_staff_resource ' .
            'WHERE ' .
            $condition .
            ' ORDER BY ' .
            '   tag ASC, sort ASC, id ASC';

        // 查询记录
        $result =DB::select($sql);

        return $result;
    }
}