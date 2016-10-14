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

namespace bb\es;

use bb\ES;
use bb\es\Builder;
use Elasticsearch\ClientBuilder;
use bb\Date;

class Query {

	// ES Connection对象实例
    protected $connection;

   	// ES客户端
   	protected $client;

   	// 查询参数
    protected $options = [];

    // 聚合参数
    protected $aggs = ['aggs' => []];

    // 聚合当前操作key
    protected $aggs_key = null;

    // 默认客户端参数
    protected $params_clinet_default = ['ignore' => ['404', '409']];

    // 原始数据输出
    static $raw_output = false;

    // 批量操作参数
    protected $bulk_options = [];

    // 批量操作最大值
    protected $bulk_max = 1000;

    // 查询返回最大数量
    protected $max_hits = -1;

    // where exists查询
    protected $where_exists = [];

    // 索引前缀
    protected $prefix = '';

    // 绑定模型
    protected $model;

    // 批量类型
    const BULK_NO = -1;
    const BULK_ID = 1;
    const BULK_DATA = 2;

    // 查询类型
    const GET_ID = 0;
    const GET_IDS = 1;
    const GET_ALL = 2;
    const GET_WHERE = 3;


    /**
     * 打印参数
     * @access public
     */
    public function dumpOptions() {
        dump($this->options);
    }


    /**
     * 新建查询
     * @access public
     */
    public function newQuery($options = []) {
        return new Query($this->connection, $this->client, $options);
    }


    /**
     * 设置客户端参数
     * @access public
     * @param mixed $params 参数
     */
    public function setClinetDefault($params) {
    	$this->params_clinet_default = $params;
    }


    /**
     * 设置是否原始数据输出
     * @access public
     * @param boolean $raw_output 是否
     */
    static public function setRawOutput($raw_output) {
        self::$raw_output = $raw_output;
    }


    /**
     * 设置批量操作最大值
     * @access public
     * @param integer $max 数量
     */
    public function setBulkMax($max) {
        $this->bulk_max = $max;
    }


    /**
     * 设置查询数量最大值
     * @access public
     * @param integer $max 数量
     */
    public function setMaxHits($max) {
        $this->max_hits = $max;
    }


    /**
     * 架构函数
     * @access public
     * @param object $connection 数据库对象实例
     */
    public function __construct($connection = '', $client = null, $options = []) {
        $this->connection = $connection ?: ES::connect();
        $this->client = is_null($client) ? ClientBuilder::fromConfig($connection->getConfig()) : $client;
        $this->options = $options;
        // 设置前缀
        $this->prefix = $this->connection->getPrefix();
    }
        

    public function __call($method, $args) {
    	if(strpos($method, '_') == 0) {
    		$method = substr($method, 1);
    		return call_user_func_array([$this->client, $method], $args);
    	}
    }


    /**
     * 获取当前的builder实例对象
     * @access protected
     * @return \bb\es\Builder
     */
    protected function builder() {
    	$builder = Builder::instance();
        // 设置当前查询对象
        $builder->setQuery($this);
        return $builder;
    }


    /**
     * 指定索引
     * @access public
     * @param string $index 索引名 
     * @return $this
     */
    public function index($index, $withPrefix = true) {
        if($withPrefix && !empty($this->prefix)) $index = $this->prefix . $index;
        $this->options['index'] = $index;
        return $this;
    }


    /**
     * 指定类型
     * @access public
     * @param string $index 索引名 
     * @return $this
     */
    public function type($type) {
        $this->options['type'] = $type;
        return $this;
    }


    /**
     * 指定ID
     * @access public
     * @param $id ID值
     * @return $this
     */
    public function id($id) {
        if(is_null($id)) return;
        $id = func_num_args() > 1 ? func_get_args() : $id;

        isset($this->options['id']) ? : $this->options['id'] = [];

        if(is_array($id)) {
            $this->options['id'] = array_merge($this->options['id'], $id);
        } else {
            $this->options['id'][] = $id;
        }

        return $this;
    }


    /**
     * 指定路由
     * @access public
     * @param $routing 路由
     * @return array|string|false
     */
    public function routing($routing) {
        $this->options['routing'] = $routing;
        return $this;
    }


    /**
     * 指定更新信息
     * @access public
     * @param $data 信息
     * @return array
     */
    public function data($data) {
        $this->options['data'] = $data;
        return $this;
    }

    /**
     * 指定脚本
     * @access public
     * @param $script 脚本
     * @param $params 参数
     * @return array
     */
    public function script($script, $params) {
        $this->options['script'] = [
            'script' => $script,
            'params' => $params
        ];
        return $this;
    }

