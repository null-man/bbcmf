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

namespace bb\cron;

use bb\DB;

class CronBase {

	private $params = [];
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
	public function execute($id)
	{
		ignore_user_abort(true);
		set_time_limit(0);
		$ret = DB::table(self::$table_name)->where('id', $id)->first();

		$info = '';

		if ($ret){
			$time = time();
			if ($this->_is_time_cron($time, $ret['rule'])) {
				$type = $ret['type'];
				switch ($type) {
					case 'url':
						$info = $this->_action_url($ret['url'], $ret['id']);
						break;
					case 'file':
						$info = $this->_action_file($ret['url']);
						break;
					default:
						$info = 'error type!';
						break;
				}
			}
		}

		return $info;
	}

	// 调度规则
	public function rule($min = '*', $hour = '*', $day = '*', $mon = '*', $week = '*'){
		$this->params['min'] = $min;
		$this->params['hour'] = $hour;
		$this->params['day'] = $day;
		$this->params['mon'] = $mon;
		$this->params['week'] = $week;

		return $this;
	}

	// 调度url
	public function url($module, $controller, $action, $params = ''){
		$this->params['module_name'] = $module;
		$this->params['controller_name'] = $controller;
		$this->params['action_name'] = $action;
		$this->params['paramas'] = $params;

		return $this;
	}

	// 调度其他参数
	public function info($name, $description = '', $type = 'url'){
		$this->params['name'] = $name;
		$this->params['description'] = $description;
		$this->params['type'] = $type;

		return $this;
	}

	// 插入数据
	public function insert(){

		$rule = $this->params['min']
			  . ' ' . $this->params['hour']
			  . ' ' . $this->params['day']
			  . ' ' . $this->params['mon']
			  . ' ' . $this->params['week'];

		$url = 'http://'
			 . $_SERVER[ "HTTP_HOST"]
			 . '/'
			 . $this->params['module_name']
			 . '/'
			 . $this->params['controller_name']
			 . '/'
			 . $this->params['action_name'];

		if(array_key_exists('paramas', $this->params)){
			$url = $url . '/' . $this->params['paramas'];
		}

		$sql_arr = [
			"url"=>$url,
			"name"=>$this->params['name'],
			"type"=>$this->params['type'],
			"rule"=>$rule,
			"description"=>$this->params['description'],
			"is_on"=>1
		];

		// 插入前判断是否存在该数据
		$is_exist = DB::table(self::$table_name)->where($sql_arr)->first();

		if($is_exist){
			return 'data exist';
		}

		$ret = DB::table(self::$table_name)->insertGetId($sql_arr);

		if (!$ret){
			return 'error';
		}else{
			return $ret;
		}
	}

	public function where($field){
		$this->params['where'] = $field;
		return $this;
	}

	public function update(){
		if(!array_key_exists('where', $this->params)){
			return 'id error!';
		}

		$ret = DB::table(self::$table_name)->where($this->params['where'])->first();

		if (!$ret){
			return 'error data!';
		}

		$params = array_merge($ret, $this->params);


		// --- 数据处理 ---
		$description = empty($params['description']) ? '' : $params['description'];

		// 打散url 数据 重新组装
//		$tmp_array = explode("/",$ret['url']);
//		$params['module_name'] = isset($params['module_name']) ? $params['module_name'] : $tmp_array[3];
//		$params['controller_name'] = isset($params['controller_name']) ? $params['controller_name'] : $tmp_array[4];
//		$params['action_name'] = isset($params['action_name']) ? $params['action_name'] : $tmp_array[5];
//
//		// 获取主机头
//		$url = 'http://' . $_SERVER[ "HTTP_HOST"] . '/'
//			 . $params['module_name'] . '/'
//			 . $params['controller_name'] . '/'
//			 . $params['action_name'];

		// 参数的处理(参数会有些问题)
//		if(!isset($params['paramas'])){
//			if(isset($tmp_array[6])){
//				$params['paramas'] = $tmp_array[6];
//			}
//		}

//		$url = !isset($params['paramas']) ? $url : $url.'/'.$params['paramas'];
		// --------------

		$update_arr = [
			"url" => $ret['url'],
			"name" => $params['name'],
			"rule" => $params['rule'],
			"success_time" => $params['success_time'],
			"description" => $description,
			"success_count" => $params['success_count'],
			"fail_time" => $params['fail_time'],
			"fail_count" => $params['fail_count'],
			"is_on" => $params['is_on']
		];

		$ret = DB::table(self::$table_name)->where($this->params['where'])->update($update_arr);

		if (!$ret){
			return 'update error!';
		}else{
			return $ret;
		}
	}

	/**
	 * @return string
	 */
	public static function getTableName()
	{
		return self::$table_name;
	}

