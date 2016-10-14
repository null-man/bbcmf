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

use bb\ES;
use bb\es\Scheme;
use bb\Date;

use think\Loader;

abstract class Eodel implements \JsonSerializable, \ArrayAccess {


	// 数据库对象池
    private static $links = [];
    // 数据库配置
    protected static $config;
    // 索引名称
    protected static $index;
    // 类型名称
    protected static $type;
    // 索引前缀
    protected static $prefix;
    // 初始化过的模型
    protected static $initialized = [];
    // 回调事件
    protected static $event = [];
    // 强制创建
    protected static $force_created = true;
    // 模型结构
    protected static $mapping = [];


	// 自动写入的时间戳字段列表
    protected $autoTimeField = ['created_at', 'updated_at'];
    // 允许写入字段
    protected $field = [];
    // 保存自动完成列表
    protected $auto = [];
    // 新增自动完成列表
    protected $insert = [];
    // 更新自动完成列表
    protected $update = [];
	// 记录改变字段
    protected $change = [];
	// 字段类型或者格式转换
    protected $field_type = [];
    // 时间字段取出后的默认时间格式
    protected $dateFormat = TIME_DEFAULT_FORMAT;


    // 匹配分数
    protected $_score;
    // ID
    protected $_id;
	// 数据信息
    protected $data = [];
    // 当前模型名称
    protected $name;
    // 是否为更新数据
    protected $isUpdate = false;
   	// 错误信息
    protected $error;

    public function __construct($data = []) {

		if (is_object($data)) {
            $this->data = get_object_vars($data);
        } else {
            $this->data = $data;
        }
        $this->name = basename(str_replace('\\', '/', get_class($this)));

        $this->initialize();
    }


    /**
     *  初始化模型
     *
     * @return void
     */
    protected function initialize() {
        $class = get_class($this);
        if (!isset(static::$initialized[$class])) {
            static::$initialized[$class] = true;
            self::es();
            static::init();
        }
    }


    /**
     * 初始化处理
     * @return void
     */
    protected static function init() {
    	if(static::$force_created) {
			static::eodelCreated();
		}
    }

    /**
     * 保证模型存在
     * @return void
     */
    protected static function eodelCreated() {

        list($index, $type) = self::tableName();
		// 保证索引存在
		if(!ES::scheme()->index($index)->exists()) {
			ES::scheme()->index($index)->create();
		}
		// 保证类型存在
		if(!ES::scheme()->index($index)->type($type)->exists() && !empty(static::$mapping)) {
			ES::scheme()->index($index)->type($type)->mapping(static::$mapping, false)->create();
		} else {
            // // 是否有增加类型
            $keys = array_keys(static::$mapping);
            $mapping = ES::scheme()->index($index)->type($type)->get();
            $prefix = self::es()->getConnection()->getPrefix();
            $keys2 = array_keys($mapping[$prefix.$index]['mappings'][$type]['properties']);
            $diff = array_diff($keys, $keys2);
            if(!empty($diff)) {
                $update = [];
                foreach ($diff as $key) {
                    $update[$key] = static::$mapping[$key];
                }
                ES::scheme()->index($index)->type($type)->mapping($update, false)->update();
            }
        }
	}


    /**
     * 设置数据对象值
     * @access public
     * @param mixed $data 数据
     * @return $this
     */
    public function data($data = '', $raw = false) {
        if (is_object($data)) {
        	$data = get_object_vars($data);
        } elseif (!is_array($data)) {
            throw new \think\Exception('data type invalid', 10300);
        }
        if(!$raw) {
        	foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        } else {
        	$this->data = $data;
        }
        return $this;
    }


	/**
     * 创建当前数据对象
     * @access public
     * @param array $data 数据
     * @param array $where 更新条件
     * @return integer
     */
    public function create($data = []) {
    	$this->isUpdate = false;
    	return $this->save($data, false);
    }


    /**
     * 保存当前数据对象
     * @access public
     * @param array $data 数据
     * @param array $where 更新条件
     * @return integer
     */
    public function save($data = [], $where = []) {

    	// 当update为FALSE，并且有ID，判断为更新。若要添加对象使用create
    	if($where !== false && !$this->isUpdate && !empty($this->_id)) {
    		$this->isUpdate = true;
    	}

    	return $this->_save($data, $where, function() use ($where) {
    		if ($this->isUpdate) {
    			return $this->_update(function() {
    				if(empty($this->data)) {
		            	return false;
		            } else {
		            	if(!empty($this->_id)) {
		            		return self::es()->id($this->_id)->update($this->data);
		            	} else {
		            		if(!empty($where)) {
								$q = self::es()->ormWhere($where);
		            			if(!empty($q)) {
		            				return $q->update($this->data);
		            			}
		            		}
		            		$this->error = 'not found data to update.';
		            		return false;
		            	}
		            }
    			});
	        } else {

	        	return $this->_create(function() {

	        		$q = self::es();
	        		if(!empty($this->_id)) {
	        			$q->id($this->_id);
	        		}
	        		return $q->insert($this->data);
		           	
	        	});
	        }
    	});


    }