    /**
     * 指定upsert参数
     * @access public
     * @param mixed $data 数据
     * @return string
     */
    public function upsert($data) {
        $this->options['upsert'] = $data;
        return $this;
    }


    /**
     * 指定parent参数
     * @access public
     * @param mixed $data 数据
     * @return string
     */
    public function parent($data) {
        $this->options['parent'] = $data;
        return $this;
    }


    /**
     * 指定timeout参数
     * @access public
     * @param integer $time 时间
     * @return string
     */
    public function timeout($time) {
        $this->options['timeout'] = $time;
        return $this;
    }


    /**
     * 指定查询数量
     * @access public
     * @param mixed $offset 起始位置
     * @param mixed $length 查询数量
     * @return $this
     */
    public function limit($offset, $length = null) {
        if (is_null($length) && strpos($offset, ',')) {
            list($offset, $length) = explode(',', $offset);
        }
        if(empty($length)) {
        	$this->options['from'] = 0;
        	$this->options['size'] = intval($offset);
        } else {
        	$this->options['from'] = intval($offset);
        	$this->options['size'] = intval($length);
        }
        return $this;
    }


    /**
     * 原始形式参数导入
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function raw($params) {
    	$this->options['raw'] = $params;
    	return $this;
    }


    /**
     * 排序
     * @access public
     * @param string $key 
     * @param string $order
     * @return $this
     */
    public function orderBy($key, $order = null) {
        isset($this->options['sort']) ?: $this->options['sort'] = [];
        if($order == null) {
            if(is_string($key)) {
                $order = 'asc';
            } else {
                foreach($key as $k) {
                    $this->options['sort'][] = $k[0].':'.$k[1];
                }
                return $this;
            }
        }
        $this->options['sort'][] = $key.':'.$order;
        return $this;
    }


    /**
     * 字段
     * @access public
     * @param string $fields 
     * @return $this
     */
    public function field($fields) {
        $fields = func_num_args() > 1 ? func_get_args() : $fields;
        $this->options['fields'] = $fields;
        return $this;
    }


    /**
     * 直接dsl查询
     * @access public
     * @param array $ 
     * @return $this
     */
    public function query($dsl) {
        $this->options['query'] = $dsl;
        return $this;
    }


    /**
     * 条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function where($field, $op = null, $condition = null) {
        list($must, $where) = $this->parseWhereExp($field, $op, $condition);
        if (!empty($where)) {
            if($must) {
                isset($this->options['where']['must']) ?: $this->options['where']['must'] = [];
                $this->options['where']['must'][] = $where;
            } else {
                isset($this->options['where']['must_not']) ? : $this->options['where']['must_not'] = [];
                $this->options['where']['must_not'][] = $where;
            }
        }
        return $this;
    }


    /**
     * 或条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function orWhere($field, $op = null, $condition = null) {
        $where = $this->parseWhereExp($field, $op, $condition);
        if (!empty($where)) {
            isset($this->options['where']['should']) ?: $this->options['where']['should'] = [];
            $this->options['where']['should'] = array_merge($this->options['where']['should'], $where);
        }
        return $this;
    }


    /**
     * where exists条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function whereExists($query) {
        $this->where_exists[] = ['&', $query];
        return $this;
    }


    /**
     * 或where exists条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function orWhereExists($query) {
        $this->where_exists[] = ['|', $query];
        return $this;
    }


    /**
     * where not exists条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function whereNotExists($query) {
        $this->where_exists[] = ['!&', $query];
        return $this;
    }


    /**
     * 或where not exists条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function orWhereNotExists($query) {
        $this->where_exists[] = ['!|', $query];
        return $this;
    }


    /**
     * Match条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function match($field, $op = null, $condition = null) {
        list($must, $match) = $this->parseWhereExp($field, $op, $condition);
        if (!empty($match)) {
            if($must) {
                isset($this->options['match']['must']) ?: $this->options['match']['must'] = [];
                $this->options['match']['must'][] = $match;
            } else {
                isset($this->options['match']['must_not']) ? : $this->options['match']['must_not'] = [];
                $this->options['match']['must_not'][] = $match;
            }
        }
        return $this;
    }


    /**
     * 或Match条件
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function orMatch($field, $op = null, $condition = null) {
        list($must, $match) = $this->parseWhereExp($field, $op, $condition);
        if (!empty($match)) {
            if($must) {
                isset($this->options['match']['must']) ?: $this->options['match']['must'] = [];
                $this->options['match']['must'][] = $match;
            } else {
                isset($this->options['match']['must_not']) ? : $this->options['match']['must_not'] = [];
                $this->options['match']['must_not'][] = $match;
            }
        }
        return $this;
    }


    /**
     * 设置客户端参数
     * @access public
     * @param mixed $params 参数
     * @return $this
     */
    public function client($params) {
    	$this->options['client'] = array_merge($this->params_clinet_default, $params);
    	return $this;
    }


