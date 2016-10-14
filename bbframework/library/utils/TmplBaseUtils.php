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

class TmplBaseUtils {
	// 参数数组
	protected $options = [];
	
	// 表格
	public function table(){
		$this->options['view_type'] = 'table';
		return $this;
	}

	// 添加
	public function add(){
		$this->options['view_type'] = 'add';
		return $this;
	}

	// 编辑
	public function edit(){
		$this->options['view_type'] = 'edit';
		return $this;
	}

	// head
	public function head($head){
		$this->options['structure']['head'] = $head;
		return $this;
	}

	// 提交按钮url
	public function submit_url($url){
		$this->options['structure']['head']['submitUrl'] = $url;
		return $this;
	}

	// 头内容
	public function head_content($content){
		$arr = $this->_init_arr('content',$this->options['structure']['head']);

		$push_content['name'] = $content[0];
		$push_content['url'] = $content[1];
		$push_content['checkActive'] = $content[2];

		array_push($arr, $push_content);
		$this->options['structure']['head']['content'] = $arr;
		return $this;
	}

	// 隐藏 类型
	public function body_hidden($body, $value = ''){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'hidden';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;
		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 文本 类型
	public function body_text($body, $value = ''){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'text';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;
		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 单选框 类型
	public function body_radio($body, $value){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'radio';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 下拉框 类型
	public function body_select($body, $value){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'select';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 多选框 类型
	public function body_checkbox($body, $value){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'checkbox';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 文件 类型
	public function body_file($body, $format = ''){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'file';
		$push_body['format'] = empty($format) ? array('png', 'jpg', 'gif', 'pdf', 'doc', 'xls', 'php') : $format;
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 日期 类型
	public function body_date($body , $value= ''){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'date';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] = $value;


		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 邮件类型
	public function body_email($body, $value = ''){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'email';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 调度控件
	public function body_crontab($body, $value = ''){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'crontab';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 文本域 富文本
	public function body_textarea($body, $is_textarea = true, $value = ''){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'textarea';
		$push_body['type'] = $is_textarea ? 'textarea' : '';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] = $value;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// 内容块 文本
	public function body_block_text($name, $body, $value = ''){
		$arr  = $this->_init_arr('block', $this->options['structure']);
		$arr2 = $this->_init_arr($name, $arr);

		$push_body['el'] 		= 'input';
		$push_body['type'] 		= 'text';
		$push_body['name'] 		= $body[0];
		$push_body['showName'] 	= $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] 	= $value;

		array_push($arr2, $push_body);

		$this->options['structure']['block'][$name] = $arr2;
		return $this;
	}

	// 内容块 crontab
	public function body_block_crontab($name, $body, $value = ''){
		$arr  = $this->_init_arr('block', $this->options['structure']);
		$arr2 = $this->_init_arr($name, $arr);

		$push_body['el'] 		= 'input';
		$push_body['type'] 		= 'crontab';
		$push_body['name'] 		= $body[0];
		$push_body['showName'] 	= $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] 	= $value;

		array_push($arr2, $push_body);

		$this->options['structure']['block'][$name] = $arr2;
		return $this;
	}

	// 内容块 下拉框
	public function body_block_select($name, $body, $value = ''){
		$arr  = $this->_init_arr('block', $this->options['structure']);
		$arr2 = $this->_init_arr($name, $arr);

		$push_body['el'] 		= 'select';
		$push_body['name'] 		= $body[0];
		$push_body['showName'] 	= $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] 	= $value;

		array_push($arr2, $push_body);

		$this->options['structure']['block'][$name] = $arr2;
		return $this;
	}

	// 内容块 多选框
	public function body_block_checkbox($name, $body, $value){
		$arr  = $this->_init_arr('block', $this->options['structure']);
		$arr2 = $this->_init_arr($name, $arr);

		$push_body['el'] 		= 'input';
		$push_body['type'] 		= 'checkbox';
		$push_body['name'] 		= $body[0];
		$push_body['showName'] 	= $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] 	= $value;

		array_push($arr2, $push_body);

		$this->options['structure']['block'][$name] = $arr2;
		return $this;
	}

	// 多对一 配置
//	public function many2one_config($configs = array()){
//		$arr  = $this->_init_arr('manyToOne', $this->options['structure']);
//
//		foreach ($configs as $key => $config){
//			$this->options['structure']['manyToOne'][$key] = $config;
//		}
//
//		return $this;
//	}

	// 多对一 隐藏
	public function many2one_hidden($block_name, $name, $body, $value){
		$arr  = $this->_init_arr('manyToOne', $this->options['structure']);
		$arr1  = $this->_init_arr($block_name, $arr);
		$arr2 = $this->_init_arr($name, $arr1);

		$push_body['el'] = 'input';
		$push_body['type'] = 'hidden';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;
		$push_body['value'] = $value;

		array_push($arr2, $push_body);

		$this->options['structure']['manyToOne'][$block_name][$name] = $arr2;
		return $this;

	}

	// 多对一 文本
	public function many2one_text($block_name, $name, $body, $value){
		$arr  = $this->_init_arr('manyToOne', $this->options['structure']);
		$arr1  = $this->_init_arr($block_name, $arr);

		$arr2 = $this->_init_arr($name, $arr1);

		$push_body['el'] 		= 'input';
		$push_body['type'] 		= 'text';
		$push_body['name'] 		= $body[0];
		$push_body['showName'] 	= $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] 	= $value;

		array_push($arr2, $push_body);

		$this->options['structure']['manyToOne'][$block_name][$name] = $arr2;
		return $this;

	}

	// 多对一 下拉框
	public function many2one_select($block_name, $name, $body, $value){
		$arr  = $this->_init_arr('manyToOne', $this->options['structure']);
		$arr1  = $this->_init_arr($block_name, $arr);

		$arr2 = $this->_init_arr($name, $arr1);

		$push_body['el'] 		= 'select';
		$push_body['name'] 		= $body[0];
		$push_body['showName'] 	= $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] 	= $value;

		array_push($arr2, $push_body);

		$this->options['structure']['manyToOne'][$block_name][$name] = $arr2;
		return $this;
	}


	// 多对一 多选框
	public function many2one_checkbox($block_name, $name, $body, $value){
		$arr  = $this->_init_arr('manyToOne', $this->options['structure']);
		$arr1  = $this->_init_arr($block_name, $arr);

		$arr2 = $this->_init_arr($name, $arr1);

		$push_body['el'] 		= 'input';
		$push_body['type'] 		= 'checkbox';
		$push_body['name'] 		= $body[0];
		$push_body['showName'] 	= $body[1];
		$push_body['isCheck'] = isset($body[2]) ? $body[2] : false;
		$push_body['disabled'] = isset($body[3]) ? $body[3] : false;

		$push_body['value'] 	= $value;

		array_push($arr2, $push_body);

		$this->options['structure']['manyToOne'][$block_name][$name] = $arr2;
		return $this;
	}



	// 提交按钮
	public function body_submit($body){
		$arr = $this->_init_arr('body', $this->options['structure']);

		$push_body['el'] = 'input';
		$push_body['type'] = 'submit';
		$push_body['name'] = $body[0];
		$push_body['showName'] = $body[1];
		$url = isset($body[2]) ? $body[2] : '';
		$push_body['url'] = $url;

		array_push($arr, $push_body);
		$this->options['structure']['body'] = $arr;
		return $this;
	}

	// table 批量操作
	public function table_operation($operation){
		$arr = $this->_init_arr('operation',$this->options['structure']);

		$push_operation['type'] = 'link';
		$push_operation['name'] = $operation[0];
		$push_operation['url'] = $operation[1];

		$push_operation['modal']['type'] = isset($operation[2][0]) ? $operation[2][0] : 'redirect';
		$push_operation['modal']['title'] = isset($operation[2][1]) ? $operation[2][1] : '';
		$push_operation['modal']['desc'] = isset($operation[2][2]) ? $operation[2][2] : '';

		$push_operation['opType'] = isset($operation[3]) ? $operation[3] : '';


		array_push($arr, $push_operation);

		$this->options['structure']['operation'] = $arr;
		return $this;
	}

	//action 单行操作
	public function table_action($params, $value = []){
		$action_arr['type'] = 'link';
		$action_arr['showName'] = $params[0];
		$action_arr['opType'] = '';

		$action_arr['url'] = $params[1];

		$action_arr['modal']['type'] = isset($params[2][0]) ? $params[2][0] : 'redirect';
		$action_arr['modal']['title'] = isset($params[2][1]) ? $params[2][1] : '';
		$action_arr['modal']['desc'] = isset($params[2][2]) ? $params[2][2] : '';

		$action_arr['modal']['value'] = $value;

		return $action_arr;
	}

	// table 表头
	public function table_body_thead($thead){
		$body = $this->_init_arr('body',$this->options['structure']);
		$arr = $this->_init_arr('thead', $body);

		foreach ($thead as $value){
			$push_name['name'] = $value;
			array_push($arr, $push_name);
		}

		$this->options['structure']['body']['thead'] = $arr;
		return $this;
	}

	// table 数据
	public function table_body_tbody($tbody){
		$this->options['structure']['body']['tbody'] = $tbody;
		return $this;
	}
	
	// table 数据对应关系
	public function table_body_relation($name, $type, $value){
		$push_relation['name'] = $type;
		$push_relation['value'] = $value;

		$this->options['structure']['relation'][$name] = $push_relation;
		return $this;
	}

	// table 过滤
	public function table_body_filter($name, $show_name, $type, $value = ""){
		$push_filter['show_name'] = $show_name;
		$push_filter['type'] = $type;
		$push_filter['value'] = $value;

		$this->options['structure']['filter']['filterItm'][$name] = $push_filter;
		return $this;
	}

	// table 过滤提交url
	public function table_body_filter_url($url = ''){
		$this->options['structure']['filter']['submitUrl'] = $url;
		return $this;
	}

	// table 分页
	public function table_body_page($total_page, $current_page, $url){
		$push_page['total_page'] = $total_page;
		$push_page['current_page'] = $current_page;
		$push_page['url'] = $url;

		$this->options['structure']['page'] = $push_page;
		return $this;
	}

	// body
	public function body($body){
		$this->options['structure']['body'] = $body;
		return $this;
	}

	// 组装完成
	public function done(){
		return $this->options;
	}


	/**
	 * 辅助方法 - 判断在该数组是否存在这个索引, 存在则返回这个索引, 不存在, 则创建再返回
	 *
 	 * @param $index 索引
	 * @param $arr 数组
	 * @return array
	 */
	private function _init_arr($index, $arr){
		return array_key_exists($index, $arr) ? $arr[$index] : array();
	}
}
