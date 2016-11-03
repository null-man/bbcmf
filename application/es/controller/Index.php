<?php
namespace app\es\controller;

use bb\Controller;
use bb\ES;

class Index extends Controller
{

    public function index()
    {


    	// $params = [
     //        'index' => 'app_static',
     //        'type'  => 'android_info',
     //        'from'  => 0,
     //        'size'  => 2,
     //    ];

     //    dump(ES::search($params));

  //   	$params = [
		//     'index'  => 'test_missing',
		//     'type'   => 'test',
		//     'id'     => 1,
		//     'client' => [ 'ignore' => 404 ] 
		// ];
		// $params = [
		//     'index'  => 'test_missing',
		//     'type'   => 'test',
		//     'client' => [ 'ignore' => [400, 404] ] 
		// ];
		// $params = [
  //           'index' => 'app_static',
  //           'type'  => 'android_info',
  //           'id'  => 1000,
  //       ];
        // ES::index('app_static')->type('android_info')->limit(10)->client(['ignore' => 404])->get();
        // dump(ES::index('app_static')->type('android_info')->get(1000));

      // dump(ES::index('app_static')->type('android_info')->id('1000')->get());


      // dump(ES::index('megacorp')->type('employee')->id(2)->script('dfaf', ['ca' => 1])->update(['dd' => 10]));

      // dump();
      // ES::setRawOutput(true);
      // dump(ES::index('megacorp')->type('employee')->id(1)
      //   ->update(['ud' => ES::newQuery()->index('app_static')->type('android_info')->id(10)->get()['_source']['age']]));

      // $d = [
      //   ['a' => 1],
      //   ['a' => 2],
      //   ['a' => 3, '_id' => 10],
      // ];

      // $x = [
      //   ['a' => 4, '_index' => 'xxx', '_id' => 11],
      //   ['a' => 4, '_index' => 'xxx', '_id' => 12],
      //   ['a' => 4, '_index' => 'xxx']
      // ];
		  
      // dump(ES::index('megacorp')->type('employee')
      //   ->id(2, 3, 4)->data($d)->bulking('index')
      //   ->id(8)->data(['bb' => 1])->bulking('index')
      //   ->bulk());


        // ->where(['term' => ['appid' => 'com.sinyee.babybus.songV']])
        // ->where(['terms' => ['apps' => ['com.baidu.homework', 'com.sina.weibog3', 'com.sinyee.babybus.foodstreet']]])
        // ->where(['range' => ['age' => ['gte' => 2, 'lt' => 4]]])
        // ->where(['exists' => ['field' => 'mac']])
        // ->where([
        //     ['term' => ['appid' => 'com.sinyee.babybus.songV']],
        //     ['term' => ['appid' => 'com.sinyee.babybus.abc']],
        //   ])


      // dump(ES::index('app_static')->type('android_info')
      //   ->where('appid', '=', 'com.sinyee.babybus.songV')
      //   // ->where('appid', 'in', ['com.baidu.homework', 'com.sina.weibog3', 'com.sinyee.babybus.foodstreet'])
      //   // ->where('age', '>', 2)
      //   // ->where('age', '<', 4)
      //   // ->where('age', 'between', [2, 4])
      //   // ->where('mac', '!null')
      //   ->where('age', '=', 5)
      //   // ->orWhere('age', '=', 5)
      //   ->whereExists(function($query) {
      //     $query->index('app_static')->type('android_info')->where('appid', '=', 'com.sinyee.babybus.abc');
      //   })
      //   ->first());

      // dump(ES::index('app_static')->type('android_info')->id(1,2,2,3,4,'dfs')->exists());


      // ES::index('app_static')->type('android_info')->whereExists(function($query) {
      //   $query->where('age', '>', 2);
      //   // return false;
      // });




      // $a = ['a' => 1, 'b' => ['aa' => 1, 'bb' => 2], 'c' => ['b' => ['aa' => 2, 'cc' => 4], 'zz' => 1]];
      // $b = array_merge($a, $a['c']);
      // unset($b['c']);
      // dump($b);


      // ES::setRawOutput(true);

      // dump(ES::index('megacorp')->type('employee')->id(200)->delete());

        // dump(ES::index('megacorp')->type('employee')->insert(['ddd' => 1]));

//       $params = ['body' => []];


      // $ret = ES::newBulk()->index('megacorp')->type('employee')
      //   ->bulking('insert', ['dfds' => 1])
      //   ->bulking('insert', ['dfds' => 1])
      //   ->bulking('insert', ['dfds' => 1])
      //   ->bulk();
      // dump($ret);







      // $eodel = eodel('DmpRawData');

    // dump(DmpRawData::scheme()->get());

    // $a = new DmpRawData();
    // $a['a.b'] = 'b';
    // // $a['create_time'] = null;

    // dump($a);

    // dump(ES::index('abc')->type('data')->first());

    // DmpRawData::event('before_write', function($model) {
    //  // dump($model);
    // });

    // DmpRawData::get();

    // $a = new DmpRawData();


    // dump(ES::scheme()->index('raw')->exists());


    // DmpRawData::get();
    // $a['ip'] = '8.8.8.8';
    // dump($a->save());

    // DmpRawData::where

    // $a = new DmpRawData();
    // $a->id = 'AVTm1f6UvCkTazQF54f9';
    // $a->ip = '9.1.2';
    // dump($a->save());

    // $a = DmpRawData::get();
    // dump($a);


    // dump(DmpRawData::first()->ip);


    // dump(DmpRawData::scope('AA')->get());

    // $a['ip'] = '8.8.8.8';
    // // $a['ip'] = '1.1.1.1';
    // dump($a->save());


    // $orm = new DmpRawData();
    // $a = $orm->save(['ip' => '1.1.1.1'], function($query) {
    //  $query->where('ip', '=', '8.8.8.8');
    // });

    // dump($a);




    // $a = new DmpRawData();
    // $a['ab.a'] = 1;
    // dump($a['ab.a']);



    // dump($eodel->scheme()->get());

    // $eodel = eodel('DmpRawData');

    // dump($eodel->scheme()->get());

    // dump($eodel->get());
    // dump(ES::scheme()->index('raw')->type('data')->get());
    // return;

    // ES::scheme()->index('raw')->delete();

    // ES::scheme()->index('raw')->create();

    // ES::scheme()->index('raw')->type('data')->mapping([
    //  'properties' => [
    //    'ip' => ['type' => 'string', 'index' => 'not_analyzed']
    //  ]
    // ])->create();

    // ES::scheme()->index('raw')->type('data')->mapping([
    //  'properties' => [
    //    'time' => ['type' => 'long']
    //  ]
    // ])->update();

    

    // dump(ES::scheme()->index('raw')->type('data')->field('ip')->get());


    // dump(ES::scheme()->index('raw')->delete());


    // dump(ES::_indices()->deleteMapping(['index' => 'abc', 'type' => 'data']));


    // $a = ES::scheme()->index('raw')->type('data')->mapping([
    //  'properties' => [
    //    'basic' => [
    //      'type' => 'object',
    //      'properties' => [
    //        'ip' => [
    //          'type'  =>    'string',
    //          'index' =>    'not_analyzed'
    //          ],
    //          'dsn' => [
    //            'type'  =>    'string',
    //          'index' =>    'not_analyzed'
    //          ],
    //          'time' => [
    //            'type' =>    'long'
    //          ]
    //      ]
    //    ]
    //  ]
    // ]
    // )->putMapping();

    // dump($a);

    // dump(\think\Loader::parseClass($module, $layer, $name););

    // dump(ES::index('')->type('')->count());

    // if(I('test', 0) == 1) {
  //           $data = $this->testData();
  //       } else {
  //           $data = $this->recevieData();
  //       }

  //       dump($data);


  //       foreach($data['event']['handler'] as $system) {
  //        dump(System::create($system)->analyze($data));
  //       }








    // $t = new Date();
    // $a = ES::index('abc')->type('data')->insert(['time' => $t->format()]);
  //       dump($a);
        // dump(ES::index('abc')->type('data')->where('time', '<', $t->format())->where('time', '>', $t->addday(-10)->format())->get());
        // ES::index('abc')->

        // return;

    // dump(new Date()->format());


        return 'ES操作';
    }




}