    /**
     * 更新记录
     * @access public
     * @param mixed $data 数据
     * @param mixed $upsert upsert数据
     * @return string
     */
    public function update($data = []) {
        // 分析查询表达式
        list($bulk, $options) = $this->parseExpress(__FUNCTION__, $data);

        if($bulk === false) return false;

        $dsl = '';


        if($bulk == Query::BULK_NO) {
            $dsl = $this->builder()->update($options);
            $ret = $this->client->update($dsl);
            return $this->parseResult($ret, __FUNCTION__, $dsl);
        } elseif($bulk == Query::BULK_ID) {
            $dsl = $this->builder()->bulkID($options, 'update');
            $this->bulk_options = [];
            $ret = $this->bulk($dsl);
            return $this->parseResult($ret, 'bulk', $dsl);
        } elseif($bulk == Query::BULK_DATA) {
            $dsl = $this->builder()->bulkData($options, 'update');
            $this->bulk_options = [];
            $ret = $this->bulk($dsl);
            return $this->parseResult($ret, 'bulk', $dsl);
        }
        return false;
    }


    /**
     * 删除记录
     * @access public
     * @param array $id 表达式
     * @return integer
     */
    public function delete($id = null) {
        // 分析查询表达式
        $this->id($id);
        list($bulk, $options) = $this->parseExpress(__FUNCTION__);

        if($bulk === false) return false;

        $dsl = '';

        if($bulk == Query::BULK_NO) {
            $dsl = $this->builder()->delete($options);
            $ret = $this->client->delete($dsl);
            return $this->parseResult($ret, __FUNCTION__, $dsl);
        } elseif($bulk == Query::BULK_ID) {
            $dsl = $this->builder()->bulkID($options, 'delete');
            $this->bulk_options = [];
            $ret = $this->bulk($dsl);
            return $this->parseResult($ret, 'bulk', $dsl);
        }
        return false;
    }


    /**
     * 插入记录
     * @access public
     * @param mixed $data 数据
     * @param boolean $replace 是否replace
     * @return integer
     */
    public function insert($data = [], $replace = false) {
        // 分析查询表达式
        list($bulk, $options) = $this->parseExpress(__FUNCTION__, $data);

        if($bulk == Query::BULK_NO) {
            $dsl = $this->builder()->insert($options);
            $ret = $replace ? $this->client->index($dsl) : $this->client->create($dsl);
            return $this->parseResult($ret, __FUNCTION__, $dsl);
        } elseif($bulk == Query::BULK_ID) {
            $dsl = $this->builder()->bulkID($options, $replace ? 'upsert' : 'insert');
            $this->bulk_options = [];
            $ret = $this->bulk($dsl);
            return $this->parseResult($ret, 'bulk', $dsl);
        } elseif($bulk == Query::BULK_DATA) {
            $dsl = $this->builder()->bulkData($options, $replace ? 'upsert' : 'insert');
            $this->bulk_options = [];
            $ret = $this->bulk($dsl);
            return $this->parseResult($ret, 'bulk', $dsl);
        }
        return false;
        
    }


    /**
     * 新建一个bulk
     * @access public
     * @return $this
     */
    public function newBulk() {
        $this->bulk_options = [];
        return $this;
    }


    /**
     * 根据option添加一个bulk参数
     * @access public
     * @param mixed $operation 操作
     * @param mixed $data 是否replace
     * @return integer
     */
    public function bulking($operation, $data = []) {
        // 判断操作是否合法
        if(!in_array($operation, ['insert', 'update', 'delete', 'index'])) return false;

        list($bulk, $options) = $this->parseExpress(__FUNCTION__, $data);

        $this->bulk_options[] = [$bulk, $options, $operation];

        $this->options['index'] = $options['index'];
        $this->options['type'] = $options['type'];

        return $this;
    }


    /**
     * 执行bulk
     * @access public
     * @param mixed $dsl dsl语句
     * @return array
     */
    public function bulk($dsl = []) {
        $dsl = $this->builder()->bulk($this->bulk_options, $dsl);
        $ret = [];
        if(count($dsl) > $this->bulk_max) {
            for($i = 0; $i < count($dsl); $i = $i + $this->bulk_max) {
                $_ret = $this->client->bulk(array_slice($dsl, $i, $this->bulk_max));
                $ret = array_merge($ret, $_ret);
            }
        } else {
            $ret = $this->client->bulk($dsl);
        }
        $this->bulk_options = [];
        $this->options = [];
        return $this->parseResult($ret, __FUNCTION__, $dsl);
    }