	/**
	 * @param string $table_name
	 */
	public static function setTableName($table_name)
	{
		self::$table_name = $table_name;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	// 删除任务
	public function delete(){
		if(!array_key_exists('where', $this->params)){
			return 'id error!';
		}

		$ret = DB::table(self::$table_name)->where($this->params['where'])->delete();

		if (!$ret){
			return 'delete error!';
		}else{
			return $ret;
		}
	}



	// 开启任务
	public function on(){
		if(!array_key_exists('where', $this->params)){
			return 'id error!';
		}

		$update_arr = [
			'is_on' => 1
		];

		$ret = DB::table(self::$table_name)->where($this->params['where'])->update($update_arr);

		if (!$ret){
			return 'update error!';
		}else{
			return $ret;
		}
	}

	// 关闭任务
	public function off(){
		if(!array_key_exists('where', $this->params)){
			return 'id error!';
		}

		$update_arr = [
			'is_on' => 0
		];

		$ret = DB::table(self::$table_name)->where($this->params['where'])->update($update_arr);

		if (!$ret){
			return 'update error!';
		}else{
			return $ret;
		}
	}


	// 每n分钟执行
	public function min($n = 1){
		return $this->rule("*/$n");
	}

	// 分钟 区间执行
	public function range_min($range='1-2'){
		return $this->rule($range);
	}

	// 分钟 多个时间点执行
	public function multiple_min($multiple='0,30'){
		return $this->rule($multiple);
	}



	// 每n小时执行
	public function huor($n = 1){
		return $this->rule("0", "*/$n");
	}

	// 小时 区间执行
	public function range_huor($range='1-2'){
		return $this->rule("0", $range);
	}

	// 小时 多个时间点执行
	public function multiple_huor($multiple='0,12'){
		return $this->rule("0", $multiple);
	}


	// 每n天执行
	public function day($n = 1){
		return $this->rule("0", "0", "*/$n");
	}

	// 天 区间执行
	public function range_day($range='1-2'){
		return $this->rule("0", "0", $range);
	}

	// 天 多个时间点执行
	public function multiple_day($multiple='0,15'){
		return $this->rule("0", "0", $multiple);
	}



	// 每n月执行
	public function mon($n = 1){
		return $this->rule("0", "0", "1", "*/$n");
	}

	// 月 区间执行
	public function range_mon($range='1-2'){
		return $this->rule("0", "0", "1", $range);
	}

	// 月 多个时间点执行
	public function multiple_mon($multiple='0,6'){
		return $this->rule("0", "0", "1", $multiple);
	}


	// 每周n执行
	public function week($n = 1){
		return $this->rule("0", "0", "*", "*","*/$n");
	}

	// 周 区间执行
	public function range_week($range='1-2'){
		return $this->rule("0", "0", "*", "*", $range);
	}

	// 周 多个时间点执行
	public function multiple_week($multiple='0,3'){
		return $this->rule("0", "0", "*", "*", $multiple);
	}





	// ------------------------------ 辅助函数 -----------------------------------

	// [内部方法] url操作
	private function _action_url($url, $id){
		if(empty($url)){
			return 'url error';
		}else{
			// 调度之后的返回值
			$keyword = $this->_curl_get_contents($url);

			$ret = DB::table(self::$table_name)->where('id', $id)->first();

			$params = array();

			if($keyword == 'fail'){
				$this->params['fail_time'] = time();
				$this->params['fail_count'] = $ret['fail_count']+1;
			}elseif($keyword == 'success'){
				$this->params['success_time'] = time();
				$this->params['success_count'] = $ret['success_count']+1;
			}else{
				$this->params['fail_time'] = time();
				$this->params['fail_count'] = $ret['fail_count']+1;
			}

			// 更新最后成功/失败 时间 以及次数
			$this->where(array('id'=>$id))->update();

			// 写入cron日志
			$this->_cron_log($ret['url'], $keyword);
			return true;
		}
	}


	// [内部方法] 日志
	private function _cron_log($url, $keyword){
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

		$ret = DB::table("task_log")->insertGetId(array('url'=>$url, 'time'=>time(), 'keyword'=>$keyword, 'state'=>$state));

		if(!$ret){
			return 'error!';
		}else{
			return true;
		}
	}

	// [内部方法] 文件操作
	private function _action_file($path){
//        $myfile = fopen("/Applications/XAMPP/xamppfiles/htdocs/web/bbframework/public/static/log.log", "a") or die("Unable to open file!");
//        $txt = date("Y:m:d H:i:s", time())."\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);
		return true;
	}

	// [内部方法] 访问url
	private function _curl_get_contents($url,$timeout=1) {
		$curlHandle = curl_init();
		curl_setopt( $curlHandle , CURLOPT_URL, $url );
		curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout );
		$result = curl_exec( $curlHandle );
		curl_close( $curlHandle );
		return $result;
	}

	// [内部方法] 调度规则函数
	private function _is_time_cron($time , $cron)
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