    protected function _update($fun) {

    	// 自动更新
	    $this->autoCompleteData($this->update);

    	// 事件回调
        if (false === $this->trigger('before_update', $this)) {
            return false;
        }

        // 去除没有更新的字段
        foreach ($this->data as $key => $val) {
            if (!in_array($key, $this->change)) {
                unset($this->data[$key]);
            }
        }

        $result = $fun();
        // 更新回调
        $this->trigger('after_update', $this);

        return $result;
    }


    protected function _create($fun) {
    	// 自动写入
        $this->autoCompleteData($this->insert);

        if (false === $this->trigger('before_insert', $this)) {
            return false;
        }

        $result = $fun();

        // 新增回调
        $this->trigger('after_insert', $this);

        return $result;
    }


    protected function _save($data, $where, $fun) {
    	if (!empty($data)) {
            // 数据对象赋值
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
            if (!empty($where)) {
                $this->isUpdate = true;
            }
        }

        // 数据自动验证
        if (!$this->validateData()) {
            return false;
        }

        // 检测字段
        if (!empty($this->field)) {
            foreach ($this->data as $key => $val) {
                if (!in_array($key, $this->field)) {
                    unset($this->data[$key]);
                }
            }
        }

        // 数据自动完成
        $this->autoCompleteData($this->auto);

        // 事件回调
        if (false === $this->trigger('before_write', $this)) {
            return false;
        }

        $result = $fun();

        // 写入回调
        $this->trigger('after_write', $this);

        // 标记为更新
        $this->isUpdate = true;
        // 清空change
        $this->change = [];
        return $result;
    }

    /**
     * 保存多个数据到当前数据对象
     * @access public
     * @param array $data 数据
     * @return integer
     */
    public function saveAll($dataSet) {
        foreach ($dataSet as $data) {
            $result = $this->isUpdate(false)->save($data, [], false);
        }
        return $result;
    }


    /**
     * 删除当前的记录
     * @access public
     * @return integer
     */
    public function delete($where = []) {

    	return $this->_delete($where, function() use ($where) {
    		// 有查询
    		if(!empty($where)) {
				$q = self::es()->ormWhere($where);
				if(!empty($q)) {
					return  $q->delete();
				}
	        } else {
	        	// 有ID
		        if(!empty($this->_id)) {
		        	return self::es()->id($this->_id)->delete();
		        }
	        }
	        $this->error = 'not found data to update.';
	        return false;
    	});

    }


    protected function _delete($where, $fun) {
    	if (false === $this->trigger('before_delete', $this)) {
            return false;
        }

        $result = $fun();

        $this->trigger('after_delete', $this);
        return $result;
    }


    /**
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 设置自动完成的字段
     * @access public
     * @param array $fields 需要自动完成的字段（ 规则通过修改器定义）
     * @return $this
     */
    public function auto($fields) {
        $this->auto = $fields;
        return $this;
    }


    /**
     * 设置字段验证
     * @access public
     * @param array|bool $rule 验证规则 true表示自动读取验证器类
     * @param array $msg 提示信息
     * @return $this
     */
    public function validate($rule = true, $msg = []) {
        if (is_array($rule)) {
            $this->validate = [
                'rule' => $rule,
                'msg'  => $msg,
            ];
        } else {
            $this->validate = true === $rule ? $this->name : $rule;
        }
        return $this;
    }


    /**
     * 自动验证当前数据对象值
     * @access public
     * @return bool
     */
    public function validateData() {
    	if (!empty($this->validate)) {
            $info = $this->validate;
            if (is_array($info)) {
                $validate = Loader::validate(Config::get('default_validate'));
                $validate->rule($info['rule']);
                $validate->message($info['msg']);
            } else {
                $name = is_string($info) ? $info : $this->name;
                if (strpos($name, '.')) {
                    list($name, $scene) = explode('.', $name);
                }
                $validate = Loader::validate($name);
                if (!empty($scene)) {
                    $validate->scene($scene);
                }
            }
            if (!$validate->check($this->data)) {
                $this->error = $validate->getError();
                return false;
            }
            $this->validate = null;
        }
        return true;
    }


    /**
     * 数据自动完成
     * @access public
     * @param array $auto 要自动更新的字段列表
     * @return void
     */
    protected function autoCompleteData($auto = []) {
        foreach ($auto as $field => $value) {
            if (is_integer($field)) {
                $field = $value;
                $value = null;
            }
            if (!in_array($field, $this->change)) {
                // 更新时间字段
                if($field == $this->autoTimeField[1]) {
                    $value = null;
                } else {
                    $value = isset($this->data[$field]) ? $this->data[$field] : $value;
                }
                $this->__set($field, $value);
            }
        }
    }