    /**
     * 分析表达式（可用插入更新删除操作）
     * @access public
     * @return array
     */
    public function parseExpress($type, $data = []) {
        $options = $this->options;

        $bulk = Query::BULK_NO;

        // 解析raw参数
        if(isset($options['raw'])) {
            $options = array_merge($options, $options['raw']);
            unset($options['raw']);
        }

        if($type != 'bulking') {  // bulk不处理where
            // 处理where 做一个查询
            if(isset($options['where']) || isset($options['match'])) {
                $query = $this->newQuery($options);
                $ret = $query->field(false)->get([], true);
                $options['id'] = [];
                foreach($ret['hits']['hits'] as $i) {
                    $options['id'][] = $i['_id'];
                }
                unset($options['where']);
                unset($options['match']);
            }

            // ID判断
            if($type != 'insert') { // insert不处理ID
                if(empty($options['id'])) {
                    // 不存在ID 返回错误
                    return [false, $options];
                }
            }
        }



        // 添加默认客户端参数
        if(!isset($options['client']) && !empty($this->params_clinet_default)) {
            $options['client'] = $this->params_clinet_default;
        }

        // 数据组合
        if($type != 'delete')  { // data操作
            isset($options['data']) && $data = array_merge($options['data'], $data);
            isset($options['body']) && $data = array_merge($options['body'], $data);
            $options['data'] = $data;
        }


        if($type == 'update') {
            // 脚本字段
            isset($options['script']) ? : $options['script'] = [];
            isset($options['body']['script']) && $options['script'] = array_merge($options['script'], $options['body']['script']);
            if(empty($options['script'])) {
                unset($options['script']);
            }

            // upsert字段
            isset($options['upsert']) ? : $options['upsert'] = [];
            isset($options['body']['upsert']) && $options['upsert'] = array_merge($options['upsert'], $options['body']['upsert']);
            if(empty($options['upsert'])) {
                unset($options['upsert']);
            }
        }

        unset($options['body']);

        // 是否批量判断
        $bulk = Query::BULK_NO;
        if(isset($options['id']) && count($options['id']) > 1) {
            $bulk = Query::BULK_ID;
        }
        if(isset($options['id']) && count($options['id']) == 1) {
            $options['id'] = $options['id'][0];
        }
        if($type != 'delete') { // data操作
            if(isset($options['data']) && isset($options['data'][0])) {
                $bulk = Query::BULK_DATA;
            }
        }

        $this->options = [];
        return [$bulk, $options];
    }


    /**
     * 解析结果
     * @access public
     * @param mixed $ret 原始结果
     * @param string $type 类型
     * @return string
     */
    protected function parseResult($ret, $type, $dsl = '') {
        if(!is_null($this->model)) {
            return $this->ormRet($ret, $type, $dsl);
        }
        // 直接输出原始结果
        if(self::$raw_output) return $ret;

        switch ($type) {
            // first方法
            case 'first':
                if(!empty($ret['hits']['hits'])) return $ret['hits']['hits'][0];
                return [];
            // exists方法
            case 'exists':
                return $ret['exists'];
            // count方法
            case 'count':
                return $ret['count'];
            // get方法
            case 'get':
                if(isset($ret['hits'])) return $ret['hits']['hits'];
                if(isset($ret['_source'])) return $ret;
                return [];
            // update方法
            case 'update':
                return isset($ret['error']) || $ret['_shards']['successful'] == 0 || $ret['_shards']['failed'] == 1 ? false : $ret['_id']; 
            // delete方法
            case 'delete':
                return $ret['found'] ? $ret['_id'] : false;
            // insert方法
            case 'insert':
                if(isset($ret['error'])) return false;
                return $ret['_id'];
            case 'scroll':
                return $ret['_scroll_id'];
            case 'scrollGet':
                if(isset($ret['hits'])) return $ret['hits']['hits'];
                return [];
            case 'aggs':
                return $ret['aggregations'];
            // bulk方法
            case 'bulk':
            default:
                return $ret;
        }
    }


