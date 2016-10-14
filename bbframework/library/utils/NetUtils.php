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

class NetUtils {

	// +----------------------------------------------------------------------
	// | mail
	// +----------------------------------------------------------------------

	/**
	 * 发送邮件
	 * 需要邮箱设置SMTP权限
	 *
	 * @param $to 收件人 邮箱
	 * @param $username 发件人 邮箱帐号
	 * @param $password 发件人 邮箱密码
	 * @param $from_name 发件人 用户名
	 * @param string $subject 邮件主题
	 * @param string $body 邮件内容
	 * @return bool 成功 true 失败 false
	 */
	public static function send_mail($to, $username, $password, $from_name, $subject = '', $body = ''){
		include(BB_PATH . "library/third/class.phpmailer.php");
		$mail             = new \PHPMailer();
//		$body            = eregi_replace("[\]",'',$body);

		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPKeepAlive = true;
		$mail->Host       = "smtp.163.com";
		$mail->Port       = 25;

		$mail->Username   = $username;
		$mail->Password   = $password;

		$mail->From       = $username;
		$mail->FromName   = $from_name;
		$mail->Subject    = $subject;
		$mail->AltBody    = $body;
		$mail->WordWrap   = 50; // set word wrap
		$mail->MsgHTML($body);

		$mail->AddReplyTo($username, $from_name);

		$mail->AddAddress($to, '');
		$mail->IsHTML(true);

		if(!$mail->Send()) {
       // $mail->ErrorInfo;
			return false;
		} else {
			return true;
		}
	}



	// +----------------------------------------------------------------------
	// | 模拟浏览器请求
	// +----------------------------------------------------------------------

	/**
	 * curl 模拟浏览器请求
	 *
	 * @param $url 请求的url
	 * @param string $method 请求类型
	 * @param array $params POST方法的参数
	 * @return mixed
	 */
	public static function curl($url, $method = 'GET', $data = [], $header = [], $cookie = []) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		// 获取数据返回
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// 是否输出头信息
		curl_setopt($ch, CURLOPT_HEADER, false);
		// 设置信息头
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		// 设置cookie
		// curl_setopt($ch, CURLOPT_COOKIE, $cookie);

	//    dump($cookie);
	//    dump($headers);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		switch($method){
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				// $data = json_decode($data, true);
				// dump(http_build_query($data));
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				break;
			case 'GET':
				curl_setopt($ch, CURLOPT_POST, false);
				break;
		}

		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}



	/**
	 * snoopy模拟浏览器请求
	 *
	 * @param $url 请求的url
	 * @param $method 请求类型
	 * @param $data POST的参数
	 * @param $header 头文件
	 * @param $cookie cookie
	 * @return $this|bool|string
	 */
	public static function snoopy($url, $method = 'GET', $data = [], $header = [], $cookie = []) {
		require_once BB_PATH.'library/third/Snoopy.class.php';
		$client = new \Snoopy();

		// $headers = array();
		// foreach(explode('$', $header) as $x) {
		// 	$x = trim($x);
		// 	if(strpos($x, ':') != False) {
		// 		$t = explode(':', $x, 2);
		// 		$headers[$t[0]] = $t[1];
		// 	}
		// }
		// $client->headers = $headers;
		foreach($header as $k => $v) {
			$client->headers[$k] = $v;
		}

		// $session = array();
		// foreach(explode(';', $cookie) as $x) {
		// 	$x = trim($x);
		// 	if(strpos($x, '=') != False) {
		// 		$t = explode('=', $x, 2);
		// 		$session[$t[0]] = $t[1];
		// 	}
		// }

		// foreach($session as $k => $v) {
		// 	$client->cookies[$k] = $v;
		// }
 		foreach($cookie as $k => $v) {
			$client->cookies[$k] = $v;
		}

		$response = '';

		switch($method){
			case 'GET':
				$response = $client->fetch($url);
				break;
			case 'POST':
				// $client->_submit_type = 'application/json';
				// $data = json_encode($data, true);
				// dump($data);
				$response = $client->submit($url, $data);
				break;
		}

		return $response;
	}



	/**
	 * 模拟浏览器GET请求
	 *
	 * @param $url 请求的url
	 * @param $type curl 或者 snoopy
	 * @return $this|bool|mixed|string
	 */
	public static function get($url, $type, $header, $cookie){

		$type = empty($type) ? 'curl' : $type;
		$output = false;

		switch($type){
			case 'curl':
				$output = self::curl($url, 'GET', '', $header, $cookie);
				break;
			case 'snoopy':
				$output = self::snoopy($url, 'GET', '', $header, $cookie);
				$output = $output->results;
				break;
		}

		return $output;
	}



	/**
	 * 模拟浏览器POST请求
	 *
	 * @param $url 请求的url
	 * @param $type curl 或者 snoopy
	 * @param array $params 参数 格式 [ curl|array snoopy|json ]
	 * @param $header 头信息 [ snoopy|string ]
	 * @param $cookie cookie信息 [ snoopy|string ]
	 * @return $this|bool|mixed|string
	 */
	public static function post($url, $type, $params, $header, $cookie){
		$type = empty($type) ? 'curl' : $type;
		$output = false;

		switch($type){
			case 'curl':
				$output = self::curl($url, 'POST', $params, $header, $cookie);
				break;
			case 'snoopy':
				$output = self::snoopy($url, 'POST', $params, $header, $cookie);
				$output = $output->results;
				break;
		}

		return $output;
	}
}
