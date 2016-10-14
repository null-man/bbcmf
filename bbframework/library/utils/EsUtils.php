<?php

// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NullYang <635384073@qq.com>
// +----------------------------------------------------------------------

namespace util;


class EsUtils {

	// +----------------------------------------------------------------
	// | index 	=> 数据库
	// | type  	=> 数据表
	// +----------------------------------------------------------------
	function __construct(){
		include(BB_PATH . "library/third/Elasticsearch/autoload.php");
		$params = [
			'hosts' => ['127.0.0.1']
		];
		$this->client = new \Elasticsearch\Client($params);
	}

	// +----------------------------------------------------------------------
	// | 索引- [数据库]
	// +----------------------------------------------------------------------

	/**
	 * 创建 索引
	 *
	 * @param $index_name 数据库名
	 * @param array $settings 默认设置
	 * @return array
	 */
	public function create_index($index_name, $settings = array('number_of_shards' => 2, 'number_of_replicas' => 1)){
		$params = [
			'index' => $index_name,
			'body' 	=> [
				'settings' => $settings
			]
		];

		return $this->client->indices()->create($params);
	}



	// +----------------------------------------------------------------------
	// | 搜索
	// +----------------------------------------------------------------------

	/**
	 * 搜索全部数据
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @return array
	 */
	public function query_all($index_name, $type_name, $from = 0, $size = 100){
		$params = [
			'index' => $index_name,
			'type' 	=> $type_name,
			'from' 	=> $from,
			'size'  => $size,
		];

		return $this->client->search($params);
	}



	/**
	 * 根据id 搜索
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $id
	 * @return array
	 */
	public function query_by_id($index_name, $type_name, $id){
		$params = [
				'index' => $index_name,
				'type' 	=> $type_name,
				'id' 	=> $id
		];

		return $this->client->get($params);
	}


	public function query_routing($index_name, $type_name, $rout, $id){
		$params = [
			'index' => $index_name,
			'type' 	=> $type_name,
			'routing' 	=> $rout,
			'id' => $id
		];

		return $this->client->get($params);
	}

	public function query_routing_doc($index_name, $type_name, $body_array, $rout){
		$params = [
			'index' => $index_name,
			'type' 	=> $type_name,
			'routing' 	=> $rout,
			'body' 	=> [
				'query' => [
					'match' => $body_array
				]
			]
		];
		return $this->client->search($params);
	}




	/**
	 * 根据字段 搜索
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $body_array 匹配的字段
	 * 		  例如:
	 * 		      array('name'=>'null', ...=>...)
	 * @param $sort 排序
	 * 		  例如:
	 * 			 array(
	 * 			 	array('time' => array('order' => 'desc')),
	 *			 	array('popularity' => array('order' => 'desc'))
	 *			)
	 *
	 * @return array
	 */
	public function query_match($index_name, $type_name, $body_array, $sort)
	{
		$params = [
			'index' => $index_name,
			'type' 	=> $type_name,
			'body' 	=> [
				'query' => [
					'match' => $body_array
				],
//				'sort' => $sort
			]
		];

		return $this->client->search($params);
	}




	/**
	 * 布尔 搜索
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $type bool搜索类型
	 * 				must 多个查询条件的完全匹配,相当于 and
	 * 				must_not 多个查询条件的相反匹配，相当于 not
	 * 				should 至少有一个查询条件匹配, 相当于 or
	 *
	 * @param $body_array 参数
	 * 		  例如:
	 * 			$params = [
	 *            'match' => [
	 *                'name'=>'null-update'
	 *            ],
	 *            'match' => [
	 *                'class'=>1
	 *            ]
	 *        ];
	 *
	 * @return array
	 */
	public function query_bool($index_name, $type_name, $type, $body_array){
		$params = [
				'index' => $index_name,
				'type' 	=> $type_name,
				'body' 	=> [
					'query' => [
						'bool' => [
							$type =>$body_array
						]
					]
				]
		];

		return $this->client->search($params);
	}




	/**
	 * 过滤 搜索
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $body_array 参数
	 * 		  例如: array('name'=>'update')
	 *
	 * @return array
	 */
	public function query_term($index_name, $type_name, $body_array){
		$params = [
				'index' => $index_name,
				'type' 	=> $type_name,
				'body' 	=> [
					'query' => [
						'term' => $body_array
					]
				]
		];

		return $this->client->search($params);
	}




	/**
	 * 查询与过滤
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $match_array 查询参数
	 * 		  例如:array('name'=>'null-update')
	 *
	 * @param $term_array 过滤参数
	 * 		  例如:array('class'=>1)
	 *
	 * @return array
	 */
	public function query_filtered($index_name, $type_name, $match_array, $term_array){
		$params = [
				'index' => $index_name,
				'type' 	=> $type_name,
				'body' 	=> [
					'query' => [
						'filtered' => [
							'filter' => [
								'term' => $term_array
							],
							'query' => [
								'match' => $match_array
							]
						]
					]
				]
		];

		return $this->client->search($params);
	}




	// +----------------------------------------------------------------------
	// | 插入
	// +----------------------------------------------------------------------