    /**
     * 查找记录
     * @access public
     * @param array $extra 表达式
     * @return array|string|false
     */
    public function get($extra = [], $force_raw = false) {

        list($type, $options) = $this->parseWhere($extra);

        if(!$this->parseWhereExists()) return false;

        $ret = [];
        $dsl = '';


        if($type == Query::GET_ID) { // 直接ID查询
            $dsl = $this->builder()->selectID($options);
            $ret = $this->client->get($dsl);
        } elseif($type == Query::GET_IDS) { // 直接多个ID查询
            $dsl = $this->builder()->selectIDS($options);
            $ret = $this->client->mget($dsl);
        } elseif($type == Query::GET_ALL) { // 查询全部
            $dsl = $this->builder()->selectAll($options);
            $ret = $this->client->search($dsl);
        } elseif($type == Query::GET_WHERE) { // 条件查询
            $dsl = $this->builder()->selectWhere($options);
            $ret = $this->client->search($dsl);
        }

        if($force_raw === true) return $ret;
        if(is_string($force_raw)) {
            return $this->parseResult($ret, $force_raw, $dsl);
        }
        return $this->parseResult($ret, __FUNCTION__, $dsl);
    }


    /**
     * 第一条记录
     * @access public
     * @param array $extra 表达式
     * @return array|string|false
     */
    public function first($extra = [], $force_raw = false) {
        $this->limit(1);
        return $this->get($extra, __FUNCTION__);
    }


    /**
     * 查找记录数
     * @access public
     * @param array $extra 表达式
     * @return array|string|false
     */
    public function count($extra = [], $force_raw = false) {

        list($type, $options) = $this->parseWhere($extra, true);

        if(!$this->parseWhereExists($options)) return false;

        $ret = [];
        $dsl = '';

        if($type == Query::GET_ALL) {
            $dsl = $this->builder()->selectAll($options);
            $ret = $this->client->count($dsl);
        } elseif($type == Query::GET_WHERE) {
            $dsl = $this->builder()->count($options);
            $ret = $this->client->count($dsl);
        }

        if($force_raw) return $ret;
        return $this->parseResult($ret, __FUNCTION__, $dsl);
    }


    /**
     * 存在
     * @access public
     * @param array $extra 表达式
     * @return array|string|false
     */
    public function exists($extra = []) {

        list($type, $options) = $this->parseWhere($extra);

        if($type == Query::GET_ALL) return false;

        // if(!$this->parseWhereExists($options)) return false;

        $ret = [];
        $dsl = '';

        if($type == Query::GET_ID) { // 直接ID查询
            $dsl = $this->builder()->exists($options);
            $ret = $this->client->exists($dsl);
        } elseif($type == Query::GET_IDS) { // 直接多个ID查询
            $ret = true;
            $dsl = [];
            foreach($options['ids'] as $id) {
                $_options = $options;
                $_options['id'] = $id;
                unset($_options['ids']);
                $_dsl = $this->builder()->exists($_options);
                $_ret = $this->client->exists($_dsl);
                $dsl[] = $_dsl;
                if($_ret == false) { 
                    $ret = false;
                    break;
                }
            }
        } elseif($type == Query::GET_WHERE) { // 条件查询
            $dsl = $this->builder()->selectWhere($options);
            $ret = $this->client->searchExists($dsl);
            return $this->parseResult($ret, __FUNCTION__, $dsl);
        }

        return $this->parseResult($ret, __FUNCTION__, $dsl);
        
    }


    /**
     * 分析条件表达式（可用查询操作）
     * @access public
     * @return array
     */
    protected function parseWhere($extra, $count = false) {
        $options = $this->options;

        is_array($extra) ? $options = array_merge($options, $extra) : $options['id'] = [strval($extra)];

        // 解析raw参数
        if(isset($options['raw'])) {
            $options = array_merge($options, $options['raw']);
            unset($options['raw']);
        }

        // 添加默认客户端参数
        if(!isset($options['client']) && !empty($this->params_clinet_default)) {
            $options['client'] = $this->params_clinet_default;
        }

        // 设置_source, fields
        if(isset($options['fields']) && $options['fields'] === false) {
            $options['_source'] = false;
            unset($options['fields']);
        }

        // 处理ID
        if(!$count) { // 计数不处理ID, size
            if(isset($options['id'])) {
                if(count($options['id']) == 1) {
                    $options['id']= $options['id'][0];
                    $this->options = [];
                    return [Query::GET_ID, $options];
                }
                $options['ids'] = $options['id'];
                $this->options = [];
                return [Query::GET_IDS, $options];
            }

            if(isset($options['from']) && isset($options['size'])) {

            } else {
                // 处理size
                isset($options['size']) ?: $options['size'] = $this->max_hits;
                if($this->max_hits == -1) { // 查询一次以保证全部显示
                    unset($options['size']);
                    $query = $this->newQuery($options);
                    $size = $query->count([], true);
                    if(isset($size['count'])) {
                        $options['size'] = $size['count'];
                    } else {
                        $options['size'] = 0;
                    }
                }
            }
            
        }

        // 处理查询条件
        if(isset($options['where']) || isset($options['match'])) {
            $this->options = [];
            return [Query::GET_WHERE, $options];
        }

        $this->options = [];
        return [Query::GET_ALL, $options];
    }


