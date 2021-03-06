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


use think\Cache;
use think\Config;
use think\Cookie;
use think\Db;
use think\Debug;
use think\Input;
use think\Lang;
use think\Loader;
use think\Log;
use think\Route;
use think\Session;
use think\Url;
use think\View;



/**
 * 快速导入BTraits
 * @param string $class trait库
 * @param string $ext 类库后缀
 * @return boolean
 */
function BT($class, $ext = EXT)
{
    return \think\Loader::import($class, BB_TRAIT_PATH, $ext);
}

/**
 * Url生成 url
 * @param string $url 路由地址
 * @param string|array $value 变量
 * @param bool|string $suffix 前缀
 * @param bool|string $domain 域名
 * @return string
 */
function U($url = '', $vars = '', $suffix = true, $domain = false) {
    $url = Url::build($url, $vars, $suffix, $domain);
    if(!empty($_SERVER["QUERY_STRING"]) && strpos($url, '?') === false) {


        $query = '?';

        $args = explode('&', $_SERVER["QUERY_STRING"]);
        foreach($args as $arg) {
            $v = explode('=', $arg);
            if(in_array($v[0], ['_app_', '_multi_', '_debug_', '_config_'])) {
                $query = $query . $arg . '&';
            }
        }


        $url = $url . substr($query, 0, strlen($query)-1);

    }
    return $url;
}


function admin_url($url, $vars = '', $suffix = true, $domain = false) {
    $admin = \think\Config::get('admin.url');
    if($admin != '/admin') {
        $url = str_replace('/admin', $admin, $url);
    }
    return U($url, $vars, $suffix, $domain);
}

// Think helper 
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//------------------------
// ThinkPHP 助手函数
//-------------------------



/**
 * 获取语言变量值
 * @param string $name 语言变量名
 * @param array $vars 动态变量值
 * @param string $lang 语言
 * @return mixed
 */
function L($name, $vars = [], $lang = '')
{
    return Lang::get($name, $vars, $lang);
}

/**
 * 获取和设置配置参数
 * @param string $name 参数名
 * @param mixed $value 参数值
 * @param string $range 作用域
 * @return mixed
 */
function C($name = '', $value = null, $range = '')
{
    if (is_null($value) && is_string($name)) {
        return Config::get($name, $range);
    } else {
        return Config::set($name, $value, $range);
    }
}

/**
 * 获取输入数据 支持默认值和过滤
 * @param string $key 获取的变量名
 * @param mixed $default 默认值
 * @param string $filter 过滤方法
 * @param bool $merge 是否合并系统默认过滤方法
 * @return mixed
 */
function I($key, $default = null, $filter = null, $merge = false)
{
    if (0 === strpos($key, '?')) {
        $key = substr($key, 1);
        $has = '?';
    } else {
        $has = '';
    }
    if ($pos = strpos($key, '.')) {
        // 指定参数来源
        $method = substr($key, 0, $pos);
        if (in_array($method, ['get', 'post', 'put', 'delete', 'param', 'request', 'session', 'cookie', 'server', 'globals', 'env', 'path', 'file'])) {
            $key = substr($key, $pos + 1);
        } else {
            $method = 'param';
        }
    } else {
        // 默认为自动判断
        $method = 'param';
    }
    return Input::$method($has . $key, $default, $filter, $merge);
}

/**
 * 记录时间（微秒）和内存使用情况
 * @param string $start 开始标签
 * @param string $end 结束标签
 * @param integer|string $dec 小数位 如果是m 表示统计内存占用
 * @return mixed
 */
function G($start, $end = '', $dec = 6)
{
    if ('' == $end) {
        Debug::remark($start);
    } else {
        return 'm' == $dec ? Debug::getRangeMem($start, $end) : Debug::getRangeTime($start, $end, $dec);
    }
}

/**
 * 快速导入Traits PHP5.5以上无需调用
 * @param string $class trait库
 * @param string $ext 类库后缀
 * @return boolean
 */
function T($class, $ext = EXT)
{
    return Loader::import($class, TRAIT_PATH, $ext);
}

/**
 * 抛出异常处理 (E)
 *
 * @param string  $msg  异常消息
 * @param integer $code 异常代码 默认为0
 * @param string $exception 异常类
 *
 * @throws Exception
 */
function Err($msg, $code = 0, $exception = '')
{
    $e = $exception ?: '\think\Exception';
    throw new $e($msg, $code);
}

/**
 * 渲染输出Widget
 * @param string $name Widget名称
 * @param array $data 传人的参数
 * @return mixed
 */
