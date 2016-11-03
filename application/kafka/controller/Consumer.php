<?php
namespace app\kafka\controller;

use bb\DB;
use bb\Controller;
use bb\Kafka;

class Consumer extends Controller {


	public function index() {

		dump(Kafka::get());
		
		// Kafka::getAll(function($data) {
		// 	DB::table('kafka')->insert(['data' => $data]);
		// });

		
		// $conf = new \RdKafka\Conf();

		// $conf->set('group.id', 'testgroup');

		// $rk = new \RdKafka\Consumer($conf);
		// // $rk->setLogLevel(LOG_DEBUG);
		// $rk->addBrokers("10.1.14.15");

		// $queue = $rk->newQueue();

		// $topicConf = new \RdKafka\TopicConf();
		// $topicConf->set('auto.commit.interval.ms', 1000);
		// $topicConf->set("offset.store.sync.interval.ms", 6000);
		// $topic = $rk->newTopic("my-test7", $topicConf);

		// // RD_KAFKA_OFFSET_BEGINNING RD_KAFKA_OFFSET_STORED RD_KAFKA_OFFSET_STORED
		// $topic->consumeQueueStart(0, RD_KAFKA_OFFSET_STORED, $queue);
		// // while (true) {
		// // 	DB::table('kafka')->insert(['data' => 'consuming']);
		//     $msg = $queue->consume(1000);
		// //     if(is_null($msg)) {
		// //     	DB::table('kafka')->insert(['data' => 'data null']);
		// //     	break;
		// //     }
		//     if ($msg->err) {
		//         DB::table('kafka')->insert(['data' => $msg->errstr()]);
		//         $topic->consumeStop(0);
		//         // break;
		//     } else {
		//     	DB::table('kafka')->insert(['data' => strval($msg->payload)]);
		//     }
		// //     DB::table('kafka')->insert(['data' => 'consumed']);
		// // }
		// $topic->consumeStop(0);

		// echo 'consumer end';
		// exit();


	}

}