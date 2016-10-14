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

namespace bb;

use bb\net\Request;
use bb\net\Mail;
use bb\net\IP;

class Net {


	// 引擎类型
	const CURL = 0;
	const SNOOPY = 1;

	public static function request($engine = Net::SNOOPY) {
		$request = new Request();
		$request->engine($engine);
		return $request;
	}

	public static function mail() {
		return new Mail();
	}


	public static function ip($ip) {
		return new IP($ip);
	}


	public static function __callStatic($method, $params) {

		

	}

	
}