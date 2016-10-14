<?php

namespace bb;

use bb\Controller;
use bb\Weixin;

use bb\wechat\Wechat;
use bb\wechat\ResponsePassive;

/**
 * 微信基本
 */
class WeixinController extends Controller {

    /**
     * 入口方法
     */
	public function index()
    {
        $token = Weixin::get_public_token();
        if($token) {
            $wechat = new Wechat($token, TRUE);
            if(isset($_GET['echostr'])) {
                return $wechat->checkSignature($token);
            }
            // 入口url必须带有public_id
            elseif(isset($_GET['public_id'])) {
                return $this->handler_msg($wechat->getRequest(), $_GET['public_id']);
            }
        }
	}

    /**
     * 处理消息
     */
    protected function handler_msg($request, $public_id) {
        $data = array();

        switch ($request['msgtype']) {
            // 事件
            case 'event':
                $request['event'] = strtolower($request['event']);
                switch ($request['event']) {

                    // 关注
                    case 'subscribe':
                        $data = $this->event_welcome($request, $public_id);
                        break;

                    //扫描二维码
                    case 'scan':
                        $data = $this->event_scan($request, $public_id);
                        break;

                    default:
                        break;
                }
                break;
            // 文本
            case 'text':
                $data = $this->text($request, $public_id);
                break;
            // 图像
            case 'image':
                break;
            // 语音
            case 'voice':
                break;
            // 视频
            case 'video':
                break;
            // 小视频
            case 'shortvideo':
                break;
            // 位置
            case 'location':
                break;
            // 链接
            case 'link':
                break;
            default:
                break;
        }

        return $data;
    }

    /**
     * 事件：添加关注_欢迎信息
     */
    protected function event_welcome($request, $public_id)
    {
        $publicinfo = Weixin::get_publicinfo($public_id);
        $content = '你好，欢迎关注< '.$publicinfo['name'].' >';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * 事件：扫二维码
     */
    protected function event_scan($request, $public_id) {

    }

    /**
     * 文本
     */
    protected function text($request, $public_id)
    {
        // 调用插件
        if('hello' == $request['content']) {

            $addon_class = get_addon_class('Hello');

            $addon   = new $addon_class();
            return $addon->reply($request, $public_id);

        }
        else if('home' == $request['content']) {

            $addon_class = get_addon_class('home');

            $addon   = new $addon_class();
            return $addon->reply($request, $public_id);
        }
        else {
            $content = '收到文本消息';
            return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
        }
    }

}