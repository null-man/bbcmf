<?php
namespace bb\wechat;

use bb\DB;

/**
 * 微信Access_Token的获取与过期检查
 * Created by Lane.
 * User: lane
 * Date: 13-12-29
 * Time: 下午5:54
 * Mail: lixuan868686@163.com
 * Website: http://www.lanecn.com
 */
class AccessToken{

    /**
     * 获取微信Access_Token
     */
    public static function getAccessToken($public_id){
        //检测本地是否已经拥有access_token，并且检测access_token是否过期
        $accessToken = self::_checkAccessToken($public_id);
        if($accessToken === false){
            $accessToken = self::_getAccessToken($public_id);
        }
        return $accessToken['access_token'];
    }

    /**
     * @descrpition 从微信服务器获取微信ACCESS_TOKEN
     * @return Ambigous|bool
     */
    private static function _getAccessToken($public_id){
        $publicinfo = DB::table('tb_public')->where('id', $public_id)->first();
        if($publicinfo) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$publicinfo['app_id'].'&secret='.$publicinfo['app_secret'];
            $accessToken = Curl::callWebServer($url, '', 'GET');
            if(!isset($accessToken['access_token'])){
                return Msg::returnErrMsg(MsgConstant::ERROR_GET_ACCESS_TOKEN, '获取ACCESS_TOKEN失败');
            }
            $accessToken['time'] = time();
            $accessTokenJson = json_encode($accessToken);

            // 存入数据库
            DB::update('update tb_public set access_token = ? where id = ?', array($accessTokenJson, $public_id));

            return $accessToken;
        }

        return Msg::returnErrMsg(MsgConstant::ERROR_CONFIG);
    }

    /**
     * @descrpition 检测微信ACCESS_TOKEN是否过期
     *              -10是预留的网络延迟时间
     * @return bool
     */
    private static function _checkAccessToken($public_id){
        $publicinfo = DB::table('tb_public')->where('id', $public_id)->first();
        if($publicinfo) {
            $data = $publicinfo['access_token'];
            $accessToken['value'] = $data;
            if(!empty($accessToken['value'])){
                $accessToken = json_decode($accessToken['value'], true);
                if(time() - $accessToken['time'] < $accessToken['expires_in']-10){
                    return $accessToken;
                }
            }
        }

        return false;
    }
}
?>