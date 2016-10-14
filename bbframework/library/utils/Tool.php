<?php

namespace util;

BT('bb/Singleton');

class Tool {

	use \bt\bb\Singleton;

	protected function baby() {
		echo 'babybus';
	}

	protected function test(){

	}
}
