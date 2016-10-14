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

namespace bb;

use bb\DB;

use bb\wechat\AccessToken;
use bb\wechat\Auth;
use bb\wechat\AutoReply;
use bb\wechat\Media;
use bb\wechat\UserManage;
use bb\wechat\Menu;
use bb\wechat\ResponseInitiative;
use bb\wechat\ResponsePassive;
use bb\wechat\TemplateMessage;

use bb\wechat\WeChatOAuth;
use think\Log;

/**
 * Weixin Api
 */
class Weixin {


	//////////////////////////////////////////////////////////////////////////////////////
	//	常用方法
	//////////////////////////////////////////////////////////////////////////////////////

	// 获得open_id
//	public static function get_open_id() {
//
//	}

	// 获得access_token
	public static function get_access_token($public_id) {
		return AccessToken::getAccessToken($public_id);
	}

	// 获得公众号信息
	public static function get_publicinfo($public_id) {
		return DB::table('tb_public')->where('id', $public_id)->first();
	}

	// 获得公众号的token
	public static function get_public_token() {
		return 'weiphp';
	}

	// 获得公众号的app_id
	public static function get_appid($public_id) {
		$publicinfo = self::get_publicinfo($public_id);
		if($publicinfo) {
			return $publicinfo['app_id'];
		}
		return '';
	}

	// 获取微信服务器IP列表
	public static function get_wechat_iplist($public_id) {
		return Auth::getWeChatIPList($public_id);
	}

	// 获得自动回复规则
	public static function get_role_autoreply($public_id) {
		return AutoReply::getRole($public_id);
	}


	//////////////////////////////////////////////////////////////////////////////////////
	//	用户管理 UserManage
	//////////////////////////////////////////////////////////////////////////////////////

	// 同步用户信息
	public static function syn_userlist ($public_id) {
		self::exec_syn_userlist($public_id);
	}

	// 获取关注者列表
	public static function getFansList($public_id, $next_openId='') {
		return UserManage::getFansList($public_id, $next_openId);
	}

	// 获取用户基本信息
	public static function getUserInfo($openId, $public_id) {
		return UserManage::getUserInfo($openId, $public_id);
	}

	// 获取分组列表
	public static function getGroupList($public_id) {
		return UserManage::getGroupList($public_id);
	}

	// 创建组名
	public static function create_group($groupName, $public_id) {
		return UserManage::createGroup($groupName, $public_id);
	}

	// 修改分组名
	public static function editGroupName($groupId, $groupName, $public_id) {
		return UserManage::editGroupName($groupId, $groupName, $public_id);
	}

	// 删除分组
	public static function deleteGroup($groupId, $public_id) {
		return UserManage::deleteGroup($groupId, $public_id);
	}

	// 查询用户所在分组
	public static function getGroupByOpenId($openId, $public_id) {
		return UserManage::getGroupByOpenId($openId, $public_id);
	}

	// 移动用户分组
	public static function editUserGroup($openid, $to_groupid, $public_id) {
		return UserManage::editUserGroup($openid, $to_groupid, $public_id);
	}

	// 修改粉丝的备注
	public static function setRemark($openId, $nickname, $public_id) {
		return UserManage::setRemark($openId, $nickname, $public_id);
	}


	//////////////////////////////////////////////////////////////////////////////////////
	//	多媒体的上传与下载 Media、Material
	//////////////////////////////////////////////////////////////////////////////////////

	// 临时素材上传
	public static function media_upload($filename, $type, $public_id) {
		return Media::upload($filename, $type, $public_id);
	}

	// 临时素材下载
	public static function media_download($mediaId, $public_id) {
		return Media::download($mediaId, $public_id);
	}

	// 永久素材列表


	// 永久素材上传
	public static function material_upload($filename, $type, $public_id) {

	}

	// 永久素材下载


	// 永久素材删除


	//////////////////////////////////////////////////////////////////////////////////////
	//	自定义菜单 Menu
	//////////////////////////////////////////////////////////////////////////////////////

	// 设置菜单
	public static function setMenu($menuList, $public_id) {
		return Menu::setMenu($menuList, $public_id);
	}

	// 获取菜单
	public static function getMenu($public_id) {
		return Menu::getMenu($public_id);
	}

