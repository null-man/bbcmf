<?php
namespace app\admin\controller;

use think\Cookie;
use bb\Controller;
use think\Session;
use bb\DB;
use util\Tree;

/**
 * Class Comon 公告类
 * @package app\admin\controller
 */
class Common extends Controller {
	// 用户名
	public $admin      			= '';
	// 用户id
	public $uid        			= '';
	// 静态路径
	const STATIC_PATH  			= '/static/cmf/';


	// 表名
	protected $table			= '';
	// [添加] 页面
	protected $view_add 		= '';	
	// [主页] 页面
	protected $view_index		= '';
	// [编辑] 页面
	protected $view_edit		= '';


	function __construct(){
		parent::__construct();
		// 静态资源路径
		$this->assign('__STATIC__', self::STATIC_PATH);

		// 自动载入
		$admin_auth = Cookie::get('admin_auth');
		// cookie不为空
		if (!empty($admin_auth)) {
			list($username_admin, $uid_admin) = explode("\t", $this->authcode($admin_auth, 'DECODE'));

			if ($username_admin && $uid_admin) {
				$this->admin 	= $username_admin;
				$this->uid 		= $uid_admin;
			}

			// session个人信息
			$user = $this->one('admin', ['username' => $this->admin]);
			// 获取网站配置
			$this->assign('config', $this->one('site_config', ['admin_id' => $user['id']]));
			$this->assign('user',$user);

			$this->set_session($user);
		}
	}



	// ----------------------------------
	// 权限检查
	// ----------------------------------
    private function checkAccess($role_id, $uid){
        // 超级管理员
        if ($role_id == 1) {
            return true;
        }

        // 获取当前 /模块/控制器/方法
        $rule = '/'.MODULE_NAME .'/'. CONTROLLER_NAME . '/' .ACTION_NAME;

        // 必须权限
        $no_need_check_rules = [
    		'/admin/Index/index', 
    		'/admin/Index/indexview', 
    		'/admin/Index/menu'
    	];
        if (in_array($rule, $no_need_check_rules)) {
            return true;
        }

        // 获取用户
        $user 		= $this->one('admin', ['id' => $uid]);
        // 角色对应的权限
        $user_rules = $this->all('role_auth', ['role_id' => $user['role_id']]);
        // 权限
        $auth 		= false;

        foreach ($user_rules as $k => $user_rule) {
        	$user_rule_info = $this->one('rule', ['id' => $user_rule['rule_id']]);
        	if (strtolower($user_rule_info['src']) === strtolower($rule)) {
        		$auth = true;
        		break;
        	}
        }

        return $auth;
    }


	// ----------------------------------
	// 免登录/权限 校验
	// ----------------------------------
	public function check(){
		$admin_auth = Cookie::get('admin_auth');

		// cookie 已过期
		if (empty($admin_auth)) {
			$this->unset_session();
			$this->redirect(admin_url('Login/index'));
		} else if (!$this->checkAccess(session('role_id'), session('id'))) {
			$this->redirect(admin_url('Index/index'));
		}
	}


	// ----------------------------------
	// 默认不需要检查的方法
	// ----------------------------------
	public function noCheckAccess($action_name_arr = []){
		// 值转换 小写
		$action_name_arr = array_map(function($value) {
			return strtolower($value);
		}, $action_name_arr);

		$common_access = ['viewadd', 'logicadd', 'viewedit', 'logicedit', 'logicdelete'];

        !in_array(ACTION_NAME, array_merge($common_access, $action_name_arr)) && $this->check();
	}







	// ----------------------------------
    // 主页
    // ----------------------------------
    public function index(){
    	// 处理assign
    	method_exists($this, 'indexAssign') && call_user_func_array([$this, 'indexAssign'], []);
        return view($this->view_index);
    }



	// ----------------------------------
	// 添加页面
	// ----------------------------------
	public function viewAdd(){
		// 处理assign
    	method_exists($this, 'addAssign') && call_user_func_array([$this, 'addAssign'], []);
		return view($this->view_add);
	}


	// ----------------------------------
	// 添加逻辑
	// ----------------------------------
	public function logicAdd(){
		$data = method_exists($this, 'getParamsAdd') ? call_user_func_array([$this, 'getParamsAdd'], []) : $_POST;
		return $this->resultResponseAjax($this->insert($this->table, $data));
	}


	// ----------------------------------
	// 编辑页面
	// ----------------------------------
	public function viewEdit(){
		// 处理assign
		method_exists($this, 'editAssign') && call_user_func_array([$this, 'editAssign'], []);
		return view($this->view_edit);
	}


	// ----------------------------------
	// 编辑逻辑
	// ----------------------------------
	public function logicEdit(){
		$data = method_exists($this, 'getParamsEdit') ? call_user_func_array([$this, 'getParamsEdit'], []) : $_POST;
		$id = $_POST['id'];

		return !empty($id) ? $this->resultResponseAjax($this->update($this->table, $data, ['id' => $id])) : false;
	}


	// ----------------------------------
	// 删除逻辑
	// ----------------------------------
	public function logicDelete(){
		$data = method_exists($this, 'getParamsDelete') ? call_user_func_array([$this, 'getParamsDelete'], []) : $_POST;

		return isset($data['id']) && !empty($data['id']) ? $this->resultResponseAjax($this->delete($this->table, ['id' => $data['id']])) : false;
	}










	// ---------------------------------------------
	// 设置session
	// ---------------------------------------------
	protected function set_session($user_info){
		Session::set('id', 			$user_info['id']);
		Session::set('username', 	$user_info['username']);
		Session::set('nikname', 	$user_info['nikname']);
		Session::set('head', 		$user_info['head']);
		Session::set('role_id', 	$user_info['role_id']);
	}