    /**
     * 分析查询表达式
     * @access public
     * @param mixed $field 查询字段
     * @param mixed $op 查询表达式
     * @param mixed $condition 查询条件
     * @return $this
     */
    protected function parseWhereExp($field, $op, $condition) {

        $must = true;
        $where = [];

        if(is_null($op) && is_null($condition)) {
            return strpos($field, '!') === 0 ? 
                [$must, ['missing' => ['field' => substr($field, 1)]]] :
                [$must, ['exists' => ['field' => $field]]];
        }

        $op = strtolower($op);

        // 判断是非
        if(strpos($op, '!') === 0) {
            $must = false;
            $op = substr($op, 1);
        }


        if(in_array($op, ['=', 'eq', '==', 'term'])) { // 等于
            $where = ['term' => [$field => $condition]];
        } elseif(in_array($op, ['>', '<', '>=', '<=', 'gte', 'gt', 'lt', 'lte'])) { // 大小等于
            $op == '>' && $op = 'gt';
            $op == '<' && $op = 'lt';
            $op == '>=' && $op = 'gte';
            $op == '<=' && $op = 'lte';
            $where = ['range' => [$field => [$op => $condition]]];
        } elseif(in_array($op, ['between', 'range'])) { // 范围
            if(is_string($condition)) {
                $x = explode(',', $condition);
                $condition = [intval($x[0]), intval($x[1])];
            }
            $where = ['range' => [$field => ['gt' => $condition[0], 'lt' => $condition[1]]]];
        } elseif(in_array($op, ['null', 'isnull', 'is null', 'exists'])) { // 存在
            $where = ['missing' => ['field' => $field]];
        } elseif(in_array($op, ['notnull', 'not null', 'missing'])) { // 不存在
            $where = ['exists' => ['field' => $field]];
        } elseif(in_array($op, ['in', 'terms'])) {  // in
            if(is_string($condition)) {
                $condition = explode(',', $condition);
            }
            $where = ['terms' => [$field => $condition]];
        } elseif(in_array($op, ['match'])) { // 匹配
            $where = is_array($field) ? 
                ['multi_match' => ['query' => $condition, 'fields' => $field]] :
                ['match' => [$field => $condition]];
        } elseif(in_array($op, ['wildcard', 'wild'])) { // 通配符
            $where = ['wildcard' => ['field' => $condition]];
        } elseif(in_array($op, ['regexp', 're'])) { // 正则
            $where = ['regexp' => ['field' => $condition]];
        }

        return [$must, $where];
    }


    /**
     * 分析子查询表达式
     * @access public
     * @param mixed $field 查询字段
     * @param mixed $op 查询表达式
     * @param mixed $condition 查询条件
     * @return $this
     */
    public function parseWhereExists() {
        $ret = true;
        if(!empty($this->where_exists)) {
            foreach($this->where_exists as $i) {
                $op = $i[0];
                $fun = $i[1];
                $query = $this->newQuery();
                call_user_func_array($fun, [ & $query]);
                $_ret = $query->exists();
                if($op == '&') {
                    $ret = $ret && $_ret;
                } elseif($op == '|') {
                    $ret = $ret || $_ret;
                } elseif($op == '!&') {
                    $ret = $ret && !$_ret;
                } elseif($op == '!|') {
                    $ret = $ret || !$_ret;
                }
            }
            $this->where_exists = [];
        }
        return $ret;
    }


    /**
     * 获得ES客户端
     * @access public
     * @return $this
     */
    public function getClient() {
        return $this->client;
    }


    /**
     * 获得ES连接
     * @access public
     * @return $this
     */
    public function getConnection() {
        return $this->connection;
    }


    /**
     * 指定当前模型
     * @access public
     * @param string $model  模型类名称
     * @return $this
     */
    public function model($model) {
        $this->model = $model;
        return $this;
    }

    /**
     * ORM条件
     * @access public
     * @return $this
     */
    public function ormWhere($where) {
        $query = $this->newQuery($this->options);
        call_user_func_array($where, [ & $query]);
        $_ret = $query->get([], true);

        if(empty($_ret['hits']['hits'])) {
            return null;
        }

        $ids = [];
        foreach($_ret['hits']['hits'] as $r) {
            $ids[] = $r['_id'];
        }

        return $this->id($ids);
    }