	// 删除菜单
	public static function delMenu($public_id) {
		return Menu::delMenu($public_id);
	}

	//////////////////////////////////////////////////////////////////////////////////////
	//	发送主动响应 ResponseInitiative
	//////////////////////////////////////////////////////////////////////////////////////

	// 发送文本
	public static function send_text($tousername, $content, $public_id) {
		return ResponseInitiative::text($tousername, $content, $public_id);
	}

	// 发送图片
	public static function send_image($tousername, $mediaId, $public_id) {
		return ResponseInitiative::image($tousername, $mediaId, $public_id);
	}

	// 发送语音
	public static function send_voice($tousername, $mediaId, $public_id) {
		return ResponseInitiative::voice($tousername, $mediaId, $public_id);
	}

	// 发送视频
	public static function send_video($tousername, $mediaId, $title, $description, $public_id) {
		return ResponseInitiative::video($tousername, $mediaId, $title, $description, $public_id);
	}

	// 发送音乐
	public static function send_music($tousername, $title, $description, $musicUrl, $hqMusicUrl, $thumbMediaId, $public_id) {
		return ResponseInitiative::music($tousername, $title, $description, $musicUrl, $hqMusicUrl, $thumbMediaId, $public_id);
	}

	// 发送图文消息
	public static function send_news($tousername, $item, $public_id) {
		return ResponseInitiative::news($tousername, $item, $public_id);
	}


	//////////////////////////////////////////////////////////////////////////////////////
	//	发送被动响应 ResponsePassive
	//////////////////////////////////////////////////////////////////////////////////////

	// 回复文本
	public static function reply_text($fromusername, $tousername, $content, $funcFlag=0) {
		return ResponsePassive::text($fromusername, $tousername, $content, $funcFlag);
	}

	// 回复图片
	public static function reply_image($fromusername, $tousername, $mediaId, $funcFlag=0) {
		return ResponsePassive::image($fromusername, $tousername, $mediaId, $funcFlag);
	}

	// 回复语音
	public static function reply_voice($fromusername, $tousername, $mediaId, $funcFlag=0) {
		return ResponsePassive::voice($fromusername, $tousername, $mediaId, $funcFlag);
	}

	// 回复视频
	public static function reply_video($fromusername, $tousername, $mediaId, $title, $description, $funcFlag=0) {
		return ResponsePassive::video($fromusername, $tousername, $mediaId, $title, $description, $funcFlag);
	}

	// 回复音乐
	public static function reply_music($fromusername, $tousername, $title, $description, $musicUrl, $hqMusicUrl, $thumbMediaId, $funcFlag=0) {
		return ResponsePassive::music($fromusername, $tousername, $title, $description, $musicUrl, $hqMusicUrl, $thumbMediaId, $funcFlag);
	}

	// 回复图文消息
	public static function reply_news($fromusername, $tousername, $item, $funcFlag=0) {
		return ResponsePassive::news($fromusername, $tousername, $item, $funcFlag);
	}

	// 将消息转发到多客服
	public static function forwardToCustomService($fromusername, $tousername) {
		return ResponsePassive::forwardToCustomService($fromusername, $tousername);
	}

	//////////////////////////////////////////////////////////////////////////////////////
	//	模板消息接口 TemplateMessage
	//////////////////////////////////////////////////////////////////////////////////////

	// 设置所属行业
	public static function setIndustry($industryId1, $industryId2, $public_id) {
		return TemplateMessage::setIndustry($industryId1, $industryId2, $public_id);
	}

	// 获得模板ID
	public static function getTemplateId($templateIdShort, $public_id) {
		return TemplateMessage::getTemplateId($templateIdShort, $public_id);
	}

	// 向用户推送模板消息
	public static function sendTemplateMessage($data, $touser, $templateId, $url, $public_id, $topcolor='#FF0000') {
		return TemplateMessage::sendTemplateMessage($data, $touser, $templateId, $url, $public_id, $topcolor='#FF0000');
	}


	//////////////////////////////////////////////////////////////////////////////////////
	//	授权登录 WeChatOAuth
	//////////////////////////////////////////////////////////////////////////////////////

	// 获得code
	public static function auth2_getcode($redirect_uri, $public_id, $scope='snsapi_base', $state=1) {
		WeChatOAuth::getCode($redirect_uri, $public_id, $scope, $state);

	}

