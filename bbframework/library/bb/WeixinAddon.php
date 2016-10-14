<?php

namespace bb;


/**
 * 微信插件基类
 */
abstract class WeixinAddon {

	// 必须实现 回复消息
	abstract public function reply($request, $public_id);

}