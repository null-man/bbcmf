<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.tool.pub All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: AC.Cai <c1985@vip.qq.com> <http://do.org.cn>
// +----------------------------------------------------------------------

namespace bb\admin\common\api;

/**
 * 日期/时间接口类
 * @author AC <63371896@qq.com>
 */
class DateTimeApi { 
    //----------------------------------
    // 变量
    //----------------------------------
    // 时间戳类型[日期+时间]
    const TIMESTAMP_TYPE_DATETIME       = 0;
    // 时间戳类型[日期]
    const TIMESTAMP_TYPE_DATE           = 1;
    // 时间戳类型[时间]
    const TIMESTAMP_TYPE_TIME           = 2;
    // 时间戳类型[小时]
    const TIMESTAMP_TYPE_HOUR           = 3;
    // 时间戳类型[分钟]
    const TIMESTAMP_TYPE_MINUTE         = 4;
    // 时间戳类型[秒]
    const TIMESTAMP_TYPE_SECOND         = 5;
    // 时间戳类型[年]
    const TIMESTAMP_TYPE_YEAR           = 6;
    // 时间戳类型[月]
    const TIMESTAMP_TYPE_MONTH          = 7;
    // 时间戳类型[日]
    const TIMESTAMP_TYPE_DAY            = 8;
    // 时间戳类型[分钟-折半]
    const TIMESTAMP_TYPE_MINUTE_HALF    = 9;
    // 时间戳类型[中文日期]
    const TIMESTAMP_TYPE_DATE_CH        = 10;
    // 时间戳类型[年月日时分]
    const TIMESTAMP_TYPE_YMDHI          = 11;
    // 时间戳类型[年月日时分00]
    const TIMESTAMP_TYPE_YMDHI00        = 12;
    // 时间戳类型[年月日]
    const TIMESTAMP_TYPE_YMD_CH         = 13;
    // 时间戳类型[年月日]
    const TIMESTAMP_TYPE_YMD            = 14;





