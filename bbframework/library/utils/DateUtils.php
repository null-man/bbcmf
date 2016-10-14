<?php

// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NullYang <635384073@qq.com>
// +----------------------------------------------------------------------

namespace util;


class DateUtils {

	// +----------------------------------------------------------------------
	// | 时间戳处理
	// +----------------------------------------------------------------------

	/**
	 * 将任何英文文本的日期时间描述解析为 Unix 时间戳
	 *
	 * @param $time 要解析的时间字符串
	 * @param $type 类型
	 * @return int 时间戳
	 */
	public static function str2timestamp($time, $type){
		return strtotime($time . " " . $type);
	}



	/**
	 * 将时间戳转换成周x
	 *
	 * @param $timestamp 时间戳
	 * @return mixed
	 */
	public static function timestamp2week($timestamp){
		$number_wk = date("w", $timestamp);
		$weekArr = array("sunday","monday","tuesday","wednesday","thursday","friday","saturday");
		return $weekArr[$number_wk];
	}



	/**
	 * 函数用于对日期或时间进行格式化
	 *
	 * @param $format 格式 例如: Y-m-d H:i:s
	 * @param $timestamp 时间戳
	 * @return bool|string 格式化之后的日期
	 */
	public static function timestamp2date($format, $timestamp){
		return date($format, $timestamp);
	}
}
