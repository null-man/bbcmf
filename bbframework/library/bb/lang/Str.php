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

class Str {

	public static function toInt($str) {
		return intval($str);
	}

	public static function toFloat($str) {
		return floatval($str);
	}


	// 转换为utf-8
    public static function convertToUtf8($str) {
        if (!self::isUtf8($str)) {
            if (function_exists('mb_convert_encoding')) {
                $str = mb_convert_encoding($str, 'UTF-8');
            } else {
                $str = utf8_encode($str);
            }
        }

        return $str;
    }

    // 是否是utf-8
    public static function isUtf8($str) {
        $length = strlen($str);

        for ($i = 0; $i < $length; $i++) {
            if (ord($str[$i]) < 0x80) {
                $n = 0;
            } elseif ((ord($str[$i]) & 0xE0) == 0xC0) {
                $n = 1;
            } elseif ((ord($str[$i]) & 0xF0) == 0xE0) {
                $n = 2;
            } elseif ((ord($str[$i]) & 0xF0) == 0xF0) {
                $n = 3;
            } else {
                return false;
            }

            for ($j = 0; $j < $n; $j++) {
                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
                    return false;
                }
            }
        }

        return true;
    }

    // URL编码
    public static function urlencode($str) {
    	return urlencode($str);
    }

    // URL解码
    public static function urldecode($str) {
    	return urldecode($str);
    }

    // 字符转换为 HTML 实体
	public static function str2html($str) {
		return htmlentities($str);
	}

	// HTML 实体转换为字符
	public static function html2str($str) {
		return html_entity_decode($str);
	}

	// 替换文本所有的'\n'或'\r\n'为'<br/>'
	public static function nl2br($str) {
		$str = str_replace("\r\n", "<br/>",$str);
		$str = str_replace("\n", "<br/>",$str);

		return $str;
	}

	// 转换文本为HTML代码
	public static function text2html($str) {
		$str  = str_replace("\t", "    ", $str);
		$str = htmlentities($str);
		$str = str_replace(" ", "&nbsp;", $str);
		$str = self::nl2br($str);

		return $str;
	}

	// 字符串是否为空
	public static function isEmpty($str) {
		return empty($str);
	}

	// 判断字符串是否为空白
	public static function isBlank($str) {
		$len = self::len($str);
		if(!isset($str) || $len == 0) {
			return true;
		}
		for($i = 0; $i < $len; $i++){
			if(!self::isWhitespaceAt($str, $i)) {
				return false;
			}
		}
		return true;
	}

	// 获得字符串指定位置的字符
	public static function at($str, $index) {
		return $str[$index];
	}

	// 判断字符串指定位置的字符是否是空白
	public static function isWhitespaceAt($str, $index) {
		$tmp_str = self::at($str, $index);
		return $tmp_str == " " or $tmp_str == "\t" or $tmp_str == "\n" or $tmp_str == "\r";
	}

	// 将字符串拆解成字符数组
	public static function toArray($str, $max_len = 0) {
		$string_array = array();

		if($max_len == 0) {
			$max_len = strlen($str);
		}

		for($i = 0; $i < $max_len; $i++){
			array_push($string_array, self::at($str, $i));
		}

		return $string_array;
	}

	// 获得字符串长度
	public static function len($str) {
		return strlen($str);
	}

	// 获得UTF8字符串的长度
	public static function lenUtf8($str){
		preg_match_all("/./us", $str, $match);
		return count($match[0]);
	}

	// 比较字符串
	public static function equals($str, $str2) {
		return $str === $str2;
	}

	// 比较字符串,忽略大小写
	public static function equalsIgnoreCase($str, $str2) {
		return strtolower($str1) === strtolower($str2);
	}

	
	// 统计str中substr出现的次数。缺省状态下from为0，to为字符串长度。
	// 成功返回统计个数，失败返回false。
	public static function countMatch($str, $substr, $from = 0, $to = 0){
		if($to == 0 || $to > self::lenUtf8($str)){
			$to = self::lenUtf8($str);
		}
		return substr_count($str, $substr, $from, $to);
	}

	// 将字符串中的小写字母转换成大写
	public static function upper($str) {
		return strtoupper($str);
	}

	// 将字符串中的大写字母转换成小写
	public static function lower($str) {
		return strtolower($str);

	}

	// 将字符串首字母变大写
	public static function capitalize($str) {
		return ucfirst($str);
	}

	// 将字符串首字母变小写
	public static function uncapitalize($str) {
		return lcfirst($str);
	}

	// 在str前面补0，使其总长度达到n，返回补充后的新串。
	// 如果str长度已经超过n，则直接返回str。
	public static function zfill($str, $len) {
		return sprintf('%0'.$len.'s', $str);
	}


	/**
	 * 填充字符串。以特定的字符进行填充
	 *
	 * @param $str 字符串
	 * @param $len 字符串长度
	 * @param $pad_str 填充字符串
	 * @param $pad_type 填充方向 STR_PAD_BOTH 两头 STR_PAD_LEFT 左 STR_PAD_RIGHT 右
	 * @return string
	 */
	public static function pad($str, $len, $pad_str, $pad_type) {
		return str_pad($str, self::$len, $pad_str, $pad_type);
	}


	/**
	 * 左填充字符串。以特定的字符进行填充
	 *
	 * @param $str
	 * @param $len 字符串长度
	 * @param $pad_str 填充字符串
	 * @return string
	 */
	public static function padLeft($str, $len, $pad_str){
		return self::pad($str, $len, $pad_str, STR_PAD_LEFT);
	}



	/**
	 * 右填充字符串。以特定的字符进行填充
	 *
	 * @param $str
	 * @param $len 字符串长度
	 * @param $pad_str 填充字符串
	 * @return string
	 */
	public static function padRight($str, $len, $pad_str){
		return self::pad($str, $len, $pad_str, STR_PAD_RIGHT);
	}



	/**
	 * 填充字符串到指定长度 填充字符左右平分 额外字符串向右填充 默认的填充字符为空格。
	 *
	 * @param $str
	 * @param $len 字符串长度
	 * @param $pad_str 填充字符串
	 * @return string
	 */
	public static function padCenter($str, $len, $pad_str){
		return self::pad($str, $len, $pad_str, STR_PAD_BOTH);
	}



	/**
	 * 获取中心处指定长度个数字符的字符串。
	 *
	 * @param $str
	 * @param $pos 起始位置 下标0 开始
	 * @param $len 长度
	 * @return string
	 */
	public static function mid($str, $pos, $len) {
		return substr($str, $pos, $len);
	}



	/**
	 * 获取最左边指定长度个数字符的字符串。
	 *
	 * @param $str
	 * @param $len 从左到右截取的长度
	 * @return string
	 */
	public static function left($str, $len) {
		$len = $len < 0 ? 0 : $len;
		return self::mid($str, 0, $len);
	}



	/**
	 * 获取最右边指定长度个数字符的字符串。
	 *
	 * @param $str
	 * @param $len 从右到左截取的长度
	 * @return string
	 */
	public static function right($str, $len){
		$len = $len < 0 ? 0 : $len;
		return substr($str, -$len);
	}


	/**
	 * 返回重复n次某字符串的串
	 *
	 * @param $str
	 * @param $num 缺省数量为2
	 * @param $sep 分割字符串的字符串
	 * @return string
	 */
	public static function repeat($str, $num = 2, $sep = ''){
		$tmp_array = array();
		$i = 0;

		$num = $num <= 0 ? 2 : $num;

		while($i < $num){
			array_push($tmp_array, $str);
			$i += 1;
		}

		return implode($sep, $tmp_array);
	}


	/**
	 * 格式化字符串
	 *
	 * 例如:
	 * %c - 接受一个数字, 并将其转化为ASCII码表中对应的字符
	 * %d, %i - 接受一个数字并将其转化为有符号的整数格式
	 * %o - 接受一个数字并将其转化为八进制数格式
	 * %u - 接受一个数字并将其转化为无符号整数格式
	 * %x - 接受一个数字并将其转化为十六进制数格式, 使用小写字母
	 * %X - 接受一个数字并将其转化为十六进制数格式, 使用大写字母
	 * %e - 接受一个数字并将其转化为科学记数法格式, 使用小写字母e
	 * %E - 接受一个数字并将其转化为科学记数法格式, 使用大写字母E
	 * %f - 接受一个数字并将其转化为浮点数格式
	 * %g(%G) - 接受一个数字并将其转化为%e(%E, 对应%G)及%f中较短的一种格式
	 * %q - 接受一个字符串并将其转化为可安全被Lua编译器读入的格式
	 * %s - 接受一个字符串并按照给定的参数格式化该字符串
	 *
	 * @param $args 不固定长度的参数 第一个为 format格式
	 * @return string
	 */
	public static function format(){
		$str = func_get_args()[0];
		$tmp_array = array();

		for($i = 1; $i < count(func_get_args()); $i++){
			array_push($tmp_array, func_get_args()[$i]);
		}

		return vsprintf($str, $tmp_array);
	}


	/**
	 * 去除字符串左侧空白
	 *
	 * @param $str
	 * @return string
	 */
	public static function ltrim($str){
		return ltrim($str);
	}



	/**
	 * 去除字符串右侧空白
	 *
	 * @param $str
	 * @return string
	 */
	public static function rtrim($str){
		return rtrim($str);
	}



	/**
	 * 去除字符串两侧空白
	 *
	 * @param $str
	 * @return string
	 */
	public static function trim($str){
		return trim($str);
	}



	/**
	 * 去除字符串两侧空白，如果结果为空字符串，则更改结果为null
	 *
	 * @param $str
	 * @return null|string
	 */
	public static function trimToNull($str){
		return self::isBlank($str) ? null : self::trim($str);
	}


	/**
	 * 去除字符串两侧空白，如果结果为空字符串，则更改结果为空字符串
	 *
	 * @param $str
	 * @return string
	 */
	public static function trimToEmpty($str){
		return self::isBlank($str) ? "" : self::trim($str);
	}


	/**
	 * 去除字符串左侧指定字符
	 *
	 * @param $str
	 * @param $strip_chars 当指定字符为空时 去除空白
	 * @return string
	 */
	public static function stripStart($str, $strip_chars = '') {
		$tmp_str = self::isEmpty($strip_chars) ? ltrim($str) : $str;
		return ltrim($tmp_str, $strip_chars);
	}


	/**
	 * 去除字符串右侧指定字符
	 *
	 * @param $str
	 * @param $strip_chars 当指定字符为空时 去除空白
	 * @return string
	 */
	public static function stripEnd($str, $strip_chars = ''){
		$tmp_str = self::isEmpty($strip_chars) ? rtrim($str) : $str;
		return rtrim($tmp_str, $strip_chars);
	}



	/**
	 * 去除字符串两侧指定字符
	 *
	 * @param $str
	 * @param $strip_chars 当指定字符为空时 去除空白
	 * @return string
	 */
	public static function strip($str, $strip_chars = ''){
		$tmp_str = self::stripStart($str, $strip_chars);
		return self::stripEnd($tmp_str, $strip_chars);
	}



	/**
	 * 去掉字符串中所有的空格
	 *
	 * @param $str
	 * @return mixed
	 */
	public static function removeBlank($str){
		return str_replace(" ","",$str);
	}



	/**
	 * 使用空格替换字符串中的制表符
	 *
	 * @param $str
	 * @param int $num 空格个数, 默认空格个数为8
	 * @return mixed
	 */
	public static function expendTabs($str, $num = 8){
		return str_replace("\t", self::repeat(" ", $num), $str);
	}



	/**
	 * 字符串截取
	 *
	 * @param $str
	 * @param $delim 分隔符
	 * @param int $max_num 最大返回数量
	 * @return array
	 */
	public static function split($str, $delim, $max_num = 0) {

		$tmp_array = explode($delim, $str);

		if($max_num === 0){
			return $tmp_array;
		}else{
			$str_array = array();

			$max_num = $max_num > count($tmp_array) ? count($tmp_array) : $max_num;

			for($i = 0; $i < $max_num; $i++){
				array_push($str_array,$tmp_array[$i]);
			}

			return $str_array;
		}
	}



	/**
	 * 将字符串以某种分隔符进行拆解，返回分解后子串的集合
	 *
	 * @param $str
	 * @param $substr 分隔符
	 * @return string
	 */
	public static function partition($str, $substr) {
		$str_array = explode($substr, $str, 2);
		return $str_array[0] . '  ' . $substr . '  ' . $str_array[1];
	}



	/**
	 * 按参数要求分解一个数据
	 *
	 * @params 不定参数
	 * @return string
	 */
	public static function analysis() {
		$str = func_get_args()[0];
		$tmp_array = array();

		$start_index    = 0;
		$end_index      = 0;

		for($i = 1; $i < count(func_get_args()); $i++) {
			$end_index += func_get_args()[$i];

			$len = $end_index - $start_index;

			$tmp_str = substr($str, $start_index, $len);

			if($tmp_str != "" || $tmp_str!= false) {
				array_push($tmp_array, $tmp_str);
			}

			$start_index += func_get_args()[$i];
		}

		return join(",", $tmp_array);
	}


	/**
	 * 返回字符串的词集合的数组
	 *
	 * @param $str
	 * @return array
	 */
	public static function words($str) {

		return explode(" ",$str);
	}



	/**
	 * 获得字符串某个子串的起始首个位置索引 如果没有找到字符串则返回 false
	 * 区分大小写
	 *
	 * @param $str 字符串
	 * @param $substr 要查找的字符
	 * @param $start 开始搜索的索引
	 * @return bool|int
	 */
	public static function indexOf($str, $substr, $start = 0) {
		return strpos($str, $substr, $start);
	}



	/**
	 * 获得字符串某个子串的起始首个位置索引 如果没有找到字符串则返回 false
	 * 不区分大小写
	 *
	 * @param $str
	 * @param $substr 要查找的字符
	 * @param $start 开始搜索的索引
	 * @return int
	 */
	public static function indexOfIgnoreCase($str, $substr, $start){
		return stripos($str, $substr, $start);
	}



	/**
	 * 获得字符串某个子串的第N个的位置索引
	 *
	 * @param $str
	 * @param $substr 查找的子字符串
	 * @param int $ordinal 查找第几个位置 缺省为第一个
	 * @param int $start 起始位置 缺省为字符串开头
	 * @param bool $is_reverse 是否降序
	 * @return mixed
	 */
	public static function ordinalIndexOf($str, $substr, $ordinal = 1, $start = 0, $is_reverse = false){
		$j = $start;
		$tmp_array = array();
		$count = substr_count($str, $substr, $start);

		for($i = 0; $i < $count; $i++){
			$j = self::indexOf($str, $substr, $j);
			array_push($tmp_array, $j);
			$j += 1;
		}

		if($is_reverse === true) $tmp_array = array_reverse($tmp_array);

		return $tmp_array[$ordinal - 1];
	}



	/**
	 * 获得字符串某个子串的结尾首个位置索引
	 * 区分大小写
	 *
	 * @param $str
	 * @param $substr 查找的子字符串
	 * @param $start 开始搜索的索引
	 * @return bool|int
	 */
	public static function lastIndexOf($str, $substr, $start){
		return strrpos($str, $substr, $start);
	}



	/**
	 * 获得字符串某个子串的结尾首个位置索引
	 * 不区分大小写
	 *
	 * @param $str
	 * @param $substr 查找的子字符串
	 * @param $start 开始搜索的索引
	 * @return bool|int
	 */
	public static function lastIndexOfIgnoreCase($str, $substr, $start){
		return strripos($str, $substr, $start);
	}



	/**
	 * 获得字符串某个子串的第N个的位置索引 倒序查找
	 *
	 * @param $str
	 * @param $substr 查找的子字符串
	 * @param int $ordinal 查找第几个位置 缺省为第一个
	 * @param int $start 起始位置 缺省为字符串开头
	 * @return mixed
	 */
	public static function lastOrdinalIndexOf($str, $substr, $ordinal = 1, $start = 0){
		return self::ordinalIndexOf($str, $substr, $ordinal, $start, true);
	}



	/**
	 * 匹配字符串中的字符
	 *
	 * @param $str
	 * @param $substr 需要查找的字符串
	 * @return mixed 最小的索引
	 */
	public static function indexOfStr($str, $substr) {
		$len = self::len($substr);
		$first_index = array();

		for($i = 0; $i < $len; $i++){
			array_push($first_index, self::indexOf($str, $substr[$i], 0));
		}

		sort($first_index);

		return $first_index[0];
	}



	/**
	 * 匹配字符串数组中的字符串
	 *
	 * @param $str
	 * @param $substr_array 需要查找的字符串的数组
	 * @return mixed 最小的索引
	 */
	public static function indexOfAny($str, $substr_array) {
		$first_index = array();

		foreach($substr_array as $key => $substr){
			array_push($first_index, self::indexOf($str, $substr, 0));
		}

		sort($first_index);

		return $first_index[0];
	}


	/**
	 * 判断字符串是否包含某字符串
	 * 区分大小写
	 *
	 * @param $str
	 * @param $substr 需要查找的字符串
	 * @return bool
	 */
	public static function contains($str, $substr){
		$index = self::indexOf($str, $substr, 0);

		$is_contains = false;

		if ($index !== false && $index >= 0) $is_contains = true;

		return $is_contains;
	}



	/**
	 * 判断字符串是否包含某字符串
	 * 不区分大小写
	 *
	 * @param $str
	 * @param $substr 需要查找的字符串
	 * @return bool
	 */
	public static function containsIgnoreCase($str, $substr){
		return self::contains(self::lower($str), self::lower($substr));
	}



	/**
	 *  判断字符串是否包含空白
	 *
	 * @param $str
	 * @return bool
	 */
	public static function containsWhitespace($str){
		$len = self::len($str);

		for($i = 0; $i < $len; $i++){
			if(self::isWhitespaceAt($str, $i)){
				return true;
			}
		}

		return false;
	}



	/**
	 * 判断字符串是否包含字符串中的任意一个字符
	 *
	 * @param $str
	 * @param $substr 需要查找的字符串
	 * @return bool
	 */
	public static function containsAnyStr($str, $substr) {
		$len = self::len($substr);

		for($i = 0; $i < $len; $i++){
			if(self::contains($str, $substr[$i]) === true){
				return true;
			}
		}

		return false;
	}



	/**
	 * 判断字符串是否包含字符串数组中的任意一个字符串
	 *
	 * @param $str
	 * @param $substr_array 需要查找的字符串的数组
	 * @return bool
	 */
	public static function containsAny($str, $substr_array){
		foreach($substr_array as $key => $substr){
			if(self::contains($str, $substr) === true){
				return true;
			}
		}

		return false;
	}



	/**
	 * 判断字符串是否以某字符串开头
	 *
	 * @param $str
	 * @param $prefix 查找的子字符串
	 * @param bool|false $ignore_case 忽略大小写 默认不忽略
	 * @return bool
	 */
	public static function startsWith($str, $prefix, $ignore_case = false){
		if($ignore_case === true){
			$prefix = self::lower($prefix);
			$str    = self::lower($str);
		}

		$index = self::indexOf($str, $prefix, 0);

		$is_contains = false;

		if ($index !== false && $index === 0) $is_contains = true;

		return $is_contains;
	}



	/**
	 * 判断字符串是否以某字符串结尾
	 *
	 * @param $str
	 * @param $suffix 查找的子字符串
	 * @param bool|false $ignore_case 忽略大小写
	 * @return bool
	 */
	public static function endsWith($str, $suffix, $ignore_case = false){
		if($ignore_case === true){
			$suffix = self::lower($suffix);
			$str    = self::lower($str);
		}

		$tmp_len = self::len($str) - self::lastIndexOf($str, $suffix, 0);

		return $tmp_len === self::len($suffix) ? true : false;
	}


	/**
	 * 获得子字符串，根据起始位置与结束位置来获得
	 *
	 * @param $str
	 * @param $start 子串起始索引
	 * @param $end 子串结束索引
	 * @return string
	 */
	public static function substr($str, $start = 0, $end){
		$start = $start >= 0 ? $start : self::len($str) - abs($start);
		$end = self::isEmpty($end) ? self::len($str) - 1 : $end;
		$end = $end >= 0 ? $end : self::len($str) - abs($end);

		$len = $end - $start + 1;

		return substr($str, $start, $len);
	}



	/**
	 * 获得子字符串，在分隔串第一次出现之前的子字符串
	 *
	 * @param $str
	 * @param $separator 分隔符
	 * @return mixed
	 */
	public static function substrBefore($str, $separator){
		$tmp_array = explode($separator, $str);
		return $tmp_array[0];
	}


	/**
	 * 获得子字符串，在分隔串最后一次出现之前的子字符串
	 *
	 * @param $str 字符串
	 * @param $separator 分隔符
	 * @return string 处理后的字符串
	 */
	public static function substrBeforeLast($str, $separator){
		$index = self::lastIndexOf($str, $separator, 0);
		if($index !== false && $index >= 0) {
			return substr($str, 0, $index - 1);
		}

		return false;
	}


	/**
	 * 获得子字符串，在分隔串第一次出现之后的子字符串
	 *
	 * @param $str 字符串
	 * @param $separator 分隔符
	 * @return string 处理后的字符串
	 */
	public static function substrAfter($str, $separator){
		$index = self::indexOf($str, $separator, 0);
		if($index !== false && $index >= 0){
			return substr($str, $index + self::len($separator));
		}

		return false;
	}



	/**
	 * 获得子字符串，在分隔串最后一次出现之后的子字符串
	 *
	 * @param $str 字符串
	 * @param $separator 分隔符
	 * @return string 处理后的字符串
	 */
	public static function substrAfterLast($str, $separator){
		$index = self::lastIndexOf($str, $separator, 0);
		if($index !== false && $index >= 0){
			return substr($str, $index + self::len($separator));
		}

		return false;
	}



	/**
	 * 获得子字符串，从起始位置开始在指定文本两个给定文本之间的子字符串
	 *
	 * @param $str 字符串
	 * @param $open 分隔串起始
	 * @param $close 分隔串结束
	 * @return string 处理后的字符串
	 */
	public static function substrBetween($str, $open, $close = null){
		if($close === null) $close = $open;
		$tmp_str = $str;
		$open_index = self::indexOf($str, $open, 0);

		if($open_index === false || $open_index < 0){
			return false;
		}

		$str = substr($str,$open_index + self::len($open));
		$close_index = self::indexOf($str, $close, 0);

		if($close_index === false || $close_index < 0){
			return false;
		}

		return substr($tmp_str, $open_index + self::len($open), $close_index);
	}



	/**
	 * 获得子字符串，从结束位置往前在指定文本两个给定文本之间的子字符串
	 *
	 * @param $str 字符串
	 * @param $open 分隔串起始
	 * @param $close 分隔串结束
	 * @return string 处理后的字符串
	 */
	public static function substrBetweenLast($str, $open, $close = null){
		if($close === null) $close = $open;
		$tmp_str        = $str;
		$close_index    = self::lastIndexOf($str, $close, 0);

		if($close_index === false || $close_index < 0){
			return false;
		}

		$close_index = $close_index === 0 ? $close_index : $close_index - 1;

		$str           = substr($str, 0, $close_index);
		$open_index    = self::lastIndexOf($str, $open, 0);

		if($open_index === false || $open_index < 0){
			return false;
		}

		return substr($tmp_str, $open_index + self::len($open), $close_index);
	}


	/**
	 * 检索字符串中的子字符串由一个开始和结束标记进行分隔，返回所有匹配的子串为一个数组
	 *
	 * @param $str 字符串
	 * @param $open 分隔串起始
	 * @param $close 分隔串结束
	 * @return array 子字符串集
	 */
	public static function substrsBetween($str, $open, $close){
		$j = 0;
		$tmp_array = array();
		$count = substr_count($str, $open, 0);

		for($i = 0; $i < $count; $i++){
			$str = substr($str, $j);
			$j = self::indexOf($str, $open, $j);
			$tmp_str = self::substrBetween($str, $open, $close);

			if($tmp_str !== false){
				array_push($tmp_array, self::substrBetween($str, $open, $close));
			}

			$j += 1;
		}

		return $tmp_array;
	}


	/**
	 * 使用分隔符连接字符串中的每个字符，返回连接后的新串
	 *
	 * @param $str 字符串
	 * @param $separator 分隔串
	 * @return string 处理后的字符串
	 */
	public static function join($str, $separator){
		$tmp_array = array();

		for($i = 0; $i< self::len($str); $i++){
			array_push($tmp_array, $str[$i]);
		}

		return join($separator, $tmp_array);
	}


	/**
	 * 判断字符串是否仅由字母或数字组成
	 *
	 * @param $str 字符串
	 * @return bool
	 */
	public static function isAlphaNumber($str){
		if(!isset($str) || $str === ""){
			return false;
		}

		for($i = 0; $i< self::len($str); $i++){
			$tmp_str = $str[$i];

			if(!(($tmp_str >= 'a' && $tmp_str <= 'z') ||
					($tmp_str >= 'A' && $tmp_str <= 'Z') ||
					($tmp_str >= '0' && $tmp_str <= '9'))){
				return false;
			}
		}

		return true;
	}



	/**
	 * 判断字符串是否仅由字母组成
	 *
	 * @param $str
	 * @return bool
	 */
	public static function isAlpha($str){
		if(!isset($str) || $str === ""){
			return false;
		}

		for($i = 0; $i< self::len($str); $i++){
			$tmp_str = $str[$i];

			if(!(($tmp_str >= 'a' && $tmp_str <= 'z') || ($tmp_str >= 'A' && $tmp_str <= 'Z'))){
				return false;
			}
		}

		return true;
	}



	/**
	 * 判断字符串是否仅由数字组成
	 *
	 * @param $str
	 * @return bool
	 */
	public static function isNumber($str){
		if(!isset($str)){
			return false;
		}

		for($i = 0; $i< self::len($str); $i++){
			$tmp_str = $str[$i];

			if(!($tmp_str >= '0' && $tmp_str <= '9')){
				return false;
			}
		}

		return true;
	}


	/**
	 * 判断字符串是否仅由小写字母组成
	 *
	 * @param $str
	 * @return bool
	 */
	public static function isLower($str) {
		if(!isset($str) || $str === ""){
			return false;
		}

		for($i = 0; $i< self::len($str); $i++){
			$tmp_str = $str[$i];

			if(!(($tmp_str >= 'a' && $tmp_str <= 'z'))){
				return false;
			}
		}

		return true;
	}


	/**
	 * 判断字符串是否仅由大写字母组成
	 *
	 * @param $str
	 * @return bool
	 */
	public static function isUpper($str) {
		if(!isset($str) || $str === ""){
			return false;
		}

		for($i = 0; $i< self::len($str); $i++){
			$tmp_str = $str[$i];

			if(!(($tmp_str >= 'A' && $tmp_str <= 'Z'))){
				return false;
			}
		}

		return true;
	}

	/**
	 * 将二进制转换为十六进制字符串
	 *
	 * @param $str
	 * @return string
	 */
	public static function bin2hex($str) {
		return bin2hex($str);
	}


	/**
	 * 把字符串按照 PHP 代码来计算
	 *
	 * @param $str
	 * @return mixed
	 */
	public static function reflect($str) {
		return eval($str);
	}


	/**
	 * 格式化字符串，将字符串变为以千分隔形式
	 * 该函数支持一个、两个或四个参数（不是三个）。
	 *
	 * @param $num
	 * @param $decimals 规定多少个小数，如果设置了该参数，则使用点号 (.) 作为小数点来格式化数字
	 * @param $decimalpoint 规定用作小数点的字符串。
	 * @param $separator 规定用作千位分隔符的字符串。
	 * @return string
	 */
	public static function numberFormat($num, $decimals, $decimalpoint, $separator){
		return number_format($num, $decimals, $decimalpoint, $separator);
	}
}