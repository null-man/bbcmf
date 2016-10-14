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

BT('bb/Singleton');

use bb\es\Query;

class Builder {
	
	use \bt\bb\Singleton;
	
	// es对象实例
    protected $query;

    /**
     * 设置当前的Query对象实例
     * @access protected
     * @param \bb\es\Query $query 当前查询对象实例
     * @return void
     */
    public function setQuery($query) {
        $this->query = $query;
    }


    /**
     * 设置选填信息
     * @access protected
     * @param \bb\es\Query $query 当前查询对象实例
     * @return void
     */
    protected function set_optional($keys, $dsl, $options) {
        foreach($keys as $key) {
            isset($options[$key]) && $dsl[$key] = $options[$key];
        }
        return $dsl;
    }


    /**
     * 生成查询DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function selectID($options = []) {
        $dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置id       - 必填
        $dsl['id'] = $options['id'];

        // 选填
        $dsl = $this->set_optional([
            'fields', 'parent', 'routing', 'client',
            'ignore_missing', 'preference', 'realtime', 'refresh',
            '_source', '_source_exclude', '_source_include'], 
            $dsl,
            $options); 

        return $dsl;
    }


    /**
     * 生成查询IDS DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function selectIDS($options = []) {
        $dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置ids       - 必填
        $dsl['body'] = [];
        $dsl['body']['ids'] = $options['ids'];

        // 选填
        $dsl = $this->set_optional([
            'fields', 'parent', 'routing', 'client',
            'preference', 'realtime', 'refresh',
            '_source', '_source_exclude', '_source_include'], 
            $dsl,
            $options); 

        return $dsl;
    }


    /**
     * 生成查询所有 DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function selectAll($options = []) {
        $dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置body       - 必填
        $dsl['body'] = ['query' => ['match_all' => []]];

        // 选填
        $dsl = $this->set_optional([
            'fields', 'parent', 'routing', 'size', 'from', 'sort', 'client',
            '_source', '_source_exclude', '_source_include', 'search_type'], 
            $dsl,
            $options); 

        return $dsl;
    }


    /**
     * 生成查询条件 DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function selectWhere($options = []) {
        $dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置body       - 必填
        $dsl['body'] = ['query' => ['filtered' => []]];
        if(isset($options['where'])) {
            $dsl['body']['query']['filtered']['filter']['bool'] = $options['where'];
        }
        if(isset($options['match'])) {
            $dsl['body']['query']['filtered']['query']['bool'] = $options['match'];
        }

        // 选填
        $dsl = $this->set_optional([
            'fields', 'parent', 'routing', 'size', 'from', 'sort', 'client',
            '_source', '_source_exclude', '_source_include', 'search_type'], 
            $dsl,
            $options); 

        return $dsl;
    }



    /**
     * 生成查询count DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function count($options = []) {
        $dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置body       - 必填
        $dsl['body'] = ['query' => ['filtered' => []]];
        if(isset($options['where'])) {
            $dsl['body']['query']['filtered']['filter']['bool'] = $options['where'];
        }
        if(isset($options['match'])) {
            $dsl['body']['query']['filtered']['query']['bool'] = $options['match'];
        }

        // 选填
        $dsl = $this->set_optional([
            'min_score', 'preference', 'routing', 'source', 'client',
            'ignore_unavailable', 'allow_no_indices', 'expand_wildcards'], 
            $dsl,
            $options);

        return $dsl;
    }


    /**
     * 生成查询exists DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function exists($options = []) {
        $dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置id       - 必填
        $dsl['id'] = strval($options['id']);

        // 选填
        $dsl = $this->set_optional([
            'parent', 'preference', 'routing', 'refresh', 'realtime', 'client'],
            $dsl,
            $options);

        return $dsl;
    }


    /**
     * 生成update DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function update($options) {
        $this->dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置id       - 必填
        $dsl['id'] = strval($options['id']);

        // 设置body       - 必填
        $dsl['body'] = [];
        // 赋值body
        isset($options['data']) && $dsl['body']['doc'] = $options['data'];
        if(isset($options['script'])) {
            $dsl['body']['script'] = $options['script']['script'];
            $dsl['body']['params'] = $options['script']['params'];
        } 
        isset($options['upsert']) && $dsl['body']['upsert'] = $options['upsert'];

        // 选填
        $dsl = $this->set_optional([
            'fields', 'routing', 'parent', 'retry_on_conflict', //'script',
            'version_type', 'lang',
            'timeout', 'timestamp', 'ttl',
            'consistency', 'percolate', 'refresh', 'replication'], 
            $dsl,
            $options); 

        return $dsl;
    }


    /**
     * 生成delete DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function delete($options) {
        $this->dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置id       - 必填
        $dsl['id'] = strval($options['id']);

        // 选填
        $dsl = $this->set_optional([
            'routing', 'parent',
            'version_type',
            'timeout',
            'consistency', 'refresh', 'replication'], 
            $dsl,
            $options);

        return $dsl;
    }


    /**
     * 生成insert DSL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function insert($options) {
        $dsl = [];

        // 设置index      - 必填
        $dsl['index'] = $options['index'];

        // 设置type       - 必填
        $dsl['type'] = $options['type'];

        // 设置body       - 必填
        $dsl['body'] = [];
        // 获得数据
        $data = $options['data'];
        // 存在数据时，赋值body
        !empty($data) && (isset($options['body']) ? 
            $dsl['body'] = array_merge($options['body'], $data) : 
            $dsl['body'] = $data);

        // 选填
        $dsl = $this->set_optional([
            'client', 'id', 'routing', 'parent',
            'version', 'version_type',
            'timeout', 'timestamp', 'ttl',
            'consistency', 'op_type', 'percolate', 'refresh', 'replication'], 
            $dsl,
            $options);

        return $dsl;
    }


    /**
     * 生成bulk DSL
     * @access public
     * @param array $options 表达式
     * @param array $extra_dsl 额外dsl
     * @return string
     */
    public function bulk($bulk_options, $extra_dsl) {
        $dsl = ['body' => []];

        // 直接返回dsl;
        if(empty($bulk_options)) return $extra_dsl;

        foreach($bulk_options as $bulk_option) {

            $bulk = $bulk_option[0];
            $options = $bulk_option[1];
            $operation = $bulk_option[2];

            // 解析操作
            $operation = $this->paserOperation($operation);

            $_dsl = [];

            // 单条
            if($bulk == Query::BULK_NO) {
                $id = isset($options['id']) ? $options['id'] : null;
                $_dsl = $this->bulkBody($operation, $_dsl, $options['data'], $options['index'], $options['type'], strval($id), $options);
            } elseif($bulk == Query::BULK_ID) {
                // 多ID
                $_dsl = $this->bulkID($options, $operation);
            } elseif($bulk == Query::BULK_DATA) {
                // 多Data
                $_dsl = $this->bulkData($options, $operation);
            }
            if(isset($_dsl['body'][0]['index']['_id']) && empty($_dsl['body'][0]['index']['_id'])) {
                unset($_dsl['body'][0]['index']['_id']);
            }

            $dsl['body'] = array_merge($dsl['body'], $_dsl['body']);

        }

        return $dsl;
    }

