<?php
namespace app\db\controller;

use bb\Controller;
use bb\DB;

use app\db\model\Students;
use app\db\model\Phone;
use app\db\model\Users;
use app\db\model\Classes;
use app\db\model\Interests;

use app\db\model\Staff;
use app\db\model\Sphoto;
use app\db\model\Sorder;


use app\db\model\Zvideo;
use app\db\model\Ztag;
use app\db\model\Zpost;
use app\db\model\Ztaggable;


class Model extends Controller
{

	public function index()
    {
        return '模型操作';
    }

    public function basic() {

    	$users = Users::all();
    	dump($users);

    	$user = Users::find(1);
    	dump($user->name);

    }

    // 一对一
    public function onetoone() {

    	dump(Students::find(1)->phone['phone']);

    	dump(Phone::find(1)->student->name);
    }

    // 一对多
    public function onetomany() {
    	dump(Classes::find(1)->students->count());
    	dump(Classes::find(1)->students()->where('name', 'like', '%in%')->first()->name);

    	dump(Students::find(1)->classes->name);

    }

    // 多对多
    public function manytomany() {

    	dump(Students::find(1)->interests->count());

    	dump(Interests::where('name', '羽毛球')->first()->students[0]->name);

    }

    // 远程一对多
    public function manythrough() {

    	dump(Classes::find(1)->phone->count());
    }

    // 多态关联
    public function morph() {
    	$staff = Staff::find(1);
    	foreach ($staff->photos as $photo) {
    		dump($photo->path);
		}
		dump(Sorder::find(1)->photos[0]->path);
    }

    // 多态多对多关联
    public function morphmany() {
    	foreach(Zpost::find(1)->tags as $tag) {
    		dump($tag->name);
    	}

    	foreach(Ztag::find(1)->posts as $post) {
    		dump($post->name);
    	}
    }

    // 关联查询
    public function relatedsearch() {

    	// has 
    	Ztag::has('posts', '>=', 1)->get();
    	dump(Zpost::has('tags', '>=', 2)->count());

    	dump(Interests::whereHas('students', function($q)
		{
		    $q->where('name', 'like', 'l%');

		})->get()[0]->name);

    	dump(Phone::find(1)->student->name);


    }


    // 预加载
    public function preload() {
  		foreach (Students::all() as $st) {
  		  dump($st->phone['phone']);
		  }
		foreach(Students::with('phone')->get() as $st) {
			dump($st->phone['phone']);
		}

		foreach(Students::with('phone', 'classes')->get() as $st) {
			dump($st->phone['phone']);
			dump($st->classes['name']);
		}

		// foreach(Phone::all() as $p) {
		// 	dump($p->student->classes->name);
		// }
		// foreach(Phone::with('student.classes')->get() as $p) {
		// 	dump($p->student->classes->name);
		// }

    	$users = Students::with(array('phone' => function($query)
				{
				    $query->orderBy('phone', 'desc');

				}))->get();
    	foreach($users as $u) {
    		dump($u->phone['phone']);
    	}

    }


    // 添加关联
    public function addrelated() {

    	// 一对多
    	// $a = new Classes(['name' => '二班']);
    	// $a->save();
		$student = new Students(array('name' => 'mimi'));
		$cls = Classes::where('name', '二班')->first();
		$student = $cls->students()->save($student);

    	// 添加多个
  //   	$a = new Classes(['name' => '三班']);
  //   	$a->save();
		// $students = array(
  //   		new Students(array('name' => 'linda')),
  //   		new Students(array('name' => 'sea')),
  //   		new Students(array('name' => 'nick'))
		// );
		// Classes::where('name', '二班')->first()->students()->saveMany($students);


    	// 从属关联模型 ( Belongs To ) 
  //   	$cls = Classes::where('name', '四班')->first();
  //   	$student = Students::where('name', 'linda')->first();
		// $student->classes()->associate($cls);
		// $student->save();

		// $cls = new Classes(['name' => '四班']);
		// $cls->save();
  //   $student = Students::where('name', 'sea')->first();
		// $student->classes()->associate($cls);
		// $student->save();

    	// 新增多对多关联模型 ( Many To Many )
    	$student = Students::find(1);
    	$interest = Interests::where('name', '桌球')->first();
    	$a = $student->whereHas('interests', function($q)
		{
		    $q->where('name', '桌球');
		});
		dump($a->count());
    	if(empty($a->count())) {
    		$student->interests()->attach($interest, []);// 其他属性
    	}
    	dump($a->count());
    	if($a->count()) {
    		$student->interests()->detach($interest, []);// 其他属性
    	}
    }

    // 使用枢纽表 多对多
    public function pivot() {
		$student = Students::find(1);

		foreach ($student->interests as $interest)
		{
		    dump($interest->pivot->id);
		}

		// 更新枢纽表的数据
		// updateExistingPivot
    }


    public function collection() {

    	// 确认 Collection 里是否包含特定键值
    	$interests = Students::find(1)->interests;
		if ($interests->contains(Interests::find(1))) {
		    dump('yes');
		} else {
			dump('no');
		}

		// 转换成数组或 JSON
		// dump($interests->toArray());
		// dump($interests->toJson());

		// Collections 遍历
		// $interests = Students::find(1)->interests->each(function($interest)
		// {
		//     $interest->
		// });
		// dump($interests->count());

		// Collection 过滤
		// $students = Students::all()->filter(function($student)
		// {
		//     return $student->class_id > 3;
		// });
		// dump($students->count());

		// 遍历传入 Collection 里的每个对象到回调函数
		// $interests = Students::find(1)->interests;
		// $interests->each(function($interest)
		// {
		//     //
		// });

    	// 依照属性值排序
  //   	$interests = Students::find(1)->interests;
  //   	$interests = $interests->sortBy(function($interest)
		// {
		//     return $interest->created_at;
		// });
		// $interests = $interests->sortBy('created_at');

  //   	dump(Students::find(5)->updated_at < Students::find(4)->updated_at);

    }

    public function events() {

    	// $s = new Students(['name' => 'abcdefghidjflkasjflksjlkfjsdlkfskld']);
    	// $s = Students::find(4);
    	// $s->name = 'kiki10abcdefghidjflkasjflksjlkfjsdlkfskld';
    	// $s->save();

    }


}