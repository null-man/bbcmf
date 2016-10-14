<?php

namespace app\index\controller;

use bb\Controller;
use bb\DB;

class Index extends Controller {

	public function index() {

		echo 'demo';

	}


	public function doc(){
		$project = DB::table('project')->where(array('id'=>1))->first();

		// 所有的接口类型
		$interface_types = DB::table('interface_type')->where(array('object_id'=>1))->get();

		$show_data = array();
		foreach ($interface_types as $interface_type){
			foreach ($interface_type as $k => $v){
				if ($k == 'id'){
					$interfaces = DB::table('interface')->where(array('interface_type_id'=>$v))->get();
					$tmp = array();
					foreach ($interfaces as $interface){
						foreach ($interface as $i_k => $i_v){
							if ($i_k == 'name'){
								array_push($tmp, $i_v);
							}
						}
					}
				}

				$show_data[$interface_type['name']] = $tmp;
			}

		}


//		dump($show_data);
//		dump($project);
		// 项目
		$this->assign('project', $project);

		// 展示数据
		$this->assign('show_data', $show_data);

		return $this->fetch();
	}



}