function W($name, $data = [])
{
    return Loader::action($name, $data, 'widget');
}

/**
 * 实例化Model
 * @param string $name Model名称
 * @param string $layer 业务层名称
 * @return \think\Model
 */
function model($name = '', $layer = MODEL_LAYER)
{
    return Loader::model($name, $layer);
}

/**
 * 实例化数据库类
 * @param array $config 数据库配置参数
 * @return \think\db\Connection
 */
function db($config = [])
{
    return Db::connect($config);
}

/**
 * 实例化控制器 格式：[模块/]控制器
 * @param string $name 资源地址
 * @param string $layer 控制层名称
 * @return \think\Controller
 */
function controller($name, $layer = CONTROLLER_LAYER)
{
    return Loader::controller($name, $layer);
}

/**
 * 调用模块的操作方法 参数格式 [模块/控制器/]操作 action
 * @param string $url 调用地址
 * @param string|array $vars 调用参数 支持字符串和数组
 * @param string $layer 要调用的控制层名称
 * @return mixed
 */
function A($url, $vars = [], $layer = CONTROLLER_LAYER)
{
    return Loader::action($url, $vars, $layer);
}

/**
 * 导入所需的类库 同java的Import 本函数有缓存功能
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return boolean
 */
function import($class, $baseUrl = '', $ext = EXT)
{
    return Loader::import($class, $baseUrl, $ext);
}

/**
 * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
 * @param string $class 类库
 * @param string $ext 类库后缀
 * @return boolean
 */
function vendor($class, $ext = EXT)
{
    return Loader::import($class, VENDOR_PATH, $ext);
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为true 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @return void|string
 */
function dump($var, $echo = true, $label = null)
{
    return Debug::dump($var, $echo, $label);
}



/**
 * Session管理
 * @param string|array $name session名称，如果为数组表示进行session设置
 * @param mixed $value session值
 * @param string $prefix 前缀
 * @return mixed
 */
function session($name, $value = '', $prefix = null)
{
    if (is_array($name)) {
        // 初始化
        Session::init($name);
    } elseif (is_null($name)) {
        // 清除
        Session::clear($value);
    } elseif ('' === $value) {
        // 判断或获取
        return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);
    } elseif (is_null($value)) {
        // 删除session
        return Session::delete($name, $prefix);
    } else {
        // 设置session
        return Session::set($name, $value, $prefix);
    }
}

/**
 * Cookie管理
 * @param string|array $name cookie名称，如果为数组表示进行cookie设置
 * @param mixed $value cookie值
 * @param mixed $option 参数
 * @return mixed
 */
function cookie($name, $value = '', $option = null)
{
    if (is_array($name)) {
        // 初始化
        Cookie::init($name);
    } elseif (is_null($name)) {
        // 清除
        Cookie::clear($value);
    } elseif ('' === $value) {
        // 获取
        return Cookie::get($name);
    } elseif (is_null($value)) {
        // 删除session
        return Cookie::delete($name);
    } else {
        // 设置session
        return Cookie::set($name, $value, $option);
    }
}

/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function cache($name, $value = '', $options = null)
{
    if (is_array($options)) {
        // 缓存操作的同时初始化
        Cache::connect($options);
    } elseif (is_array($name)) {
        // 缓存初始化
        return Cache::connect($name);
    }
    if ('' === $value) {
        // 获取缓存
        return Cache::get($name);
    } elseif (is_null($value)) {
        // 删除缓存
        return Cache::rm($name);
    } else {
        // 缓存数据
        if (is_array($options)) {
            $expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
        } else {
            $expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
        }
        return Cache::set($name, $value, $expire);
    }
}

/**
 * 记录日志信息
 * @param mixed $log log信息 支持字符串和数组
 * @param string $level 日志级别
 * @return void|array
 */
function trace($log = '[think]', $level = 'log')
{
    if ('[think]' === $log) {
        return Log::getLog();
    } else {
        Log::record($log, $level);
    }
}

/**
 * 渲染模板输出
 * @param string $template 模板文件
 * @param array $vars 模板变量
 * @return string
 */
function view($template = '', $vars = [])
{
    return View::instance(Config::get('view'))->fetch($template, $vars);
}

/**
 * 路由注册 route
 * @param string $rule 路由规则
 * @param mixed $route 路由地址
 * @param sting $type 请求类型
 * @param array $option 路由参数
 * @param array $pattern 变量规则
 * @return void
 */
function R($rule = '', $route = [], $type = '*', $option = [], $pattern = [])
{
    Route::register($rule, $route, $type, $option, $pattern);
}

