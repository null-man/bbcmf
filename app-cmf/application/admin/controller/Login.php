<?php
namespace app\admin\controller;

use think\Cookie;
use think\Validate;
use app\common\helper\VerifyHelper;

class Login extends Common {
    // cookie 3天过期时间
    const EXPIRE_TIME = 259200;
    // cookie 默认过期时间（1小时）
    const NOMAL_TIME  = 3600;
    // 验证码 开启
    const VERIFY_CODE = 1;
    // 3天免登录 开启
    const AUTO_LOGIN  = 1;

    function __construct(){
        parent::__construct();
    }
    
    public function index(){
        // 自动登录
        if (!empty(Cookie::get('admin_auth'))) {
            return $this->success("⊂(˃̶͈̀ε ˂̶͈́ ⊂ )))Σ≡=─ 登陆成功 ", admin_url('Index/index'));
        }

        // 获取网站配置
        $this->assign('config', $this->one('bbcmf_site_set', ['id' => 1]));
        return $this->fetch('index');
    }


    // ----------------------------------
    // 验证码图片src
    // ----------------------------------
    public function verify(){
        VerifyHelper::verify();
    }
    

    // ----------------------------------
    // 登录
    // ----------------------------------
    public function doAdminLogin(){
        $username   = trim($_POST['username']);
        $password   = $_POST['password'];
        $auto_login = isset($_POST['auto_login']) ? $_POST['auto_login'] : '0';

        // 网站配置信息
        $site_set = $this->one('bbcmf_site_set', ['id' => 1]);

        if (true) {
            // ###数据合法性校验
            // 校验器
            $validate = new Validate([
                'username'  => 'require',
                'password'  => 'require'
            ]);

            $data = [
                'username'  => $username,
                'password'  => $password,
            ];

            // 是否开启验证码 && 验证码校验
            if ($site_set['verify_code'] == self::VERIFY_CODE && session('verify_code') != $_POST['code']) {
                 return $this->error('(⁄ ⁄•⁄ω⁄•⁄ ⁄) 验证码错误 ');
            }
           
            // 帐号密码校验
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
        }

        // ###校验账户登录合法性
        // 获取该账户信息
        $user_info = $this->one('bbcmf_admin', ['username' => $username]);
        
        if (!empty($user_info)) {
            // 密码校验
            $hash_password = password_verify($password, $user_info['password']);
            if (!$hash_password) {
                return $this->error('(ง •̀_•́)ง 密码错误 ');
            }
            // ###3天免登录开启
            // 过期时间
            if ($auto_login == self::AUTO_LOGIN) {
                $expire = time() + self::EXPIRE_TIME;
            } else {
                $expire = time() + self::NOMAL_TIME;
            }

            Cookie::set('admin_auth', $this->authcode("$username\t$user_info[id]", 'ENCODE'), $expire);
            $this->set_session($user_info);
            
            return $this->success("⊂(˃̶͈̀ε ˂̶͈́ ⊂ )))Σ≡=─ 登陆成功 ", U('Index/index'));
        } else {
            return $this->error("(ง •̀_•́)ง 登陆失败, 请检查帐号或密码 ");
        }
    }


    // ----------------------------------
    // 注册
    // ----------------------------------
    public function register(){
        if (IS_GET) {
            if ($this->one('bbcmf_site_set', ['id' => 1])['free_reg'] == 0 ) {
                return $this->error("(┛ಠДಠ)┛ 你想做什么羞羞的事 ");
            }

            return $this->fetch('reg');
        }

        if (IS_POST) {
            $username = trim($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $admin_info = $this->one('bbcmf_admin', ['username' => $username]);

            if ($admin_info) {
                return $this->error('(┛ಠДಠ)┛ 该用户名已被注册 ');
            }

            $ret = $this->insert(
                'bbcmf_admin', 
                [
                    'username' => $username, 
                    'password' => $password,
                    'nikname'  => 'anonymous',
                    'head'     => '/static/cmf/upload/default.png',
                    'create_time' => time()
                ]
            );
            return $this->resultRedirect($ret, 'index', '⊂(˃̶͈̀ε ˂̶͈́ ⊂ )))Σ≡=─ 注册成功 ');
        }
    }


    // ----------------------------------
    // 注销
    // ----------------------------------
    public function logout(){
        Cookie::delete('admin_auth');
        $this->unset_session();
        return $this->success("⊂(˃̶͈̀ε ˂̶͈́ ⊂ )))Σ≡=─ 注销成功", U('admin/Login/index'));
    }
}


?>