    /**
     * ORM结果解析
     * @access public
     * @return $this
     */
    public function ormRet($ret, $type, $dsl) {

        $model = $this->model;
        $this->model = null;

        switch ($type) {
            // first方法
            case 'first':
                if(isset($ret['_source'])) {
                    return $this->createOrm($model, $ret['_id'], 1, $ret['_source']);
                }
                if(empty($ret['hits']['hits'])) return null;
                $ret = $ret['hits']['hits'][0];
                return $this->createOrm($model, $ret['_id'], $ret['_score'], $ret['_source']);
            // exists方法
            case 'exists':
                if(is_bool($ret)) return $ret;
                return $ret['exists'];
            // count方法
            case 'count':
                return $ret['count'];
            // get方法
            case 'get':
                if(isset($ret['_source'])) {
                    return $this->createOrm($model, $ret['_id'], 1, $ret['_source']);
                }
                if(empty($ret['hits']['hits'])) return null;
                $orms = [];
                foreach ($ret['hits']['hits'] as $hit) {
                    $orm = $this->createOrm($model, $hit['_id'], $hit['_score'], $hit['_source']);
                    $orms[] = $orm;
                }
                return $orms;
            // update方法
            case 'update':
                if(isset($ret['error']) 
                    || $ret['_shards']['successful'] == 0 
                    || $ret['_shards']['failed'] == 1) {
                    return false;
                } else {
                    return true;
                }
            // delete方法
            case 'delete':
                return $ret['found'] ? true : false;
            // insert方法
            case 'insert':
                if(isset($ret['error'])) return false;
                return $ret['_id'];
            // 
            case 'scroll':
                return $ret['_scroll_id'];
            case 'scrollGet':
                if(empty($ret['hits']['hits'])) return null;
                $orms = [];
                foreach ($ret['hits']['hits'] as $hit) {
                    $orm = $this->createOrm($model, $hit['_id'], $hit['_score'], $hit['_source']);
                    $orms[] = $orm;
                }
                return $orms;
            case 'aggs':
                return $ret['aggregations'];
            // bulk方法
            case 'bulk':
            default:
                return $ret;
        }
        
    }

    /**
     * 创建ORM对象
     * @access public
     * @return $this
     */
    protected function createOrm($model_name, $id, $score, $data) {
        $orm = new $model_name();
        $orm['id'] = $id;
        $orm['score'] = $score;
        $orm->data($data, true);
        $orm->isUpdate(true);
        $orm->clearChange();
        return $orm;
    }


    public function aggs($aggs = [], $force_raw = false) {

        list($type, $options) = $this->parseWhere([]);

        if(!$this->parseWhereExists()) return false;

        $ret = [];
        $dsl = '';

        if($type == Query::GET_ID || $type == Query::GET_IDS || $type == Query::GET_ALL) { // 直接ID查询
            $dsl = $this->builder()->selectAll($options);  
        } elseif($type == Query::GET_WHERE) { // 条件查询
            $dsl = $this->builder()->selectWhere($options);
        }

        $dsl['size'] = 0;

        $aggs = array_merge($aggs, $this->aggs);

        $dsl['body']['aggs'] = $aggs['aggs'];

        $this->clearAggs();

        $ret = $this->client->search($dsl);

        if($force_raw === true) return $ret;
        if(is_string($force_raw)) {
            return $this->parseResult($ret, $force_raw, $dsl);
        }
        return $this->parseResult($ret, __FUNCTION__, $dsl);
    }


    public function clearAggs() {
        $this->aggs = ['aggs' => []];
        $this->aggs_key = null;
    }


    protected function aggsFindKey($key, $aggs, $pre = []) {

        if(!isset($aggs['aggs'])) {
            return null;
        }

        if(isset($aggs['aggs'][$key])) {
            $pre[] = $key;
            return $pre;
        }
        foreach($aggs['aggs'] as $k => $v) {
            $x = $pre;
            $x[] = $k;
            $ret = $this->aggsFindKey($key, $aggs['aggs'][$k], $x);
            if(!is_null($ret)) {
                return $ret;
            }
        }
        return null;
    }

    protected function aggsSet($value, $name = null) {

        $keys = $this->aggs_key;
        if(!empty($name)) {
            $keys[] = $name;
        }

        $c = & $this->aggs;
        foreach ($keys as $key) {
            if(!isset($c['aggs'])) $c['aggs'] = [];
            if(!isset($c['aggs'][$key])) $c['aggs'][$key] = [];
            $c = & $c['aggs'][$key];
        }
        $c = $value;
    }


