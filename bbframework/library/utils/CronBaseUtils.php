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
use bb\DB;

class CronBaseUtils {

	// sqlite任务的 表名
	private static $table_name = "task";
	/**
	 * 调度函数
	 * 		使用方法:
	 * 			step1: crontab -e
	 * 				   插入 * * * * * python 你cron.py的物理地址
	 *
	 *  			   例如: * * * * * python /Applications/XAMPP/xamppfiles/htdocs/web/bbframework/bbframework/library/third/cron/cron.py
	 *			step2: 配置数据库中 task_config中要调度的核心 url 地址 然后加上一个id参数 格式(需要http://): url/id/
	 *
	 * 				   例如: http://www.bbframework.com/index/index/task/id/
	 *
	 * 			step3: 在你task_config配置的action中,使用本方法
	 *
	 * 				   例如: use util\CronBaseUtils
	 * 						CronBaseUtils::cron($_REQUEST['id']);
	 *
	 * @param $id 被调用的cron的id
	 *
	 * @return string
	 */
	public static function cron($id)
	{
		$ret = DB::table(self::$table_name)->where('id', $id)->first();

		if ($ret){
			$time = time();
			if (self::_is_time_cron($time, $ret['rule'])) {
				$type = $ret['type'];
				switch ($type) {
					case 'url':
						return self::_action_url($ret['url'], $ret['id']);
						break;
					case 'file':
						self::_action_file($ret['url']);
						break;
					default:
						return 'error type!';
						break;
				}
			}
		}
	}




	/**
	 * 添加cron
	 *
	 * @param $params array
	 * 		  min 分钟 1～59
	 * 		  hour 小时 1～23
	 * 		  day 日 1～31
	 * 		  mon 月 1～12
	 * 		  week 星期 0～6
	 *		  module_name 模块名
	 *  	  controller_name 控制器名
	 *  	  action_name 方法名
	 *  	  paramas 参数 格式 id/1/name/null
	 * 		  name 任务名称
	 * 		  type 任务类型 默认:url
	 *		  description 描述
	 *
	 * min hour day mon week => * * * * * (与crontab用法完全一致,可自行参考百度)
	 *
	 * @example $params = array(
	 *			'min' => '*',
	 *			'hour' => '*',
	 *			'day' => '*',
	 *			'mon' => '*',
	 *			'week' => '*',
	 * 			'module_name' => 'index',
	 *			'controller_name' => 'index',
	 *			'action_name' => 'database_backup',
	 *			'paramas'=>'id/1/name/null',
	 *			'name' => '打印log',
	 *			'type' => 'url',
	 *			'description' => '描述',
	 *			);
	 *
	 *  时间配置使用说明:
	 *	min 为 * 时表示每分钟都要执行 url，hour 为 * 时表示每小时都要执行程序，其馀类推
	 *	min 为 a-b 时表示从第 a 分钟到第 b 分钟这段时间内要执行，hour 为 a-b 时表示从第 a 到第 b 小时都要执行，其馀类推
	 *	min 为 *中间无空格/n 时表示每 n 分钟个时间间隔执行一次, hour 为 *中间无空格/n 表示每 n 小时个时间间隔执行一次，其馀类推
     *	min 为 a, b, c,... 时表示第 a, b, c,... 分钟要执行，hour 为 a, b, c,... 时表示第 a, b, c...个小时要执行，其馀类推
	 *
	 * @return string
	 */
	public static function add_cron($params)
	{
		// --- 数据处理 ---

		$params['description'] = empty($params['description']) ? '无' : $params['description'];
		$rule = $params['min'] . ' ' . $params['hour'] . ' ' . $params['day'] . ' ' . $params['mon'] . ' ' . $params['week'];
		$url = 'http://' . $_SERVER[ "HTTP_HOST"].'/'.$params['module_name'].'/'.$params['controller_name'].'/'.$params['action_name'];
		$url = empty($params['paramas']) ? $url : $url.'/'.$params['paramas'];
		// --------------

		$sql ="INSERT INTO tp_".self::$table_name." (NAME,TYPE,RULE,URL,DESCRIPTION,FAIL_TIME,SUCCESS_TIME,IS_ON,SUCCESS_COUNT,FAIL_COUNT)"
				." VALUES ('".$params['name'] ."', '".$params['type']."', '".$rule."', '".$url."', '".$params['description']
				."','0','0',1,0,0)";

//		$ret = DB::select($sql);
		$sql_arr = [
			"url"=>$url,
			"name"=>$params['name'],
			"type"=>$params['type'],
			"rule"=>$rule,
			"description"=>$params['description'],
			"is_on"=>1
		];

		$ret = DB::table(self::$table_name)->insertGetId($sql_arr);

		if (!$ret){
			return 'error';
		}else{
			return $ret;
		}
	}




