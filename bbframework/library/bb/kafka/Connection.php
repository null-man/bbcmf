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


namespace bb\kafka;

BT('bb/Pack');

use bb\Parse;

class Connection {

	use \bt\bb\Pack;

	protected $config = [
		'hosts' => []
	];

	protected $producer = null;
	protected $consumer = null;

	public function __construct($config) {
		if (!extension_loaded('rdkafka')) {
            throw new \think\Exception('_NOT_SUPPERT_:rdkafka');
        }
		if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
	}

	protected function createProducer() {
		if($this->producer == null) {
			$this->producer = new \RdKafka\Producer();
			$this->producer->addBrokers(implode(',', $this->config['hosts']));
		}
		return $this->producer;
	}

	protected function createConsumer() {
		if($this->consumer == null) {
			$conf = new \RdKafka\Conf();
			$conf->set('group.id', $this->config['groupid']);
			$this->consumer = new \RdKafka\Consumer($conf);
			$this->consumer->addBrokers(implode(',', $this->config['hosts']));
		}
		return $this->consumer;
	}


	public function send($data, $topic = '') {
		$producer = $this->createProducer();
		$topic = empty($topic) ? $this->config['default_topic'] : $topic;
		$producerTopic = $producer->newTopic($topic);
		$producerTopic->produce(RD_KAFKA_PARTITION_UA, 0, $this->pack($data));
	}


	public function sendAll($data, $topic = '') {
		$producer = $this->createProducer();
		$topic = empty($topic) ? $this->config['default_topic'] : $topic;
		$producerTopic = $producer->newTopic($topic);
		foreach($data as $d) {
			$producerTopic->produce(RD_KAFKA_PARTITION_UA, 0, $this->pack($d));
		}
	}

	public function get($partition = null, $topic = '') {

		if(is_null($partition)) {
			if($this->config['partitions'] == 1) {
				$partition = 0;
			} else {
				$partition = rand(0, C('kafka.partitions')-1);
			}
		}

		// if(is_null($partition)) {

		// 	$consumer = $this->createConsumer();
		// 	$queue = $consumer->newQueue();
		// 	$topicConf = new \RdKafka\TopicConf();
		// 	$topicConf->set('auto.commit.interval.ms', $this->config['auto.commit.interval.ms']);
		// 	$topicConf->set('offset.store.sync.interval.ms', $this->config['offset.store.sync.interval.ms']);
		// 	$topic = empty($topic) ? $this->config['default_topic'] : $topic;
		// 	$consumerTopic = $consumer->newTopic($topic, $topicConf);
		// 	for($i = 0; $i < $this->config['partitions']; $i ++) {
		// 		$consumerTopic->consumeQueueStart($i, RD_KAFKA_OFFSET_STORED, $queue);
		// 	}

		// 	for($i = 0; $i < $this->config['partitions']; $i ++) {
		// 		$msg = $queue->consume(1000);
		// 		$ret = false;
		// 	    if(is_null($msg) || $msg->err) {
		// 	    } else {
		// 	    	$ret = $this->unpack(strval($msg->payload));
		// 	    	usleep(10);
		// 	    	break;
		// 	    }
		// 	}

		//     for($i = 0; $i < $this->config['partitions']; $i ++) {
		// 		$consumerTopic->consumeStop($i);
		// 	}
		    
		//     return $ret;	

		// }

		return $this->_get($partition, $topic);

	}


	protected function _get($partition, $topic) {
		$consumer = $this->createConsumer();
		// return 'ddd';
		// $queue = $consumer->newQueue();
		$topicConf = new \RdKafka\TopicConf();
		$topicConf->set('auto.commit.interval.ms', $this->config['auto.commit.interval.ms']);
		$topicConf->set('offset.store.sync.interval.ms', $this->config['offset.store.sync.interval.ms']);
		$topic = empty($topic) ? $this->config['default_topic'] : $topic;
		$consumerTopic = $consumer->newTopic($topic, $topicConf);
		// $consumerTopic->consumeQueueStart($partion, RD_KAFKA_OFFSET_STORED, $queue);
		$consumerTopic->consumeStart($partition, RD_KAFKA_OFFSET_STORED);
		// $msg = $queue->consume(1000);
		$msg = $consumerTopic->consume($partition, 30*1000);
		$ret = false;
	    if(is_null($msg) || $msg->err) {
	    } else {
	    	$ret = $this->unpack(strval($msg->payload));
	    }
	    $consumerTopic->consumeStop($partition);
	    return $ret;
	}



	public function getAll($callback = null, $partition = null, $topic = '') {

		if(is_null($partition) && $this->config['partitions'] == 1) {
			$partition = 0;
		}

		// return $partition;
		// $partition = 0;

		if(is_null($partition)) {
			$consumer = $this->createConsumer();
			$queue = $consumer->newQueue();
			$topicConf = new \RdKafka\TopicConf();
			$topicConf->set('auto.commit.interval.ms', $this->config['auto.commit.interval.ms']);
			$topicConf->set('offset.store.sync.interval.ms', $this->config['offset.store.sync.interval.ms']);
			$topic = empty($topic) ? $this->config['default_topic'] : $topic;
			$consumerTopic = $consumer->newTopic($topic, $topicConf);
			for($i = 0; $i < $this->config['partitions']; $i ++) {
				$consumerTopic->consumeQueueStart($i, RD_KAFKA_OFFSET_STORED, $queue);
			}

			$ret = [];
			while (true) {
			    $msg = $queue->consume(1000);
			    if (is_null($msg) || $msg->err) {
			        // $consumerTopic->consumeStop($partion);
			        break;
			    } else {
			    	$m = $this->unpack(strval($msg->payload));
			    	$ret[] = is_null($callback) ? $m : $callback($m);
			    }
			}
			return $ret;

		}

		return $this->_getAll($callback, $partition, $topic);
	}


	protected function _getAll($callback, $partition, $topic) {
		$consumer = $this->createConsumer();
		// $queue = $consumer->newQueue();
		$topicConf = new \RdKafka\TopicConf();
		$topicConf->set('auto.commit.interval.ms', $this->config['auto.commit.interval.ms']);
		$topicConf->set('offset.store.sync.interval.ms', $this->config['offset.store.sync.interval.ms']);
		$topic = empty($topic) ? $this->config['default_topic'] : $topic;
		$consumerTopic = $consumer->newTopic($topic, $topicConf);
		// $consumerTopic->consumeQueueStart($partion, RD_KAFKA_OFFSET_STORED, $queue);
		$consumerTopic->consumeStart($partition, RD_KAFKA_OFFSET_STORED);
		$ret = [];
		while (true) {
		    // $msg = $queue->consume(1000);
		    $msg = $consumerTopic->consume($partition, 30*1000);
		    if (is_null($msg) || $msg->err) {
		        // $consumerTopic->consumeStop($partion);
		        break;
		    } else {
		    	$m = $this->unpack(strval($msg->payload));
		    	$ret[] = is_null($callback) ? $m : $callback($m);
		    }
		}
		return $ret;
	}


}