    public function newAggs($name, $parent = null) {
        if(empty($parent)) {
            $this->aggs['aggs'][$name] = [];
            $this->aggs_key = [$name];
            return $this;
        }
        $p = $this->aggsFindKey($parent, $this->aggs);
        $p[] = $name;
        $this->aggs_key = $p;
        return $this;
    }


    public function groupBy($field, $args = []) {

        // order
        // size

        return $this->metric('terms', $field, $args);
    }


    // protected function metric($name, $metric, $field) {
    //     if(empty($name)) {
    //         $name = $this->aggs_key[count($this->aggs_key) - 1].'_'.$metric;
    //     }
    //     $dsl = [$metric => ['field' => $field]];
    //     $this->aggsSet($dsl, $name);
    //     return $this;
    // }


    protected function metric($metric, $field, $args) {
        if(is_null($this->aggs_key)) return null;
        $dsl = [$metric => ['field' => $field]];
        foreach ($args as $key => $value) {
            $dsl[$metric][$key] = $value;
        }
        $this->aggsSet($dsl);
        return $this;
    }


    public function max($field, $args = []) {
        return $this->metric('max', $field, $args);
    }

    public function min($field, $args= []) {
        return $this->metric('min', $field, $args);
    }

    public function sum($field, $args= []) {
        return $this->metric('sum', $field, $args);
    }

    public function avg($field, $args = []) {
        return $this->metric('avg', $field, $args);
    }

    public function distinct($field, $args= []) {
        return $this->metric('cardinality', $field, $args);
    }


    public function histogram($field, $interval, $args = []) {
        $args = array_merge(['interval' => $interval], $args);
        return $this->metric('histogram', $field, $args);
    }


    public function stats($field, $args= []) {
        return $this->metric('extended_stats', $field, $args);
    }

    public function dateHistogram($field, $interval, $args = []) {
        // "min_doc_count" : 0,
        // "extended_bounds" : {
        //      "min" : "2014-01-01",
        //      "max" : "2014-12-31"
        // }

        $args = array_merge(['interval' => $interval, 'format' => 'yyyy-MM-dd HH:mm:ss'], $args);
        return $this->metric('date_histogram', $field, $args);
    }

    public function dateHistogramAll($field, $interval, $from, $to, $args = []) {
        $args = array_merge([
            'interval' => $interval, 
            'format' => 'yyyy-MM-dd HH:mm:ss',
            'min_doc_count' => 0,
            'extended_bounds' => ['min' => Date::t($from)->format(), 'max' => Date::t($to)->format()]
            ], $args);
        return $this->metric('date_histogram', $field, $args);
    }


    public function searchType($search_type) {
        $this->options['search_type'] = $search_type;
        return $this;
    }


    public function scroll($scroll = '30s', $size = null, $force_raw = false) {

        list($type, $options) = $this->parseWhere([]);

        if(!$this->parseWhereExists()) return false;

        $ret = [];
        $dsl = '';

        if($type == Query::GET_ID || $type == Query::GET_IDS) { // 直接ID查询
            return null;
        } elseif($type == Query::GET_ALL) { // 查询全部
            $dsl = $this->builder()->selectAll($options);
        } elseif($type == Query::GET_WHERE) { // 条件查询
            $dsl = $this->builder()->selectWhere($options);
        }


        unset($dsl['from']);
        $dsl['scroll'] = $scroll;
        if(!empty($size)) $dsl['size'] = $size;
        if(empty($dsl['size'])) $dsl['size'] = 100;

        $ret = $this->client->search($dsl);

        if($force_raw === true) return $ret;
        if(is_string($force_raw)) {
            return $this->parseResult($ret, $force_raw, $dsl);
        }
        return $this->parseResult($ret, __FUNCTION__, $dsl);
    }


    public function scrollGet($scroll_id, $scroll = '30s', $force_raw = false) {

        $dsl['scroll_id'] = $scroll_id;
        $dsl['scroll'] = $scroll;

        // 添加默认客户端参数
        if(!isset($options['client']) && !empty($this->params_clinet_default)) {
            $dsl['client'] = $this->params_clinet_default;
        }

        $ret = $this->client->scroll($dsl);

        if(is_string($force_raw)) {
            return $this->parseResult($ret, $force_raw, $dsl);
        }
        return $this->parseResult($ret, __FUNCTION__, $dsl);
    }


    public function scrollClear($scroll_id, $scroll = '30s') {
        $dsl['scroll_id'] = $scroll_id;
        // $dsl['scroll'] = $scroll;

        // 添加默认客户端参数
        if(!isset($options['client']) && !empty($this->params_clinet_default)) {
            $dsl['client'] = $this->params_clinet_default;
        }

        return $this->client->clearScroll($dsl);
    }


}