	// 通过code换取网页授权access_token
	public static function auth2_getaccesstoken($code, $public_id) {
		return WeChatOAuth::getAccessTokenAndOpenId($code, $public_id);
	}

	// 刷新access_token
	public static function auth2_refreshtoken($refreshToken, $public_id) {
		return WeChatOAuth::refreshToken($refreshToken, $public_id);
	}

	// 验证access_token
	public static function auth2_checkAccessToken($accessToken, $openId) {
		return WeChatOAuth::checkAccessToken($accessToken, $openId);
	}

	// 获得用户信息
	public static function auth2_getUserInfo($accessToken, $openId, $lang='zh_CN') {
		return WeChatOAuth::getUserInfo($accessToken, $openId, $lang);
	}



	//////////////////////////////////////////////////////////////////////////////////////
	//	高级群发 AdvancedBroadcast
	//////////////////////////////////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////////////////////////
	//	多客服功能 CustomService
	//////////////////////////////////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////////////////////////
	//	智能接口 IntelligentInterface
	//////////////////////////////////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////////////////////////
	//	推广支持 Popularize
	//////////////////////////////////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////////////////////////////////
	// 内部方法
	//////////////////////////////////////////////////////////////////////////////////////

	private static function exec_syn_userlist($public_id) {
		Log('exec_syn_userlist...');

		self::get_fanslist($public_id);

		$userlist = DB::select('select * from tb_user');

		foreach ($userlist as $key => $user) {
			$open_id = $user['open_id'];
			$userinfo = self::getUserInfo($open_id, $public_id);

			if($userinfo && isset($userinfo['openid'])) {
				unset($userinfo['openid']);

				// 昵称
				if(isset($userinfo['nickname'])) {
					$userinfo['nickname'] = urlencode($userinfo['nickname']);
				}

				// 标签
				$userinfo['tagid_list'] = '';
				if(isset($userinfo['tagid_list'])) {
					$tagid_list = $userinfo['tagid_list'];
					if(!empty($tagid_list)) {
						$userinfo['tagid_list'] = implode(',', $tagid_list);
					}
				}

				DB::update('update tb_user set subscribe=?,nickname=?,sex=?,language=?,city=?,province=?,country=?,headimgurl=?,subscribe_time=?,remark=?,unionid=?,groupid=?,tagid_list=? where open_id = ?',
						array($userinfo['subscribe'],$userinfo['nickname'],$userinfo['sex'],$userinfo['language'],$userinfo['city'],$userinfo['province'],
								$userinfo['country'],$userinfo['headimgurl'],$userinfo['subscribe_time'],$userinfo['remark'],$userinfo['unionid'],$userinfo['groupid'],
								$userinfo['tagid_list'], $open_id));

				Log('update用户信息 '.$userinfo['nickname']);
			}
		}

	}

	// 获取关注者列表
	private static function get_fanslist($public_id) {
		Log('get_fanslist...');

		$result = self::getFansList($public_id);

		$total = $result['total'];
		$curTotal = $result['count'];

		Log('total: '.$total);
		Log('curTotal: '.$curTotal);

		// 保存关注者列表
		$fanslist = $result['data']['openid'];
		self::save_fanslist($fanslist, $public_id);

		// 继续获得列表
		while ($curTotal < $total) {
			$next_openid = $result['next_openid'];

			$result = self::getFansList($public_id, $next_openid);

			$total = $result['total'];
			$curTotal = $curTotal + $result['count'];

			Log('curTotal: '.$curTotal);

			if(isset($result['data'])) {
				$fanslist = $result['data']['openid'];
				self::save_fanslist($fanslist, $public_id);
			}
			else {
				break;
			}
		}
	}

	// 保存openid
	private static function save_fanslist($fanslist, $public_id) {
		Log('save_fanslist...');

		foreach ($fanslist as $key => $item) {

			$model = DB::table('tb_user')->where('open_id', $item)->first();

			// 没有时，添加
			if(!$model) {
				DB::insert('insert into tb_user (open_id, public_id) values (?, ?)', array($item, $public_id));

				Log('insert...openid...');
			}
		}

	}

	private static function Log($msg) {
		Log::record('insert...openid...');
		Log::save();
	}
}