<?php
// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZergL <lin029011@163.com>
// +----------------------------------------------------------------------

namespace bb\controller;

use bb\Controller;
use bb\form\Form;

class FormController extends Controller {

	protected $tmpl = null;


	public function form() {
		return null;
	}

	public function submit() {

	}

	public function index() {
		if(IS_GET) {
			$this->assign('json_url', U('index_json'));
        	return $this->fetch('/tmpl');
		}
		if(IS_POST) {
			return $this->submit();
		}
	}

	public function index_json() {
		$form = $this->form();
		return json_encode($form);
	}

	protected function view() {
        $template = C('template');
        $sp = STATIC_PATH . C('admin.static') . DS;
        if(!is_null($this->tmpl)) {
        	$sp = $this->tmpl;
        }
        $template['view_path'] = '.' . $sp;
        $replace = C('view_replace_str');
        $replace['"./'] = '"' . $sp;
        return \think\View::instance($template, $replace);
    }


}
