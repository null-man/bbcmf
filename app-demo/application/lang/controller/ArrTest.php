<?php
namespace app\lang\controller;

use bb\Controller;

use bb\lang\Arr;
use bb\lang\Func;
use bb\O;

class ArrTest extends Controller {

	public function index() {

		$arr = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

		$list = [4, 1, 2, 3];

		// dump(O::b($list)->sub(1, 2)->pad(10, 'abc')->isList()->get());


		// return;

		// dump(Arr::isList($list));
		// dump(Arr::flip($arr));
		// dump(Arr::keys($arr));
		// dump(Arr::values($arr));
		// dump(Arr::in($arr, 1));
		// dump(Arr::search($arr, 1));
		// dump(Arr::exists($arr, 'a'));
		// dump(Arr::size($arr));
		// dump(Arr::merge($arr, $list, true));
		// dump(Arr::sub($list, 1, 3));
		// dump(Arr::replace($list, 1, 1, []));
		dump(Arr::split($arr, [2, 1, 1]));
		// dump(Arr::pad($arr, 5));

		// dump(Arr::append($list, 'as', 'ddd'));
		// dump(Arr::insert($list, 2, 'ddd'));
		// dump(Arr::pop($arr, 0, 1));

		// dump(Arr::remove($list, 2));

		// dump(Arr::each($arr, [$this, 'each']));

		// dump(Arr::build($arr, function($key, $value) {
		// 	return 'ccc';
		// }, false));

		// dump(Arr::filter($arr, function($key, $value) {
		// 	if($value > 2) {
		// 		return false;
		// 	}
		// 	return true;
		// }));

		// 
		// dump(Arr::sort($list, function($a, $b) {
		// 	if($a == 1) return -1;
		// 	return 1;
		// }, true));

		// dump(Arr::sum($list));

		// dump(Arr::diff($list, $arr));

		// dump(Arr::intersect($list, $arr));

		// dump(Arr::reverse($list));

		// dump(Arr::rand($list, 3));

		// dump(Arr::shuffle($list));

		// dump(Arr::split($arr, 2));
		// dump(O::b($list)->split([2, 1, 1], 2)->get());

	}

	public function each($key, &$value) {
		$value = 1;
	}

	public function build($var) {
		if($var > 2) {
			return false;
		}
		return true;
	}



}