    /**
     * 生成ID批量bulk DSL
     * @access public
     * @param array $options 表达式
     * @param array $operation 操作
     * @return string
     */
    public function bulkID($options, $operation) {
        $dsl = [];

        // 解析操作
        $operation = $this->paserOperation($operation);

        if(isset($options['data'])) $options['data'] = [];
        // 生成bulk dsl
        foreach($options['id'] as $id) {
            $dsl = $this->bulkBody($operation, $dsl, $options['data'], $options['index'], $options['type'], strval($id), $options);
        }

        return $dsl;
    }


    /**
     * 生成DATA批量bulk DSL
     * @access public
     * @param array $options 表达式
     * @param array $operation 操作
     * @return string
     */
    public function bulkData($options, $operation) {
        $dsl = [];
        // 解析操作
        $operation = $this->paserOperation($operation);
        // 操作数据
        $data = $options['data'];

        // id是数组
        $oid = isset($options['id']) && is_array($options['id']);

        for($i = 0; $i < count($data); $i++) {

            $index = isset($data[$i]['_index']) ? $data[$i]['_index'] : $options['index'];
            $type = isset($data[$i]['_type']) ? $data[$i]['_type'] : $options['type'];
            $id = $oid && isset($data[$i]['_id']) ? $data[$i]['_id'] : null;
            unset($data[$i]['_index']);
            unset($data[$i]['_type']);
            unset($data[$i]['_id']);

            $dsl = $this->bulkBody($operation, $dsl, $data[$i], $index, $type, $id, $options);

        }

        return $dsl;
    }


    /**
     * 解析操作
     * @access protected
     * @param array $operation 操作
     * @return string
     */
    protected function paserOperation($operation) {
        $operation == 'upsert' && $operation = 'index';
        $operation == 'insert' && $operation = 'create';
        return $operation;
    }


    /**
     * bulk数据填写
     * @access protected
     * @param string $operation 操作
     * @param array $dsl dsl
     * @return string
     */
    protected function bulkBody($operation, $dsl, $data, $index, $type, $id, $options) {

        $o = [
            $operation => [
                '_index' => $index,
                '_type'  => $type,
            ]
        ];
        if(!is_null($id)) $o[$operation]['_id'] = $id;

        $dsl['body'][] = $o;

        if($operation == 'delete') return $dsl;
        if($operation == 'update') {

            $_ = ['doc' => $data];
            if(isset($options['script'])) {
                $_['script'] = $options['script']['script'];
                $_['params'] = $options['script']['params'];
            };
            isset($options['upsert']) && $_['upsert'] = $options['upsert'];
            $dsl['body'][] = $_;

            return $dsl;
        }
        $dsl['body'][] = $data;
        return $dsl;
    }



}