	/**
	 * 更新 cron
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
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public static function update_cron($params, $id){
		if(empty($id)){
			return 'id error!';
		}

		$ret = DB::table(self::$table_name)->where('id', $id)->first();

		if (!$ret){
			return 'error! ';
		}

		$params = array_merge($ret, $params);

		// --- 数据处理 ---
		$description = empty($params['description']) ? '无' : $params['description'];

		// 打散url 数据 重新组装
		$tmp_array = explode("/",$ret['url']);
		$params['module_name'] = isset($params['module_name']) ? $params['module_name'] : $tmp_array[3];
		$params['controller_name'] = isset($params['controller_name']) ? $params['controller_name'] : $tmp_array[4];
		$params['action_name'] = isset($params['action_name']) ? $params['action_name'] : $tmp_array[5];

		$url = 'http://' . $_SERVER[ "HTTP_HOST"].'/'.$params['module_name'].'/'.$params['controller_name'].'/'.$params['action_name'];

		// 参数的处理
		if(!isset($params['paramas'])){
			if(isset($tmp_array[6])){
				$params['paramas'] = $tmp_array[6];
			}
		}

		$url = !isset($params['paramas']) ? $url : $url.'/'.$params['paramas'];
		// --------------

		$sql ="UPDATE tp_".self::$table_name." set "
			 ."name='" . $params['name'] . "',url='" . $url . "',rule='" . $params['rule'] . "',description='" . $description
			 ."',success_time='" . $params['success_time'] . "',success_count='" . $params['success_count']
			 ."',fail_time='" . $params['fail_time'] . "',fail_count='" . $params['fail_count']
			 ."' where id=" . $id;

//		DB::select($sql);
		$update_arr = array();
		$update_arr['name'] = $params['name'];
		$update_arr['url'] = $params['url'];
		$update_arr['rule'] = $params['rule'];
		$update_arr['description'] = $description;
		$update_arr['success_time'] = $params['success_time'];
		$update_arr['success_count'] = $params['success_count'];
		$update_arr['fail_time'] = $params['fail_time'];
		$update_arr['fail_count'] = $params['fail_count'];


		DB::table(self::$table_name)->where('id', $id)->update($update_arr);
	}




	/**
	 * 任务开关
	 *
	 * @param $is_on 1 开启
	 * 				 0 关闭
	 * @param $id
	 *
	 * @return string
	 */
	public static function switch_cron($is_on, $id){
		if(empty($id)){
			return 'id error!';
		}

		$sql ="UPDATE tp_".self::$table_name." set "
				."is_on=" . $is_on
				." where id=" . $id;

		$ret = DB::select($sql);
	}




	/**
	 * 删除 cron
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public static function del_cron($id){
		if(empty($id)){
			return 'id error!';
		}

		$ret = DB::table(self::$table_name)->where('id', $id)->delete();

		if (!$ret){
			return 'error! ';
		}else{
			return $ret;
		}
	}




	// [内部方法] url操作
	protected static function _action_url($url, $id){
		if(empty($url)){
			return 'url error';
		}else{
			// 调度之后的返回值
			$keyword = self::_curl_get_contents($url);

			$ret = DB::table(self::$table_name)->where('id', $id)->first();

			$params = array();

			if($keyword == 'fail'){
				$params = array(
						'fail_time' => time(),
						'fail_count' => $ret['fail_count']+1
				);
			}elseif($keyword == 'success'){
				$params = array(
						'success_time' => time(),
						'success_count' => $ret['success_count']+1
				);
			}else{
				$params = array(
					'fail_time' => time(),
					'fail_count' => $ret['fail_count']+1
				);
			}

			// 更新最后成功/失败 时间 以及次数
			self::update_cron($params, $id);

			// 写入cron日志
			self::_cron_log($ret['url'], $keyword);
		}
	}


	// [内部方法] 日志
	protected static function _cron_log($url, $keyword){
		switch($keyword){
			case 'fail':
				$state = 0;
				break;
			case 'success':
				$state = 1;
				break;
			default:
				$state = 0;
				break;
		}

		DB::table("task_log")->insert(array('url'=>$url, 'time'=>time(), 'keyword'=>$keyword, 'state'=>$state));
	}

	// [内部方法] 文件操作
	protected static function _action_file($path){
//        $myfile = fopen("/Applications/XAMPP/xamppfiles/htdocs/web/bbframework/public/static/log.log", "a") or die("Unable to open file!");
//        $txt = date("Y:m:d H:i:s", time())."\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);
	}

	// [内部方法] 访问url
	protected static function _curl_get_contents($url,$timeout=1) {
		$curlHandle = curl_init();
		curl_setopt( $curlHandle , CURLOPT_URL, $url );
		curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout );
		$result = curl_exec( $curlHandle );
		curl_close( $curlHandle );
		return $result;
	}

	// [内部方法] 调度规则函数
	protected static function _is_time_cron($time , $cron)
	{
		$cron_parts = explode(' ' , $cron);
		if(count($cron_parts) != 5){
			return false;
		}

		list($min , $hour , $day , $mon , $week) = explode(' ' , $cron);

		$to_check = array('min' => 'i' , 'hour' => 'G' , 'day' => 'j' , 'mon' => 'n' , 'week' => 'w');

		$ranges = array(
				'min' => '0-59' ,
				'hour' => '0-23' ,
				'day' => '1-31' ,
				'mon' => '1-12' ,
				'week' => '0-6' ,
		);

		foreach($to_check as $part => $c) {
			$val = $$part;
			$values = array();

			if(strpos($val , '/') !== false) {
				//Get the range and step
				list($range , $steps) = explode('/' , $val);
				//Now get the start and stop
				if($range == '*') {
					$range = $ranges[$part];
				}
				list($start , $stop) = explode('-' , $range);
				for($i = $start ; $i <= $stop ; $i = $i + $steps) {
					$values[] = $i;
				}
			}else{
				$k = explode(',', $val);
				foreach($k as $v) {
					if(strpos($v, '-') !== false) {
						list($start, $stop) = explode('-', $v);
						for($i = $start ; $i <= $stop ; $i++) {
							$values[] = $i;
						}
					}else{
						$values[] = $v;
					}
				}
			}
			if(!in_array(date($c, $time), $values) and (strval($val) != '*')){
				return false;
			}
		}

		return true;
	}
}
