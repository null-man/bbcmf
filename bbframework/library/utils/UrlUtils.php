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


class UrlUtils {
	
	// +----------------------------------------------------------------------
	// | 加密/解密
	// +----------------------------------------------------------------------

	/**
	 * BASE64 加密
	 *
	 * @param $data 需要加密的数据
	 * @return string
	 */
	public static function base64_encode($data){
		return base64_encode ($data);
	}



	/**
	 * BASE64 解密
	 *
	 * @param $data 需要解密的数据
	 * @return string
	 */
	public static function base64_decode($data){
		return base64_decode($data);
	}



	/**
	 * url encode
	 *
	 * @param $url
	 * @return string
	 */
	public static function url_encode($url){
		return urlencode($url);
	}



	/**
	 * url decode
	 *
	 * @param $url
	 * @return string
	 */
	public static function url_decode($url){
		return urldecode($url);
	}
}
