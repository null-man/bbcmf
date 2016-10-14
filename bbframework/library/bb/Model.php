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
BT('bb/Cls');

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Events\Dispatcher;

class Model extends Eloquent {

	use \bt\bb\Cls;

	protected static $instances = [];

	protected $config = [];

    // 后端字段验证
    protected $validate = [];

    protected $auto = [];

    protected $field = [];

	protected function _initialize() {}

	public function __construct(array $attributes = [], $table = '', $config = []) {

		parent::__construct($attributes);

		// 获得当前类名
		$clsname = $this->getClassName();
		$key = $clsname.'/'.$table;

		// 设置表和连接
		if(isset(self::$instances[$key])) {
			$this->table = self::$instances[$key]['table'];
			$this->connection = self::$instances[$key]['connection'];
		} else {
			// 设置表
			if (!empty($table)) {
	        	$this->table = $table;
	    	} elseif (empty($this->table)) {
	        	$this->table = \think\Loader::parseName($clsname);
	    	}
	        // 设置连接
	        if(!empty($config)) {
	        	$this->config = $config;
	        }
	        $this->connection = DB::connectName($this->config);

	        self::$instances[$key]['table'] = $this->table;
	        self::$instances[$key]['connection'] = $this->connection;
		}

		$this->fillable = $this->field;

	}

	protected static function boot() {
		$dispatcher  = static::getEventDispatcher();
		if(empty($dispatcher)) {
			static::setEventDispatcher(new Dispatcher());
		}
        parent::boot();
    }


	/**
	 * 一对一关系
	 *
	 * @param  string  $related
	 * @param  string  $foreignKey
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function hasOne($related, $foreignKey = null, $localKey = null) {
		$related = $this->parseClassName($related);
		return Eloquent::hasOne($related, $foreignKey, $localKey);
	}



	/**
	 * 反一对一或一对多关系
	 *
	 * @param  string  $related
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $relation
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null) {
		$related = $this->parseClassName($related);
		return Eloquent::belongsTo($related, $foreignKey, $otherKey, $relation);
	}





	/**
	 * 一对多关系
	 *
	 * @param  string  $related
	 * @param  string  $foreignKey
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function hasMany($related, $foreignKey = null, $localKey = null) {
		$related = $this->parseClassName($related);
		return Eloquent::hasMany($related, $foreignKey, $localKey);
	}


	/**
	 * 多对多关系
	 *
	 * @param  string  $related
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $relation
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null) {
		$related = $this->parseClassName($related);
		return Eloquent::belongsToMany($related, $table, $foreignKey, $otherKey, $relation);
	}


	/**
	 * 远程一对多关系
	 *
	 * @param  string  $related
	 * @param  string  $through
	 * @param  string|null  $firstKey
	 * @param  string|null  $secondKey
	 * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null) {
		$related = $this->parseClassName($related);
		$through = $this->parseClassName($through);	
		return Eloquent::hasManyThrough($related, $through, $firstKey, $secondKey);
	}





	/*
	 * 多态一对一关系
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $id
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\MorphOne
	 */
	public function morphOne($related, $name, $type = null, $id = null, $localKey = null) {
		$related = $this->parseClassName($related);
		return Eloquent::morphOne($related, $name, $type, $id, $localKey);
	}

	/**
	 * 反多态一对多关系
	 *
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $id
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function morphTo($name = null, $type = null, $id = null) {
		return Eloquent::morphTo($name, $type, $id);
	}


	/**
	 * 多态一对多关系
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $id
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function morphMany($related, $name, $type = null, $id = null, $localKey = null) {
		$related = $this->parseClassName($related);
		return Eloquent::morphMany($related, $name, $type, $id, $localKey);
	}


	/**
	 * 反多态多对多关系
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function morphedByMany($related, $name, $table = null, $foreignKey = null, $otherKey = null) {
		$related = $this->parseClassName($related);
		return Eloquent::morphedByMany($related, $name, $table, $foreignKey, $otherKey);
	}


	/**
	 * 多态多对对关系
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  bool    $inverse
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false) {
		$related = $this->parseClassName($related);
		return Eloquent::morphToMany($related, $name, $table, $foreignKey, $otherKey, $inverse);
	}


	public function save(array $options = array()) {
		// 数据自动验证
        if (!$this->validateData()) {
            return false;
        }

        // 检测字段
        if (!empty($this->field)) {
            foreach ($this->attributes as $key => $val) {
                if (!in_array($key, $this->field) || is_array($val)) {
                    unset($this->attributes[$key]);
                }
            }
        }


        // 数据自动完成
        $this->autoCompleteData($this->auto);


		return parent::save($options);
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
                $validate = \think\Loader::validate(\think\Config::get('default_validate'));
                $validate->rule($info['rule']);
                $validate->message($info['msg']);
            } else {
                $name = is_string($info) ? $info : $this->name;
                if (strpos($name, '.')) {
                    list($name, $scene) = explode('.', $name);
                }
                $validate = \think\Loader::validate($name);
                if (!empty($scene)) {
                    $validate->scene($scene);
                }
            }
            if (!$validate->check($this->attributes)) {
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
        	if(!isset($this->attributes[$field])) {
        		$this->__set($field, $value);
        	}
        }
    }


    public function getData($field = null) {
    	if(is_null($field)) return $this->attributes;
    	return $this->attributes[$field];
    }

}
