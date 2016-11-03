<?php
namespace app\kafka\controller;

use bb\DB;
use bb\Controller;
use bb\Kafka;

class Producer extends Controller {

	public function index() {

		Kafka::send('kafka test');

		$data = [];
		for ($i = 0; $i < 1000; $i++) {
    		$data[] = 'kafka all '.$i;
		}

		Kafka::sendAll($data);

	}

}