	/**
	 * 插入数据 省略id值
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $body_array 参数
	 * 		  例如: array('name'=>'null-del')
	 *
	 * @return array
	 */
	public function insert($index_name, $type_name, $body_array){
		$params = [
			'index' => $index_name,
			'type' 	=> $type_name,
			'body' 	=> $body_array
		];

		return $this->client->index($params);
	}




	/**
	 * 插入数据 指定id值
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $body_array 参数
	 * 		  例如: array('name'=>'null-del')
	 *
	 * @param $id
	 * @return array
	 */
	public function insert_with_id($index_name, $type_name, $body_array, $id){
		$params = [
			'index' => $index_name,
			'type' 	=> $type_name,
			'id' 	=> $id,
			'body' 	=> $body_array
		];

		return $this->client->index($params);
	}




	/**
	 * 批量添加数据
	 *
	 * @param $index_name 数据库
	 * @param $type_name 表名
	 * @param $body_array 插入的数据
	 * 		  例如: array('name'=>'test_bulk')
	 *
	 * @param $bulk_count 批量 总量
	 * 		  例如: 1000
	 *
	 * @param $bulk_once 批量 单次
	 * 		  例如:100
	 */
	public function insert_bulk($index_name, $type_name, $body_array, $bulk_count, $bulk_once){
		$params = ['body' => []];
		$self = $this;

		for ($i = 1; $i <= $bulk_count; $i++) {
			$params['body'][] = [
				'index' => [
					'_index' => $index_name,
					'_type' => $type_name,
					'_id' => $i
				]
			];

			$params['body'][] = $body_array;

			// 每 $bulk_once 个文件停止并发送大量请求
			if($i % $bulk_once == 0){
				$responses = $self->client->bulk($params);

				// 删除旧的请求
				$params = ['body' => []];

				// 取消大部分的反应当你为了节省内存
				unset($responses);
			}
		}

		// 发送最后一批，如果它存在
		if (!empty($params['body'])) {
			$this->client->bulk($params);
		}
	}




	/**
	 * 添加路由
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $body_array 参数
	 * 		  例如: array('name'=>'test_bulk')
	 *
	 * @param $routing 路由
	 * 		  例如: 'company_xyz'
	 *
	 * @param $id
	 * @return array
	 */
	public function insert_routing($index_name, $type_name, $body_array, $routing, $id){
		$params = [
			'index' => $index_name,
			'type' 	=> $type_name,
			'id' 	=> $id,
			'routing' => $routing,
			'body' 	=> $body_array
		];

		return $this->client->index($params);
	}




	// +----------------------------------------------------------------------
	// | 更新
	// +----------------------------------------------------------------------

	/**
	 * 部分更新 根据id
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $body_array 参数
	 * 		  例如: array('name'=>'test_bulk')
	 *
	 * @param $id
	 * @return array
	 */
	public function update($index_name, $type_name, $body_array, $id){
		$params = [
			'index' => $index_name,
			'type' => $type_name,
			'id' => $id,
			'body' => [
				'doc' => $body_array
			]
		];

		return $this->client->update($params);
	}




	/**
	 * 更新路由
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $routing 路由
	 * 		  例如: 'company_xyz'
	 *
	 * @param $id
	 * @return array
	 */
	public function update_routing($index_name, $type_name, $body_array, $routing, $id){
		$params = [
			'index' => $index_name,
			'type' => $type_name,
			'id' => $id,
			'body' => [
				'doc' => $body_array
			],
			'routing' => $routing,
		];

		return $this->client->update($params);
	}




	/**
	 * 更新脚本
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $script 脚本
	 * 		  例如: 'ctx._source.counter += count'
	 *
	 * @param $params 参数
	 * 		  例如: array('count'=>4)
	 *
	 * @param $id
	 * @return array
	 */
	public function update_script($index_name, $type_name, $script, $params, $id){
		$params = [
				'index' => $index_name,
				'type' => $type_name,
				'id' => $id,
				'body' => [
					'script' => $script,
					'params' => $params
				]
		];

		return $this->client->update($params);
	}




	/**
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $script 脚本
	 * 		  例如: 'ctx._source.counter += count'
	 *
	 * @param $params 参数
	 * 		  例如: array('count'=>4)
	 *
	 * @param $upsert 更新或者插入
	 * 		  例如: array('counter'=>1)
	 * @param $id
	 * @return array
	 */
	public function update_insert($index_name, $type_name, $script, $params, $upsert, $id){
		$params = [
			'index' => $index_name,
			'type' => $type_name,
			'id' => $id,
			'body' => [
				'script' => $script,
				'params' => $params,
				'upsert' => $upsert
			]
		];

		return $this->client->update($params);
	}


	// +----------------------------------------------------------------------
	// | 删除
	// +----------------------------------------------------------------------

	/**
	 * 删除 根据id
	 *
	 * @param $index_name 数据库名
	 * @param $type_name 表名
	 * @param $id
	 * @return array
	 */
	public function delete($index_name, $type_name, $id){
		$params = [
			'index' => $index_name,
			'type' => $type_name,
			'id' => $id,
		];

		return $this->client->delete($params);
	}
}
