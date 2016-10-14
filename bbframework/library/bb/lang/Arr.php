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

namespace bb\lang;

class Arr {

	// 判断数组
	public static function is($obj, $callback = null) {
		if(!is_array($obj)) return false;
		if(!empty($callback)) {
			foreach ($obj as $key => $value) {
				if(!call_user_func_array($callback, [$key, $value])) {
					return false;
				}
			}
		}
		return true;
	}

	// 判断数组
	public static function isList($arr) {
		return static::is($arr) && isset($arr[0]);
	}

	// 判断表
	public static function isMap($arr) {
		return !static::isList($arr);
	}

	// 获得数组的建名
	public static function keys(&$arr) {
		return array_keys($arr);
	}

	// 获得数组的值
	public static function values(&$arr) {
		return array_values($arr);
	}

	// 数组中的值与键名互换
	public static function flip(&$arr) {
		return array_flip($arr);
	}

	// 存在
	public static function in(&$arr, $obj) {
		return in_array($obj, $arr);
	}

	// 查找，返回键名
	public static function search(&$arr, $obj) {
		return array_search($obj, $arr);
	}

	// 存在键
	public static function exists(&$arr, $key) {
		return array_key_exists($key, $arr);
	}

	// 数组截取(不影响原数组)
	public static function sub(&$arr, $start, $length = null) {
		if(is_null($length)) {
			$length = static::size($arr);
		}
		return array_slice($arr, $start, $length);
	}

	// 数组截取(影响原数组)
	public static function subr(&$arr, $start, $length = null) {
		if(is_null($length)) {
			$length = static::size($arr);
		}
		return array_splice($arr, $start, $length, []);
	}

	// 替换
	public static function replace(&$arr, $start, $length = null, $replace = []) {
		if(is_null($length)) {
			$length = static::size($arr);
		}
		array_splice($arr, $start, $length, $replace);
		return $arr;
	}


	// 数组长度
	public static function size($arr) {
		return count($arr);
	}

	// 数组分割
	public static function split($arr, $separator, $limit = 0) {

		$ret = [];
		$size = static::size($arr);

		if(!static::is($separator)) {
			$div = $size / $separator;
			$sub = false;
		} else {
			$div = count($separator);
			$sub = true;
		}

		for($i = 0; $i < $div; $i ++) {
			$length = $sub ? intval($separator[$i]) : $separator;
			// dump($length);
			if($limit > 0 && $i >= $limit - 1) {
				$a = static::subr($arr, 0);
				$ret[] = $a;
				break;
			} else {
				$s = static::subr($arr, 0, $length, []);
				$ret[] = $s;
			}
		}

		return $ret;
	}

	// 数组填充到指定长度
	public static function pad(&$arr, $length, $value = null) {
		return array_pad($arr, $length, $value);
	}

	// 最后一个
	public static function last(&$arr) {
		return $arr[static::size($arr) - 1]; 
	}

	// 数组组合
	public static function merge($arr, $arr2, $recursive = false) {
		$args = func_get_args();
		$last = static::last($args);
		if(is_bool($last)) {
			$args = static::sub($args, 0, static::size($args) - 1);
			if($last) {
				return call_user_func_array('array_merge_recursive', $args);
			}
		}
		return call_user_func_array('array_merge', $args);
	}

	// 添加到最后
	public static function append(&$arr, $var) {
		$args = func_get_args();
		if(static::is($var)) {
			return call_user_func_array('static::merge', $args);
		} else {
			call_user_func_array('array_push', array_merge([& $arr], static::sub($args, 1)));
			return $arr;
		}
	}

	// 插入数据
	public static function insert(&$arr, $i, $var) {
		$args = func_get_args();
		$args = static::sub($args, 2);
		if($i == 0) {
			call_user_func_array('array_unshift', array_merge([& $arr], $args));
			return $arr;
		} else {
			return call_user_func_array('static::sub', array_merge([& $arr, $i, 0], $args));
		}
	}

	// 弹出数据
	public static function pop(&$arr, $i = null, $length = 1) {
		if($length == 1 && $i === 0) {
			return array_shift($arr);
		}
		if($length == 1 && $i === null) {
			return array_pop($arr);
		}

		$v = static::sub($arr, $i, $length);
		static::sub($arr, $i, $length, []);
		return $v;
	}

	// 删除数据
	public static function remove(&$arr, $i) {

		if(static::isList($arr)) {
			$arr = static::sub($arr, $i, 1, []);
		} else {
			unset($arr[$i]);
		}
		return $arr;
	}

	// 去重
	public static function toSet(&$arr) {
		return static::unique($arr);
	}

	public static function unique(&$arr) {
		return array_unique($arr);
	}


	// 遍历执行
	public static function each(&$arr, $callback, $params = []) {

		$walk = function(&$v, $k, $p) use ($callback) {

			return call_user_func_array($callback, [$k, &$v, $p]);

		};

		array_walk($arr, $walk, $params);
		return $arr;
	}

	// 生成新数组
	public static function build(&$arr, $callback = null, $withKey = true) {
		$results = [];
		foreach ($arr as $key => $value) {
			if(empty($callback)) {
				$innerKey = $key;
				$innerValue = $value;
			} else {
				if($withKey) {
					list($innerKey, $innerValue) = call_user_func($callback, $key, $value);
				} else {
					$innerKey = $key;
					$innerValue = call_user_func($callback, $key, $value);
				}
			}
			$results[$innerKey] = $innerValue;
		}
		return $results;
	}

	// 数组过滤
	public static function filter(&$arr, $callback = null) {
		if(empty($callback)) return array_filter($arr);
		$ret = [];
		foreach ($arr as $key => $value) {
			if(call_user_func_array($callback, [$key, $value])) {
				$ret[$key] = $value;
			}
		}
		return $ret;
	} 


	// 排序
	public static function sort(&$arr, $mode = false, $key = false) {
		$func = 'sort';
		if(is_bool($mode)) {
			$func = ($key ? 'a' : '') . ($mode ? 'r' : '') . $func;
		} else {
			$func = 'u' . ($key ? 'a' : '') . $func;
		}
		call_user_func_array($func, [&$arr, $mode]);
		return $arr;
	}

	// 键名排序
	public static function sortKey(&$arr, $mode = false) {
		if(is_bool($mode)) {
			return $mode ? krsort($arr) : ksort($arr);
		} else {
			return uksort($arr, $mode);
		}
	}

	// 求和
	public static function sum(&$arr) {
		return array_sum($arr);
	}

	// 差
	public static function diff(&$arr, $arr2, $key = false) {
		$func = 'array_diff';
		$args = func_get_args();
		$last = static::last($args);
		if(is_bool($last)) {
			$args = static::sub($args, 0, static::size($args) - 1);
			$func = $key ? $func . '_assoc' : $func;
		}
		return call_user_func_array($func, $args);
	}

	public static function intersect(&$arr, $arr2, $key = false) {
		$func = 'array_intersect';
		$args = func_get_args();
		$last = static::last($args);
		if(is_bool($last)) {
			$args = static::sub($args, 0, static::size($args) - 1);
			$func = $key ? $func . '_assoc' : $func;
		}
		return call_user_func_array($func, $args);
	}

	// 反向
	public static function reverse(&$arr, $key = false) {
		return array_reverse($arr, $key);
	}

	// 随机
	public static function rand(&$arr, $i = 1) {
		return array_rand($arr, $i);
	}

	// 乱序
	public static function shuffle(&$arr) {
		shuffle($arr);
		return $arr;
	}

}