    /**
     * 设置允许写入的字段
     * @access public
     * @param bool $update
     * @return $this
     */
    public function allowField($field) {
        $this->field = $field;
        return $this;
    }

    /**
     * 是否为更新数据
     * @access public
     * @param bool $update
     * @return $this
     */
    public function isUpdate($update = true) {
        $this->isUpdate = $update;
        return $this;
    }


    /**
     * 注册回调方法
     * @access public
     * @param string $event 事件名
     * @param callable $callback 回调方法
     * @param bool $override 是否覆盖
     * @return void
     */
    public static function event($event, $callback, $override = false) {
        if ($override) {
            static::$event[$event] = [];
        }
        static::$event[$event][] = $callback;
    }

    /**
     * 触发事件
     * @access protected
     * @param string $event 事件名
     * @param mixed $params 传入参数（引用）
     * @return bool
     */
    protected function trigger($event, &$params) {
        if (isset(static::$event[$event])) {
            foreach (static::$event[$event] as $callback) {
                if (is_callable($callback)) {
                    $result = call_user_func_array($callback, [ & $params]);
                    if (false === $result) {
                        return false;
                    }
                }
            }
        }
        return true;
    }


    /**
     * 命名范围
     * @access public
     * @param string|Closure $name 命名范围名称 逗号分隔
     * @param mixed $params 参数调用
     * @return \think\Model
     */
    public static function scope($name, $params = []) {
        $model = new static();
        $class = self::es();
        if ($name instanceof \Closure) {
            call_user_func_array($name, [ & $class, $params]);
        } elseif ($name instanceof Query) {
            return $name;
        } else {
            $names = explode(',', $name);
            foreach ($names as $scope) {
                $method = 'scope' . $scope;
                if (method_exists($model, $method)) {
                    $model->$method($class, $params);
                }
            }
        }
        return $class;
    }

    public function __call($method, $args) {
        if (method_exists($this, 'scope' . $method)) {
            // 动态调用命名范围
            $method = 'scope' . $method;
            $class  = self::es();
            array_unshift($args, $class);
            call_user_func_array([$this, $method], $args);
            return $this;
        } else {
            throw new \think\Exception(__CLASS__ . ':' . $method . ' method not exist');
        }
    }

	public static function __callStatic($method, $params) {
        return call_user_func_array([self::es(), $method], $params);
    }


    public static function tableName() {

        $model = get_called_class();

        $index = static::$index;
        $type  = static::$type;
        $prefix = '';


        if (empty(static::$index) || empty(static::$type)) {
            $clsname = basename(str_replace('\\', '/', $model));
            $table = Loader::parseName($clsname);
            $conn = self::$links[$model]->getConnection();
            $prefix = $conn->getPrefix();
            if(0 === strpos($table, strtolower($prefix))) {
                $table = substr($table, strlen($prefix));
            }
            list($index, $type) = explode('_', $table, 2);
        }

        return [$index, $type];
    }


	/**
     * 初始化ES对象
     * @access public
     * @return \bb\es\Query
     */
    public static function es() {
        $model = get_called_class();
        if (!isset(self::$links[$model])) {
            $conn = ES::connect(static::$config);
        	self::$links[$model] = $conn->newQuery();
        }
        list($index, $type) = self::tableName();
        self::$links[$model]->index($index)->type($type);
        // 设置当前模型 确保查询返回模型对象
        self::$links[$model]->model($model);
        // 返回当前数据库对象
        return self::$links[$model];
    }


    /**
     * 初始化ES对象
     * @access public
     * @return \bb\es\Scheme
     */
    public static function scheme() {
        $conn = static::es()->getConnection();
        $scheme = $conn->scheme();
        list($index, $type) = self::tableName();
        $scheme->index($index)->type($type);
        return $scheme;
    }


