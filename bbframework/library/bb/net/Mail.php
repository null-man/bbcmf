<?php

namespace bb\net;

use util\NetUtils;

class Mail {

	// 源邮箱
	protected $mail = '';

	// 密码
	protected $password = '';

	// 署名
	protected $name = '';

	// 内容
	protected $content = '';

	// 标题
	protected $title = '';
	

	public function from($mail, $password = null, $name = null) {
		$this->mail = $mail;
		empty($password) ?: $this->password = $password;
		empty($name) ?: $this->name = $name;
		return $this;
	}

	public function password($password) {
		$this->password = $password;
		return $this;
	}

	public function name($name) {
		$this->name = $name;
		return $this;
	}

	public function title($title) {
		$this->title = $title;
		return $this;
	}

	public function content($content) {
		$this->content = $content;
		return $this;
	}

	public function to($to, $callback = null) {
		if(empty($this->mail) || empty($this->password) || empty($this->name)) {
			return false;
		}
		$ret = $this->send($to, $this->mail, $this->password, $this->name, $this->title, $this->content);

		return $ret;
	}


	public function send($to = '', $mail = '', $password = '', $name = '', $subject = '', $body = '') {
		return NetUtils::send_mail($to, $mail, $password, $name, $subject, $body);
	}
}