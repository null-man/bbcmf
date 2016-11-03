<?php

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

function sp_testwrite($d) {
    $tfile = "_test.txt";
    $fp = @fopen($d . "/" . $tfile, "w");
    if (!$fp) {
        return false;
    }
    fclose($fp);
    $rs = @unlink($d . "/" . $tfile);
    if ($rs) {
        return true;
    }
    return false;
}

function sp_dir_create($path, $mode = 0777) {
    if (is_dir($path))
        return true;
    $ftp_enable = 0;
    $path = sp_dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir))
            continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}


function sp_dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}

/**
 * 返回带协议的域名
 */
function sp_get_host(){
    $host=$_SERVER["HTTP_HOST"];
    $protocol=is_ssl()?"https://":"http://";
    return $protocol.$host;
}


/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}


function sp_execute_sql($db,$file,$tablepre){
    //读取SQL文件
    $sql = file_get_contents(MODULE_PATH . 'Data/'.$file);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $default_tablepre = "cmf_";
    $sql = str_replace(" `{$default_tablepre}", " `{$tablepre}", $sql);

    //开始安装
    sp_show_msg('(∩•̀ω•́)⊃-*⋆  开始安装数据库');
    foreach ($sql as $item) {
        $item = trim($item);
        if(empty($item)) continue;
        preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
        if($matches) {
            $table_name = $matches[1];
            $msg  = "(∩•̀ω•́)⊃-*⋆ 创建数据表{$table_name}";
            if(false !== $db->execute($item)){
                sp_show_msg($msg . ' 完成');
            } else {
                sp_show_msg($msg . ' 失败！', 'error');
            }
        } else {
            $db->execute($item);
        }

    }
}


function sp_update_site_configs($db,$table_prefix){
    $sitename = I("post.sitename");

    $sql =<<<hello
    INSERT INTO 
        `{$table_prefix}site_config` (site_name,admin_id) 
    VALUES 
        ('{$sitename}', '1');;
hello;
    $db->execute($sql);

    sp_show_msg("(∩•̀ω•́)⊃-*⋆  网站信息配置成功!");
}



/**
 * 随机字符串生成
 * @param int $len 生成的字符串长度
 * @return string
 */
function sp_random_string($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}



function sp_create_admin_account($db,$table_prefix){
    $username       = I("post.manager");
    $password       = password_hash(I("post.manager_pwd"), PASSWORD_DEFAULT);
    $email          = I("post.manager_email");
    $create_date    = time();

    $sql =<<<hello
    INSERT INTO 
        `{$table_prefix}admin` (username,password,nikname,role_id,group_id,phone,mail,head,mark,create_time,update_time) 
    VALUES 
        ('{$username}', '{$password}', 'admin', '1', '1', '', '{$email}', '/static/cmf/upload/default.png', '', '{$create_date}', '');;
hello;
    $db->execute($sql);

    sp_show_msg("(∩•̀ω•́)⊃-*⋆  管理员账号创建成功!");
}


/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function sp_create_config($config){
    if(is_array($config)){
        //读取配置内容
        $conf = file_get_contents(MODULE_PATH . 'database.php');

        //替换配置项
        foreach ($config as $key => $value) {
            $conf = str_replace("#{$key}#", $value, $conf);
        }
        //写入应用配置文件
        if(file_put_contents(APP_PATH . '/admin/database.php', $conf)){
            sp_show_msg('(∩•̀ω•́)⊃-*⋆  配置文件写入成功');
        } else {
            sp_show_msg('(∩•̀ω•́)⊃-*⋆  配置文件写入失败！', 'error');
        }
        return '';

    }
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 显示提示信息
 * @param  string $msg 提示信息
 */
function sp_show_msg($msg, $class = ''){
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    flush();
    ob_flush();
}