	/**
     * 修改器 设置数据对象的值
     * @access public
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name, $value) {
    	// 分数处理
    	if($name == 'score') {$this->_score = (float) $value; return;}
    	if($name == 'id') {$this->_id = strval($value); return;}

        if(!in_array($name, array_keys(static::$mapping))) {
            return;
        }

        if (is_null($value) && in_array($name, $this->autoTimeField)) {
            // 自动写入的时间戳字段
            $value = Date::t(NOW_TIME)->format();
        } else {
            // 检测修改器
            $method = 'set' . Loader::parseName($name, 1) . 'Attr';
       		// $method = str_replace('.', '_', $method);
            if (method_exists($this, $method)) {
                $value = $this->$method($value, $this->data);
            } elseif (isset($this->field_type[$name])) {
                // 类型转换
                $type = $this->field_type[$name];
                if (strpos($type, ':')) {
                    list($type, $param) = explode(':', $type, 2);
                }
                switch ($type) {
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'float':
                        if (empty($param)) {
                            $value = (float) $value;
                        } else {
                            $value = (float) number_format($value, $param);
                        }
                        break;
                    case 'boolean':
                        $value = (bool) $value;
                        break;
                    case 'datetime':
                    	$value = strtotime($value);
                        break;
                    case 'date':
                    	$value = Date::t($value)->format();
                    	break;
                    case 'object':
                        if (is_object($value)) {
                            $value = json_encode($value, JSON_FORCE_OBJECT);
                        }
                        break;
                    case 'json':
                    case 'array':
                        if (is_array($value)) {
                            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        break;
                }
            }
        }


   //      $names = explode('.', $name);

   //      // 多维结构处理
   //      if(count($names) > 1) {

   //      	$x = &$this->data;
			// foreach($names as $i) $x = &$x[$i];
			// // 标记字段更改
	  //       if (!empty($x) || ($x != $value && !in_array($name, $this->change))) {
	  //           $this->change[] = $name;
	  //       }
			// $x = $value;

   //      } else {

	        // 标记字段更改
	        if (!isset($this->data[$name]) || ($this->data[$name] != $value && !in_array($name, $this->change))) {
	            $this->change[] = $name;
	        }
	        // 设置数据对象属性
	        $this->data[$name] = $value;
        // }

    }

    /**
     * 获取器 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name) {
    	// 分数处理
    	if($name == 'score') return $this->_score;
    	if($name == 'id') return $this->_id;

        if(!in_array($name, array_keys(static::$mapping))) {
            return null;
        }

  //   	$names = explode('.', $name);
  //       // 多维结构处理
  //       if(count($names) > 1) {
  //       	$x = &$this->data;
		// 	foreach($names as $i) $x = &$x[$i];
		// 	$value = $x;
  //       } else {
  //       	$value = isset($this->data[$name]) ? $this->data[$name] : null;
		// }

        $value = isset($this->data[$name]) ? $this->data[$name] : null;


        if (in_array($name, $this->autoTimeField)) {
            // 自动写入的时间戳字段
            $value = Date::t($value);
        } else {
            // 检测属性获取器
            $method = 'get' . Loader::parseName($name, 1) . 'Attr';
            // $method = str_replace('.', '_', $method);
            if (method_exists($this, $method)) {
                return $this->$method($value, $this->data);
            } elseif (!is_null($value) && isset($this->field_type[$name])) {
                // 类型转换
                $type = $this->field_type[$name];
                if (strpos($type, ':')) {
                    list($type, $param) = explode(':', $type, 2);
                }
                switch ($type) {
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'float':
                        if (empty($param)) {
                            $value = (float) $value;
                        } else {
                            $value = (float) number_format($value, $param);
                        }
                        break;
                    case 'boolean':
                        $value = (bool) $value;
                        break;
                    case 'datetime':
                        $format = !empty($param) ? $param : $this->dateFormat;
                        $value  = date($format, $value);
                        break;
                    case 'date':
                        $value = Date::t($value);
                        break;
                    case 'json':
                    case 'array':
                        $value = json_decode($value, true);
                        break;
                    case 'object':
                        $value = json_decode($value);
                        break;
                }
            } elseif (is_null($value) && method_exists($this, $name)) {
                // // 获取关联数据
                // $value = $this->relation->getRelation($name);
                // // 保存关联对象值
                // $this->data[$name] = $value;
            }
        }

        return $value;
    }


	/**
     * 检测数据对象的值
     * @access public
     * @param string $name 名称
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * 销毁数据对象的值
     * @access public
     * @param string $name 名称
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function __toString()
    {
        return json_encode($this->data);
    }

    // JsonSerializable
    public function jsonSerialize()
    {
        return $this->data;
    }

    // ArrayAccess
    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }

    public function offsetExists($name)
    {
        return $this->__isset($name);
    }

    public function offsetUnset($name)
    {
        $this->__unset($name);
    }

    public function offsetGet($name)
    {
        return $this->__get($name);
    }

    /**
     * 解序列化后处理
     */
    public function __wakeup() {
        $this->initialize();
    }

    
    public function clearChange() {
        $this->change = [];
    }


    public function info() {
        list($index, $type) = self::tableName();
        return [
            'index' => $index,
            'type'  => $type,
            'data'  => $this->data
        ];
    }

    public function toArray() {
        return $this->data;
    }


    public static function getMapping() {
        return static::$mapping;
    }

    public static function getIndex() {
        return static::$index;
    }

    public static function getType() {
        return static::$type;
    }

    public static function getTable() {
        return [static::$index, static::$type];
    }


    public static function getID() {
        return $this->_id;
    }

}