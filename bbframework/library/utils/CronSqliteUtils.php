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
use util\CronBaseUtils;

class CronUtils{

	// +----------------------------------------------------------------------
	// | 调度函数
	// +----------------------------------------------------------------------

	/**
	 * 调度函数
	 *
	 * @param $id 任务id
	 */
	public static function cron($id){
		CronBaseUtils::cron($id);
	}


	/**
	 * 更新 cron
	 *
	 * 更新想要更新的字段即可
	 * 例如 :
	 * 		$params = array(
	 *			'module_name' => 'index222',
	 *			'controller_name' => 'index333',
	 *			//    'action_name' => 'database_backup444',
	 *			//    'paramas'=>'555',
	 *			'rule'=>'* 1 1 * *'
	 *			);
	 *
	 * @param $params
	 *		  module_name 模块名
	 *  	  controller_name 控制器名
	 *  	  action_name 方法名
	 *  	  paramas 参数 格式 id/1/name/null
	 * 		  name 任务名称
	 *		  description 描述
	 *		  rule 调度规则
	 * 		  	min 分钟 1～59
	 * 		  	hour 小时 1～23
	 * 		  	day 日 1～31
	 * 		  	mon 月 1～12
	 * 		  	week 星期 0～6
	 *
	 * 		  	格式: min hour day mon week
	 * 		  	例: * * * * *
	 *
	 * @param $id 任务id
	 */
	public static function update_cron($params, $id){
		CronBaseUtils::update_cron($params, $id);
	}


	/**
	 * 任务开关
	 *
	 * @param $is_on 1 开启
	 * 				 0 关闭
	 *
	 * @param $id
	 */
	public static function switch_cron($is_on, $id){
		CronBaseUtils::switch_cron($is_on, $id);
	}


	/**
	 * 删除 cron
	 *
	 * @param $id 任务id
	 */
	public static function del_cron($id){
		CronBaseUtils::del_cron($id);
	}


	// +----------------------------------------------------------------------
	// | 分 / min
	// +----------------------------------------------------------------------

	/**
	 * 每n分钟执行一次 默认每分钟执行
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param int $n n分钟
	 */
	public static function min($params, $n = 1){
		dump (self::_base($params, "*/$n", '*', '*', '*', '*'));
	}


	/**
	 * 区间执行,默认小时的1-2分
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $range 时间区间
	 * 					  格式 'startmin-endmin'
	 * 					  例:  '1-10'
	 */
	public static function range_min($params, $range='1-2'){
		self::_base($params, $range, '*', '*', '*', '*');
	}

	/**
	 * 多个时间点执行 默认间隔30分钟
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $multiple 多个执行时间
	 * 					  格式 'min1,min2,min3,...'
	 * 					  例:  '0,1,30'
	 */
	public static function multiple_min($params, $multiple='0,30'){
		self::_base($params, $multiple, '*', '*', '*', '*');
	}




	// +----------------------------------------------------------------------
	// | 小时 / hour
	// +----------------------------------------------------------------------

	/**
	 * 每n小时执行一次 默认每小时执行
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param int $n n小时
	 */
	public static function huor($params, $n = 1){
		self::_base($params, '0', "*/$n", '*', '*', '*');
	}


	/**
	 * 区间执行,默认天的1-2点
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $range 时间区间
	 * 					  格式 'starthour-endhour'
	 * 					  例:  '1-10'
	 */
	public static function range_huor($params, $range='1-2'){
		self::_base($params, '0', $range, '*', '*', '*');
	}

	/**
	 * 多个时间点执行 默认间隔12小时
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $multiple 多个执行时间
	 * 					  格式 'huor1,huor2,huor3,...'
	 * 					  例:  '0,1,12'
	 */
	public static function multiple_huor($params, $multiple='0,12'){
		self::_base($params, '0', $multiple, '*', '*', '*');
	}




