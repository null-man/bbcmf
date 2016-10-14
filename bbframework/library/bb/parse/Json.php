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

namespace bb\parse;

use bb\Parse;

class Json extends Parse {

	protected $options = [
		'assoc' => true,		// 当该参数为TRUE时，decode将返回array而非object
	];


	public function encode($data, $options = []) {
		$data = parent::encode($data, $options);
		return json_encode($data);
	}

	public function decode($data, $options = []) {
		$data = parent::decode($data, $options);
		return json_decode($data, $this->options['assoc']);
	}

}