    //----------------------------------
    // 功能方法
    //----------------------------------
    /**
     * 格式化相差时间
     * @param timestamp $datetime 时间戳
     * @param enum $kind 格式化类型
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function formatDiff($datetime, $kind=1) {
        // 结果数据
        $result = NULL;

        // 格式化时间
        $datetime   = intval($datetime);

        // 当前时间
        $now        = time();
        // 相差时间
        $diff       = $now - $datetime;

        // 处理时间格式
        if ($diff < 5) {
            $result = '刚刚';
        } elseif ($diff < 60) {
            $result = intval($diff) . '秒前';
        } elseif ($diff < 60 * 60) {
            $result = intval($diff / 60) . '分钟前';
        } elseif ($diff < 60 * 60 * 24) {
            $result = intval($diff / 60 / 24) . '小时前';
        } elseif ($diff < 60 * 60 * 24 * 7) {
            $result = intval($diff / 60 / 24 / 7) . '周前';
        } elseif ($diff < 60 * 60 * 24 * 30) {
            $result = intval($diff / 60 / 24 / 30) . '个月前';
        } else {
            $result = intval($diff / 60 / 24 / 365) . '年前';
        }

        // 返回结果数据
        return $result;
    }











    //----------------------------------
    // 常用函数
    //----------------------------------
    /**
     * 获得某日开始时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getDayStartTimestamp($datetime=NULL) {
        $now  = ($datetime === NULL ? time() : $datetime);
        return strtotime(date('Y-m-d 00:00:00', $now));
    }

    /**
     * 获得某日结束时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getDayEndTimestamp($datetime=NULL) {
        $now  = ($datetime === NULL ? time() : $datetime);
        return strtotime(date('Y-m-d 23:59:59', $now));
    }

    /**
     * 获得本周周一时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getWeekStartTimestamp($datetime=NULL) {
        // 当前时间
        $now  = ($datetime === NULL ? time() : $datetime);
        // 今天起始
        $now0 = strtotime(date('Y-m-d 00:00:00', $now));
        // 今天结束
        $now1 = strtotime(date('Y-m-d 23:59:59', $now));
        // 一天时间
        $aday = $now1 - $now0;

        // 星期索引(数字表示 0（星期天）到 6（星期六）)
        $now_week_index = intval(date('w', $now));

        // 格式化星期索引
        if ($now_week_index === 0) {
            $now_week_index = 7;
        }

        return $now0 - ($now_week_index - 1) * $aday;
    }

    /**
     * 获得本周周日时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getWeekEndTimestamp($datetime=NULL) {
        // 当前时间
        $now  = ($datetime === NULL ? time() : $datetime);
        // 今天起始
        $now0 = strtotime(date('Y-m-d 00:00:00', $now));
        // 今天结束
        $now1 = strtotime(date('Y-m-d 23:59:59', $now));
        // 一天时间
        $aday = $now1 - $now0;

        // 星期索引(数字表示 0（星期天）到 6（星期六）)
        $now_week_index = intval(date('w', $now));

        // 格式化星期索引
        if ($now_week_index === 0) {
            $now_week_index = 7;
        }

        return $now1 + (7 - $now_week_index) * $aday;
    }

    /**
     * 获得当月第一天时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getMonthStartTimestamp($datetime=NULL) {
        return strtotime(DateTimeApi::getThisMonthFirstDay($datetime) . ' 00:00:00');
    }

    /**
     * 获得当月最后一天时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getMonthEndTimestamp($datetime=NULL) {
        return strtotime(DateTimeApi::getThisMonthLastDay($datetime) . ' 23:59:59');
    }

    /**
     * 获得上月第一天时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getLastMonthStartTimestamp($datetime=NULL) {
        return strtotime(DateTimeApi::getPrevMonthFirstDay($datetime) . ' 00:00:00');
    }

    /**
     * 获得上月最后一天时间戳
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getLastMonthEndTimestamp($datetime=NULL) {
        return strtotime(DateTimeApi::getPrevMonthLastDay($datetime) . ' 23:59:59');
    }

    /**
     * 获得当月第一天
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getThisMonthFirstDay($datetime=NULL) {
        return date('Y-m-01', ($datetime === NULL ? time() : $datetime));
    }

    /**
     * 获得当月最后一天
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getThisMonthLastDay($datetime=NULL) {
        $datetime = ($datetime === NULL ? time() : $datetime);
        return date('Y-m-d', strtotime(date('Y-m-01', $datetime) . ' +1 month -1 day'));
    }

    /**
     * 获得上月第一天
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getPrevMonthFirstDay($datetime=NULL) {
        return date('Y-m-d', strtotime(date('Y-m-01', ($datetime === NULL ? time() : $datetime)) . ' -1 month'));
    }

    /**
     * 获得上月最后一天
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getPrevMonthLastDay($datetime=NULL) {
        $datetime = ($datetime === NULL ? time() : $datetime);
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($datetime)) . ' -1 day'));
    }

    /**
     * 获得下月第一天
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getNextMonthFirstDay($datetime=NULL) {
        return date('Y-m-d', strtotime(date('Y-m-01', ($datetime === NULL ? time() : $datetime)) . ' +1 month'));
    }

    /**
     * 获得下月最后一天
     * @param timestamp $datetime 时间戳
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function getNextMonthLastDay($datetime=NULL) {
        $datetime = ($datetime === NULL ? time() : $datetime);
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($datetime)) . ' +2 month -1 day'));
    }


    /**
     * 格式化时间戳
     * @param timestamp $datetime 时间戳
     * @param enum $kind 格式化类型(0:日期+时间, 1:日期, 2:时间)
     * @return 结果数据
     * @author AC <63371896@qq.com>
     */
    public static function formatTimestamp($datetime, $kind=0) {
        // 结果数据
        $result = NULL;

        // 格式化时间
        $datetime   = intval($datetime);

        if ($kind === DateTimeApi::TIMESTAMP_TYPE_DATETIME || $kind === DateTimeApi::TIMESTAMP_TYPE_DATE) {
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
                $datetime_week_index_cn = DateTimeApi::convertWeekIndex(date('w', $datetime));

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
    protected static function convertWeekIndex($week_index) {
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

}