	// +----------------------------------------------------------------------
	// | 天 / day
	// +----------------------------------------------------------------------

	/**
	 * 每n天执行一次 默认每天执行
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param int $n n天
	 */
	public static function day($params, $n = 1){
		self::_base($params, '0', '0', "*/$n", '*', '*');
	}


	/**
	 * 区间执行,默认月的1-2日
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $range 时间区间
	 * 					  格式 'startday-endday'
	 * 					  例:  '1-10'
	 */
	public static function range_day($params, $range='1-2'){
		self::_base($params, '0', '0', $range, '*', '*');
	}

	/**
	 * 多个时间点执行 默认间隔15天
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $multiple 多个执行时间
	 * 					  格式 'day1,day2,day3,...'
	 * 					  例:  '0,1,15'
	 */
	public static function multiple_day($params, $multiple='0,15'){
		self::_base($params, '0', '0', $multiple, '*', '*');
	}




	// +----------------------------------------------------------------------
	// | 月 / mon
	// +----------------------------------------------------------------------

	/**
	 * 每n月执行一次 默认每月执行
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param int $n n月
	 */
	public static function mon($params, $n = 1){
		self::_base($params, '0', '0', '1', "*/$n", '*');
	}


	/**
	 * 区间执行,默认年的1-2月
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $range 时间区间
	 * 					  格式 'startmon-endmon'
	 * 					  例:  '1-10'
	 */
	public static function range_mon($params, $range='1-2'){
		self::_base($params, '0', '0', '1', $range, '*');
	}

	/**
	 * 多个时间点执行 默认间隔6个月
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $multiple 多个执行时间
	 * 					  格式 'mon1,mon2,mon3,...'
	 * 					  例:  '0,1,6'
	 */
	public static function multiple_mon($params, $multiple='0,15'){
		self::_base($params, '0', '0', '1', $multiple, '*');
	}




	// +----------------------------------------------------------------------
	// | 周 / week
	// +----------------------------------------------------------------------

	/**
	 * 每一周间隔n天执行一次 默认一周每天执行
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param int $n 间隔n天
	 */
	public static function week($params, $n = 1){
		self::_base($params, '0', '0', '*', "*", "*/$n");
	}


	/**
	 * 区间执行,默认周1-周2
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $range 时间区间
	 * 					  格式 'startweek-endweek'
	 * 					  例:  '1-10'
	 */
	public static function range_week($params, $range='1-2'){
		self::_base($params, '0', '0', '*', '*', $range);
	}

	/**
	 * 多个时间点执行 默认间隔3天
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $str $multiple 多个执行时间
	 * 					  格式 'week1,week2,week3,...'
	 * 					  例:  '0,1,3'
	 */
	public static function multiple_week($params, $multiple='1,3'){
		self::_base($params, '0', '*', '*', $multiple, '*');
	}

	/**
	 * 自由使用crontab命令
	 *
	 * @param $params
	 *		  module_name 模块名称
	 *		  controller_name 控制器名称
	 *		  action_name 方法名称
	 *		  paramas 参数 格式 id/1/name/null
	 *		  name 任务名称
	 *		  type  任务类型 默认:url
	 *		  description 描述
	 *
	 * @param $min 分 1-59
	 * @param $hour 小时 1-23 （0表示0点）
	 * @param $day 日 1-31
	 * @param $mon 月 1-12
	 * @param $week 周 0-6（0表示星期天）
	 */
	public static function cron_time($params,  $min, $hour, $day, $mon, $week){
		self::_base($params,  $min, $hour, $day, $mon, $week);
	}




	// [公共基础函数]
	protected static function _base($params, $min, $hour, $day, $mon, $week){
		$params['min'] 	= $min;
		$params['hour'] = $hour;
		$params['day'] 	= $day;
		$params['mon'] 	= $mon;
		$params['week'] = $week;

		return CronBaseUtils::add_cron($params);
	}
}
