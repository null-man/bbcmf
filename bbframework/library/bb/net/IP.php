<?php

namespace bb\net;

class IP {

	protected $ip;

	public function __construct($ip) {
		if(is_int($ip)) {
			$this->ip = long2ip($ip);
		} else {
			$this->ip = $ip;
		}
	}

	public function isValid() {
		$ret = filter_var($this->ip, FILTER_VALIDATE_IP);
		if($ret === false) return false;
		return true;
	}

	public function getLong() {
		return ip2long($this->ip);
	}

	public function getIP() {
		return $this->ip;
	}

	// public function get

}