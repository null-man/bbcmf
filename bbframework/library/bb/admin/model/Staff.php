<?php

namespace bb\admin\model;

use bb\admin\model\AdminModel;
use bb\DB;
use bb\admin\common\api\DateTimeApi;

class Staff extends AdminModel {

    protected $table = 'staff';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
    public $timestamps = false;


    //----------------------------------
    // 方法 - 员工-部门图
    //----------------------------------
    /**
     * 获得所有数据[详细][公司]
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getCompanyGraph() {
        // ###结果数据
        $result = array(
            'leaders'       => array(),
            'departments'   => array()
        );

        // ###构建查询
        $sql =
            'SELECT DISTINCT ' .
            '   s.id, ' .
            '   s.no, ' .
            '   s.name, ' .
            '   s.department_id, ' .
            '   d.name as department_name, ' .
            '   s.group_id, ' .
            '   g.name as group_name, ' .
            '   s.is_forbidden, ' .
            '   CONCAT("' . 'http://'.$_SERVER['SERVER_NAME'] . '", pic.path) as img_path ' .
            'FROM ' .
            '   bb_staff s ' .
            'LEFT JOIN' .
            '   bb_staff_group g ON s.group_id = g.id ' .
            'LEFT JOIN' .
            '   bb_staff_department d ON s.department_id = d.id ' .
            'LEFT JOIN' .
            '   bb_staff_picture pic ON s.img = pic.id ' .
            'WHERE ' .
            // '   d.is_del = 0 AND ' .  // 注：这里加上部门与组别的判断会去掉无部门无组别的员工
            // '   g.is_del = 0 AND ' .
            '   s.is_del = 0 ' .
            'ORDER BY ' .
            '   s.department_id ASC, s.group_id ASC, s.position_id ASC, s.no ASC';

        // ###检索数据

        $data = DB::select($sql);
        // ###加工数据
        if ($data !== NULL && count($data) > 0) {
            // ###操作数据
            // 主管映射
            $leaderMappers = $this->getLeaderMapper();


            // ###遍历员工记录
            foreach ($data as $item) {

                // 部门ID
                $department_id      = intval($item['department_id']);
                // 组别ID
                $group_id           = intval($item['group_id']);
                // 员工ID
                $staff_id           = intval($item['id']);
                // 主管类型
                $leader_kind        = $this->getLeaderMapperKind($leaderMappers, $department_id, $group_id, $staff_id);
                // 部门名称
                $department_name    = $item['department_name'];
                // 组别名称
                $group_name         = $item['group_name'];


                // FIXME: AC - Handle super admin department/group
                if ($department_id === 0) {
                    if (empty($department_name)) {
                        $department_name = '超级管理员';
                    }
                }
                if ($group_id === 0) {
                    if (empty($group_name)) {
                        $group_name = '管理人员';
                    }
                }


                switch ($leader_kind) {
                    case 3:
                        $result['leaders'][] = $item;
                        break;
                    case 2:
                        // ###部门主管(从属于特定部门)
                        // 判断所属部门存在, 不存在添加记录
                        if (!isset($result['departments'][$department_id])) {
                            $result['departments'][$department_id] = array(
                                'id'      => $department_id,
                                'name'    => $department_name,
                                'leaders' => array(),
                                'groups'  => array()
                            );
                        }
                        // 添加记录
                        $result['departments'][$department_id]['leaders'][] = $item;
                        break;
                    case 1:
                        // ###部门组长(从属于特定部门特定组别)
                        // 判断所属部门存在, 不存在添加记录
                        if (!isset($result['departments'][$department_id])) {
                            $result['departments'][$department_id] = array(
                                'id'      => $department_id,
                                'name'    => $department_name,
                                'leaders' => array(),
                                'groups'  => array()
                            );
                        }
                        // 判断所属组别存在, 不存在添加记录
                        if (!isset($result['departments'][$department_id]['groups'][$group_id])) {
                            $result['departments'][$department_id]['groups'][$group_id] = array('id'=>$group_id, 'name'=>$group_name, 'leaders'=>array(), 'staffs'=>array());
                        }

                        // 添加记录
                        $result['departments'][$department_id]['groups'][$group_id]['leaders'][] = $item;
                        break;
                    case 0:
                    default:
                        // ###普通员工
                        // 判断所属部门存在, 不存在添加记录
                        if (!isset($result['departments'][$department_id])) {
                            $result['departments'][$department_id] = array(
                                'id'      => $department_id,
                                'name'    => $department_name,
                                'leaders' => array(),
                                'groups'  => array()
                            );
                        }
                        // 判断所属组别存在, 不存在添加记录
                        if (!isset($result['departments'][$department_id]['groups'][$group_id])) {
                            $result['departments'][$department_id]['groups'][$group_id] = array('id'=>$group_id, 'name'=>$group_name, 'leaders'=>array(), 'staffs'=>array());
                        }
                        // 添加记录
                        $result['departments'][$department_id]['groups'][$group_id]['staffs'][] = $item;
                        break;
                }
            }


            // 统计个数
            if (true) {
                // 公司BOSS数量
                $result['leader_count'] = count($result['leaders']);
                // 公司部门数量
                $result['department_count'] = count($result['departments']);

                // 遍历部门
                if ($result['department_count'] > 0) {
                    foreach ($result['departments'] as $key1 => $value1) {
                        // 部门主管数量
                        $result['departments'][$key1]['leader_count'] = count($result['departments'][$key1]['leaders']);
                        // 部门组别数量
                        $result['departments'][$key1]['group_count']  = count($result['departments'][$key1]['groups']);
                        // 部门员工数量
                        $result['departments'][$key1]['member_count']  = 0;

                        if ($result['departments'][$key1]['group_count'] > 0) {
                            foreach ($result['departments'][$key1]['groups'] as $key2 => $value2) {
                                $result['departments'][$key1]['groups'][$key2]['leader_count'] = count($result['departments'][$key1]['groups'][$key2]['leaders']);
                                $result['departments'][$key1]['groups'][$key2]['staff_count']  = count($result['departments'][$key1]['groups'][$key2]['staffs']);

                                $result['departments'][$key1]['member_count'] += $result['departments'][$key1]['groups'][$key2]['leader_count'] + $result['departments'][$key1]['groups'][$key2]['staff_count'];
                            }
                        }
                    }
                }
            }
        }


        // FIXME: AC - Add empty departments/gruops
        if (true) {
            // 所有组别
            $group = new Group();
            $groups = $group->getAll();

            // 遍历所有组别, 如果未出现在$result中的部门/组别数据, 进行添加
            foreach ($groups as $item) {
                // 部门ID
                $department_id      = intval($item['department_id']);
                // 部门名称
                $department_name    = $item['department_name'];
                // 组别ID
                $group_id           = intval($item['id']);
                // 组别名称
                $group_name         = $item['name'];

                if (!isset($result['departments'][$department_id])) {
                    $result['departments'][$department_id] = array(
                        'id'            => $department_id,
                        'name'          => $department_name,
                        'leaders'       => array(),
                        'groups'        => array(),
                        'leader_count'  => 0,
                        'group_count'   => 0,
                        'member_count'  => 0
                    );
                }
                if (!isset($result['departments'][$department_id]['groups'][$group_id])) {
                    $result['departments'][$department_id]['groups'][$group_id] = array(
                        'id'            => $group_id,
                        'name'          => $group_name,
                        'leaders'       => array(),
                        'staffs'        => array(),
                        'leader_count'  => 0,
                        'staff_count'   => 0
                    );
                }
            }
        }


        // ###检索数据
        return $result;
    }


    /**
     * 获得所有数据[详细][公司]
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getLeaderMapper() {
        // ###结果数据
        $result = NULL;

        // ###构建查询
        $sql =
            'SELECT DISTINCT ' .
            '   id, ' .
            '   department_id, ' .
            '   group_id, ' .
            '   staff_id ' .
            'FROM ' .
            '   bb_staff_r_head ' .
            'ORDER BY ' .
            '   department_id ASC, group_id ASC, staff_id ASC ';


        // ###检索数据
        $data = DB::select($sql);
        // ###加工数据
        // 加工数据
        if ($data !== NULL && count($data) > 0) {
            // ###初始化
            $result = array(
                'leaders'       => array(),
                'departments'   => array()
            );

            // ###遍历主管记录
            foreach ($data as $item) {
                // ###操作数据
                // 部门ID
                $department_id  = intval($item['department_id']);
                // 组别ID
                $group_id       = intval($item['group_id']);
                // 主管ID
                $staff_id       = intval($item['staff_id']);

                // 判断是否是公司级主管
                if ($department_id === 0 && $group_id === 0) {
                    // ###公司主管(非从属于特定部门)
                    // 防止脏数据
                    if ($staff_id !== 0) {
                        $result['leaders'][] = $staff_id;
                    }
                } elseif ($department_id !== 0 && $group_id === 0) {
                    // ###部门主管(从属于特定部门)
                    // 判断所属部门存在, 不存在添加记录
                    if (isset($result['departments'][$department_id])) {
                        $result['departments'][$department_id] = array(
                            'leaders' => array(),
                            'groups'  => array()
                        );
                    }

                    // 添加记录
                    $result['departments'][$department_id]['leaders'][] = $staff_id;
                } elseif ($department_id !== 0 && $group_id !== 0) {
                    // ###部门组长(从属于特定部门特定组别)
                    // 判断所属部门存在, 不存在添加记录
                    if (isset($result['departments'][$department_id])) {
                        $result['departments'][$department_id] = array(
                            'leaders' => array(),
                            'groups'  => array()
                        );
                    }
                    // 判断所属组别存在, 不存在添加记录
                    if (isset($result['departments'][$department_id]['groups'][$group_id])) {
                        $result['departments'][$department_id]['groups'][$group_id] = array('leaders'=>array());
                    }

                    // 添加记录
                    $result['departments'][$department_id]['groups'][$group_id]['leaders'][] = $staff_id;
                }
            }
        }

        // ###检索数据
        return $result;
    }


    /**
     * 获得主管类型
     * @param array $mapper 主管映射
     * @param int $department_id 部门ID
     * @param int $group_id 组别ID
     * @param int $staff_id 员工ID
     * @return enum 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getLeaderMapperKind($mapper, $department_id, $group_id, $staff_id) {
        // 判断是否是BOSS
        if ($this->inArray($staff_id, $mapper['leaders'])) {
            return 3;
        }
        // 判断是否是部门主管
        if (isset($mapper['departments'][$department_id])) {
            if ($this->inArray($staff_id, $mapper['departments'][$department_id]['leaders'])) {
                return 2;
            }
        }
        // 判断是否是部门组长
        if (isset($mapper['departments'][$department_id])) {
            if (isset($mapper['departments'][$department_id]['groups'] )) {
                if (isset($mapper['departments'][$department_id]['groups'][$group_id])) {
                    if (isset($mapper['departments'][$department_id]['groups'][$group_id]['leaders'])) {
                        if ($this->inArray($staff_id, $mapper['departments'][$department_id]['groups'][$group_id]['leaders'])) {
                            return 1;
                        }
                    }
                }
            }
        }
        // 普通员工
        return 0;
    }


    //----------------------------------
    // 功能方法
    //----------------------------------
    /**
     * 判断项目是否存在于集合中
     * @param object $search 项目
     * @param array $array 集合
     * @return bool y/n
     * @author AC <63371896@qq.com>
     */
    public function inArray($search, $array) {
        $result = false;

        if (is_array($array) && count($array) > 0) {
            foreach ($array as $item) {
                if ($item === $search) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * 获得员工数据[ID]
     * @param int $id ID
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getById($id) {
        return $this->getDetail("s.id = $id AND ");
    }

    /**
     * 获得所有数据[详细]
     * @param array $where 查询条件
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getDetail($where) {
        // ###结果数据
        $result = NULL;


        // ###构建查询
        $sql =
            'SELECT ' .
            '   s.*, ' .
            '   d.name as department_name, ' .
            '   g.name as group_name, ' .
            '   r1.name as position_name, ' .
            '   r2.name as role_name, ' .
            '   CONCAT("' . 'http://'.$_SERVER['SERVER_NAME'] . '", pic.path) as img_path ' .
            'FROM ' .
            '   bb_staff s ' .
            'LEFT JOIN' .
            '   bb_staff_department d ON s.department_id = d.id ' .
            'LEFT JOIN' .
            '   bb_staff_group g ON s.group_id = g.id ' .
            'LEFT JOIN' .
            '   bb_staff_resource r1 ON s.position_id = r1.tag_id AND r1.tag = \'position\' ' .
            'LEFT JOIN' .
            '   bb_staff_role r2 ON s.role_id = r2.id ' .
            'LEFT JOIN' .
            '   bb_staff_picture pic ON s.img = pic.id ' .
            'WHERE ' .
            $where .
            // '   d.is_del = 0 AND ' .  // 注：这里加上部门与组别的判断会去掉无部门无组别的员工
            // '   g.is_del = 0 AND ' .
            '   s.is_del = 0 ' .
            'LIMIT 1 ';

        // ###检索数据
        $data = $this->fetch($sql);
        // ###加工数据
        $result = $this->wrapOne($data);


        // ###返回结果数据
        return $result;
    }

    /**
     * 获得唯一数据（扩展query方法）
     * @param string $sql 查询字符串
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    protected function fetch($sql) {
        $result = DB::select($sql);
        return (($result !== NULL && count($result) > 0) ? $result[0] : NULL);
    }

    /**
     * 包装结果对象
     * @param array $data 数据
     * @return array 结果数据
     * @author AC <63371896@qq.com>
     */
    public function wrapOne($data) {
        // ###过滤数据
        if ($data === NULL) {
            return NULL;
        }


        // ###处理数据
        $data['at_name']            = $this->getAtName($data);
        $data['status_name']        = $this->getStatusName($data['status']);
        $data['leader_kind']        = $this->getLeaderKind($data['id']);
        $data['add_time_format']    = DateTimeApi::formatTimestamp(isset($data['add_time_format']) ? $data['add_time_format'] : '', DateTimeApi::TIMESTAMP_TYPE_YMD_CH);

        // ###返回结果数据
        return $data;
    }

    //----------------------------------
    // 方法 - AT
    //----------------------------------
    /**
     * 获得员工AT名称
     * @param array $data 数据
     * @return string AT名称
     * @author AC <63371896@qq.com>
     */
    public function getAtName($data) {
        // ###结果数据
        $result = '';

        // ###处理数据
        if ($data !== NULL) {
            // 员工姓名
            $name       = $data['name'];
            // 员工昵称
            $nick_name  = $data['nick_name'];
            // 员工工号
            $no         = $data['no'];

            // 根据昵称，工号进行组合
            if ($nick_name !== NULL && $nick_name !== '') {
                $result .= $name . '(' . $nick_name . ')';
            } else {
                $result .= $name . '(' . $no . ')';
            }
        }

        // ###返回结果数据
        return $result;
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


    //----------------------------------
    // 方法 - 状态
    //----------------------------------
    /**
     * 获得状态名称
     * @param int $status 状态
     * @return string 名称
     * @author AC <63371896@qq.com>
     */
    public function getStatusName($status) {
        $data = $this->get_staff_status();
        return $data[$status];
    }

    /**
     * 获得主管类型
     * (
     *      判断依据:
     *          1.如果head表中存在d0&g0则表示是3-公司领导
     *          2.如果head表中存在d1&g0则表示是2-部门部长
     *          3.如果head表中存在d1&g1则表示是1-部门组长
     *          4.如果head表中不存在数据则表示是0-普通员工
     * )
     * @param int $id 员工ID
     * @return enum 结果数据
     * @author AC <63371896@qq.com>
     */
    public function getLeaderKind($id) {
        // ###判断公司领导
        $countBoss = DB::table('staff_r_head')->where(array('staff_id'=>$id, 'department_id'=>0, 'group_id'=>0))->count();
        if ($countBoss > 0) {
            return 3;
        }

        // ###部门部长
        $countDepartment = DB::table('staff_r_head')->where(array('staff_id'=>$id, 'department_id'=>array('neq', 0), 'group_id'=>0))->count();
        if ($countDepartment > 0) {
            return 2;
        }

        // ###部门部长
        $countGroup = DB::table('staff_r_head')->where(array('staff_id'=>$id, 'department_id'=>array('neq', 0), 'group_id'=>array('neq', 0)))->count();
        if ($countGroup > 0) {
            return 1;
        }

        // ###普通员工
        return 0;
    }

    /**
     * 格式化时间戳
     * @param timestamp $datetime 时间戳
     * @param enum $kind 格式化类型(0:日期+时间, 1:日期, 2:时间)
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public function formatTimestamp($datetime, $kind=0) {
        // 结果数据
        $result = NULL;

        // 格式化时间
        $datetime   = intval($datetime);

        if ($kind === 0 || $kind === 1) {
            // 当前时间
            $now  = time();
            // 今天起始
            $now0 = strtotime(date('Y-m-d 00:00:00', $now));
            // 今天结束
            $now1 = strtotime(date('Y-m-d 23:59:59', $now));
            // 一天时间
            $aday = $now1 - $now0;

            // 处理时间格式
            if ($now0 <= $datetime && $datetime <= $now1) {
                $result = '今天';
            } elseif (($now0 - $aday * 1) <= $datetime && $datetime <= ($now1 - $aday * 1)) {
                $result = '昨天';
            } elseif (($now0 - $aday * 2) <= $datetime && $datetime <= ($now1 - $aday * 2)) {
                $result = '前天';
            } elseif (($now0 + $aday * 1) <= $datetime && $datetime <= ($now1 + $aday * 1)) {
                $result = '明天';
            } elseif (($now0 + $aday * 2) <= $datetime && $datetime <= ($now1 + $aday * 2)) {
                $result = '后天';
            } else {
                // 星期索引(数字表示 0（星期天）到 6（星期六）)
                $now_week_index = intval(date('w', $now));

                // 格式化星期索引
                if ($now_week_index === 0) {
                    $now_week_index = 7;
                }

                // 本周区间
                $thisweek0 = $now0 - ($now_week_index - 1) * $aday;
                $thisweek1 = $now1 + (7 - $now_week_index) * $aday;
                // 上周区间
                $lastweek0 = $thisweek0 - 7 * $aday;
                $lastweek1 = $thisweek1 - 7 * $aday;
                // 下周区间
                $nextweek0 = $thisweek0 + 7 * $aday;
                $nextweek1 = $thisweek1 + 7 * $aday;

                // 星期数
                $datetime_week_index_cn =$this->convertWeekIndex(date('w', $datetime));

                // 本周起始
                if ($thisweek0 <= $datetime && $datetime <= $thisweek1) {
                    $result = '周' . $datetime_week_index_cn;
                } elseif ($lastweek0 <= $datetime && $datetime <= $lastweek1) {
                    $result = '上周' . $datetime_week_index_cn;
                } elseif ($nextweek0 <= $datetime && $datetime <= $nextweek1) {
                    $result = '下周' . $datetime_week_index_cn;
                } else {
                    $result = date('m-d', $datetime);
                }
            }
        } else {
            $result = '';
        }

        if ($kind === DateTimeApi::TIMESTAMP_TYPE_DATE) {
            // nothing to do!
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_TIME) {
            $result = date('H:i', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_HOUR) {
            $result = date('H', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_MINUTE) {
            $result = date('i', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_SECOND) {
            $result = date('s', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_YEAR) {
            $result = date('Y', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_MONTH) {
            $result = date('m', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_DAY) {
            $result = date('d', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_MINUTE_HALF) {
            $result = intval(date('i', $datetime));
            $result = ($result < 30) ? 0 : 30;
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_DATE_CH) {
            $result = date('m月d日', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_YMDHI) {
            $result = date('Y-m-d H:i', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_YMDHI00) {
            $result = date('Y-m-d H:i:00', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_YMD_CH) {
            $result = date('Y年m月d日', $datetime);
        } elseif ($kind === DateTimeApi::TIMESTAMP_TYPE_YMD) {
            $result = date('Y-m-d', $datetime);
        } else {
            $result .= ' ' . date('H:i', $datetime);
        }

        // 返回结果数据
        return $result;
    }

    //----------------------------------
    // 辅助方法
    //----------------------------------
    /**
     * 转换星期数
     * @param int $week_index 星期数
     * @return string 中文星期数
     * @author AC <63371896@qq.com>
     */
    public function convertWeekIndex($week_index) {
        $result = NULL;

        switch ($week_index) {
            case 1:
                $result = '一';
                break;
            case 2:
                $result = '二';
                break;
            case 3:
                $result = '三';
                break;
            case 4:
                $result = '四';
                break;
            case 5:
                $result = '五';
                break;
            case 6:
                $result = '六';
                break;
            default:
                $result = '日';
                break;
        }

        return $result;
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
        // ###操作数据
        $id = $data['id'];

        // ###数据格式化
        if (isset($data['password']) && strlen($data['password']) > 0) {
            $data['password'] = md5($data['password']);
        }

        $data1 = [];
        foreach ($data as $k=>$v){
            $k != 'leader_kind' && $k != 'add_time' && $v!== null && $data1[$k] = $v;
        }
        // ###数据操作
        // 更新数据，并返回ID
        DB::table('staff')->where('id', $id)->update($data1);
//        $this->where('id='.$id)->save($data);
        // 更新数据[员工-领导]
        if ($data['leader_kind'] !== NULL) {
            $this->saveOrUpdateHeadData($data, $id, $data['leader_kind']);
        }

        // 返回结果数据
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
        // ###数据格式化
        if (isset($data['password']) && strlen($data['password']) > 0) {
            $data['password'] = md5($data['password']);
        }


        // ###数据操作
        // 保存数据，并返回ID
//        $id = $this->data($data)->add();
        $data1 = [];
        foreach ($data as $k=>$v){
            $k != 'leader_kind' && $k != 'add_time' && $v!== null && $data1[$k] = $v;
        }

        $id = DB::table('staff')->insertGetId($data1);
        // 更新数据[员工-领导]
        if ($data['leader_kind'] !== NULL) {
            $this->saveOrUpdateHeadData($data, $id, $data['leader_kind']);
        }

        // 返回结果数据
        return $id;
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
     * 删除数据[假]
     * @param int $id ID
     * @return bool y/n
     * @author AC <63371896@qq.com>
     */
    public function deleteDataFalse($id) {
        $ret = DB::table('staff')->where('id', $id)->update(array('is_del'=>1));
        return $ret;
//        return $this->where('id='.$id)->save(array('is_del'=>VAL_YES));
    }

    /**
     * 删除数据[真]
     * @param int $id ID
     * @return bool y/n
     * @author AC <63371896@qq.com>
     */
    public function deleteDataTrue($id) {
        return DB::table('staff')->where('id',$id)->delete();
//        return $this->where('id='.$id)->delete();
    }
}