	// ---------------------------------------------
	// 删除session
	// ---------------------------------------------
	protected function unset_session(){
		Session::delete('id');
		Session::delete('username');
		Session::delete('nikname');
		Session::delete('head');
		Session::delete('role_id');
	}










	// ----------------------------------
	// 跳转
	// ----------------------------------
	protected function resultRedirect($result, $s_url = 'index', $s_info = '操作成功', $e_info = '操作失败'){
		return !empty($result) ? $this->success($s_info, admin_url($s_url)) : $this->error($e_info);
	}


	// ----------------------------------
	// 返回数据
	// ----------------------------------
	protected function responseAjax($status, $message){
		return json_encode(['status' => $status, 'message' => $message]);
	}


	// ----------------------------------
	// 返回成功数据
	// ----------------------------------
	protected function resultResponseAjax($result, $s_message = '操作成功', $e_message = '操作失败', $s_status = '1', $e_status = '0'){
		return !empty($result) ? $this->responseAjax($s_status, $s_message) : $this->responseAjax($e_status, $e_message);
	}













	// + ----------------------------------
	// | 数据处理
	// + ----------------------------------

	// ----------------------------------
	// 查询所有
	// ----------------------------------
	protected function all($table, $where = [], $page = []){
		// 初始化表
		$handler = DB::table($table);

		// ### 逻辑处理
        // 分页
		!empty($page)  && $handler->forPage($page['page_cur'] < 1 ? 1 : $page['page_cur'], $page['page_num'] ?: 10);
		// where条件
        !empty($where) && $handler->where($where);
		
		return $handler->get();
	}

	// ----------------------------------
	// 查询一条
	// ----------------------------------
	protected function one($table, $where = []){
		return DB::table($table)->where($where)->first();
	}


	// ----------------------------------
	// 插入单条
	// ----------------------------------
	protected function insert($table, $data = []){
		return DB::table($table)->insertGetId($data);
	}


	// ----------------------------------
	// 更新单条
	// ----------------------------------
	protected function update($table, $data = [], $where){
		return DB::table($table)->where($where)->update($data);
	}


	// ----------------------------------
	// 删除单条
	// ----------------------------------
	protected function delete($table, $where){
		return DB::table($table)->where($where)->delete();
	}


	// ----------------------------------
	// 原生执行
	// ----------------------------------
	protected function execSql($sql){
		return DB::unprepared($sql);
	}


	// ----------------------------------
	// 分页
	// ----------------------------------
	protected function forPage($table){
		// ### 获取请求数据
		// 分页数
        $page_num   = I('page_num', 10);
        // 当前页
        $page       = I('page_cur', 1);

        // 获取分页数据
		$page_data = $this->all($table, [], ['page_cur' => $page, 'page_num' => $page_num]);
	
		$this->assign('page_data', $page_data);
		// 分页总数
        $this->assign('page_all',  ceil(count($this->all($this->table))/$page_num));

        return $page_data;
	}


	// ----------------------------------
	// 事务
	// ----------------------------------
	public function transaction($callback){
		try {
		  $exception = DB::transaction($callback);
		  return is_null($exception) ? true : $exception;
		} catch(Exception $e) {
		    return false;
		}
	}











	// + ----------------------------------
	// | 工具方法
	// + ----------------------------------

	/**
	 * 上传文件
	 * 
	 * @param string $path 上传文件夹路径
	 * @param string $file_name 文件名
	 * @param string $absolute_path 是否返回绝对路径
	 *
	 * @return 上传结果 bool
	 * @author Null <635384073@qq.com>
	 */
	public function uploadFile($file_name, $path = '', $absolute_path = FALSE){
		// ###获取文件信息
		// 临时文件
        $temp_file = $_FILES[$file_name]['tmp_name'];
        // 后缀名
        $suffix = strrev(explode('.', strrev($_FILES[$file_name]['name']))[0]);

        // 异常判断
        if(!$temp_file){
        	return FALSE;
        }

    	// 相对路径
		$return_path = $path = join("/", [empty($path) ? '/static/cmf/upload' : $path, date('Y-m-d')]);
		
		// 判断是单应用还是多应用
		$root_path = empty($_GET['_app_']) ? ROOT_PATH : ROOT_PATH . '../';
		
		// 绝对路径
		$path =  $root_path . 'public' . $return_path;
    	
    	// ###图片处理
        // 权限处理
    	!file_exists($path) && mkdir($path, 0777);
    	// 文件名
    	$final_file_name = '/' . md5(time()) . '.' .$suffix;

    	// 移动至目标文件夹
    	if (move_uploaded_file($temp_file, $path . $final_file_name)) {
    		return $absolute_path == FALSE ? $return_path . $final_file_name : $path . $final_file_name;
    	} else {
    		return FALSE;
    	}
	}



    /**
     * treeStyleData 树形风格数据
     * @param string 风格 
     *        "<option value='\$id' \$selected>\$spacer \$name</option>"
     * 
     * @return str 返回生成的树形风格 
     * 
     * @author Null <635384073@qq.com>
     */
    public function treeStyleData($table, $str){
        $tree   = new Tree();
        $tree->init($this->all($table));

        return $tree->get_tree(0, $str);
    }



	/**
	 * cookie 加密/解密
	 *
	 * @param string 需要加密的字符串
	 * @param string operation 加密/解密 操作
	 * @param stirng key
	 * @param int expiry
	 *
	 * @return string  加密/解密 数据
	 */
	protected function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$ckey_length = 4;
		$key = md5($key ? $key : SITE_URL);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);
		$result = '';
		$box = range(0, 255);
		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
}
?>