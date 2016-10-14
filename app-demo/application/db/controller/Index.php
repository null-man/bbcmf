<?php
namespace app\db\controller;

use bb\Controller;
use bb\DB;

class Index extends Controller
{

    public function index()
    {
        return '数据库操作';
    }

    public function selects() {

        // 从数据库表中取得所有的数据列
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            dump($user);
        }

        // 从数据库表中取得单一数据列
        // $user = DB::table('users')->where('name', 'jack')->first();
        // dump($user);

        // 取得单一字段值的列表
        $roles = DB::table('roles')->lists('title');
        // 为回传的数组指定自定义键值
        $roles = DB::table('roles')->lists('title', 'name');
        // dump($roles);

        // 指定查询子句 (Select Clause)
        $users = DB::table('users')->select('name', 'email')->get();
        $users = DB::table('users')->distinct()->get();
        $users = DB::table('users')->select('name as user_name')->get();
        // dump($users);

        // 增加查询子句到现有的的查询中
        $query = DB::table('users')->select('name');
        $users = $query->addSelect('age')->get();
        // dump($users);

        // 使用 where 及运算符
        $users = DB::table('users')->where('votes', '>', 100)->get();
        // dump($users);

        // "or" 语法
        $users = DB::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'jack')
                    ->get();
        // dump($users);

        // 使用 Where Between
        $users = DB::table('users')
                    ->whereBetween('votes', array(1, 100))->get();
        // dump($users);

        // 使用 Where Not Between
        $users = DB::table('users')
                    ->whereNotBetween('votes', array(1, 100))->get();
        // dump($users);

        // 使用 Where Null 找有未设定的值的数据
        $users = DB::table('users')
                    ->whereNull('updated_at')->get();
        // dump($users);

        // 排序(Order By), 分群(Group By), 及 Having
        $users = DB::table('users')
                    ->select(DB::raw('name, sum(_count) as sm'))
                    ->orderBy('sm', 'desc')
                    ->groupBy('name')
                    ->having('sm', '>', 5)
                    ->get();
        // dump($users);

        // 偏移(Offset) 及 限制(Limit)
        $users = DB::table('users')->skip(1)->take(1)->get();
        // dump($users);

    }


    public function joins() {

        // 基本的 Join 语法
        dump(DB::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.id', 'contacts.phone', 'orders.price')
            ->get());

        // Left Join 语法
        dump(DB::table('users')
            ->leftJoin('contacts', 'users.id', '=', 'contacts.user_id')
            ->get());
        // 指定更进阶的 join 子句
        dump(DB::table('users')
            ->join('contacts', function($join)
            {
                $join->on('users.id', '=', 'contacts.user_id')->orOn(...);
            })
            ->get());
        dump(DB::table('users')
            ->join('contacts', function($join)
            {
                $join->on('users.id', '=', 'contacts.user_id')
                 ->where('contacts.user_id', '<', 2);
            })
            ->get());

    }

    public function wheres() {

        // 群组化参数
        dump(DB::table('users')
            ->where('name', '=', 'John')
            ->orWhere(function($query)
            {
                $query->where('votes', '>', 100)
                      ->where('email', '<>', 'Admin');
            })
            ->get());

        // Exists 语法
        dump(DB::table('users')
            ->whereExists(function($query)
            {
                $query->select(DB::raw(1))
                      ->from('orders')
                      ->whereRaw('tp_orders.user_id = tp_users.id');
            })
            ->get());

    }


    public function aggs() {

        // 使用聚合方法

        $users = DB::table('users')->count();

        $price = DB::table('orders')->max('price');

        $price = DB::table('orders')->min('price');

        $price = DB::table('orders')->avg('price');

        $total = DB::table('users')->sum('votes');

    }


    public function raw() {

        $users = DB::table('users')
                     ->select(DB::raw('count(*) as user_count, name'))
                     ->groupBy('name')
                     ->get();

        dump($users);
    }


    public function insert() {

        // 新增一条数据进数据库表
        DB::table('users')->insert(
            array('email' => 'john@example.com', 'votes' => 0)
        );

        // 新增自动递增 (Auto-Incrementing) ID 的数据至数据库表
        dump(DB::table('users')->insertGetId(
            array('email' => 'john@example.com', 'votes' => 0)
        ));

        // 新增多条数据进数据库表
        DB::table('users')->insert(array(
            array('email' => 'taylor@example.com', 'votes' => 0),
            array('email' => 'dayle@example.com', 'votes' => 0),
        ));

    }

    public function update() {
        
        // 更新数据库表中的数据
        DB::table('users')
            ->where('id', 1)
            ->update(array('votes' => 1));

        // 对字段递增或递减数值
        DB::table('users')->increment('votes');

        DB::table('users')->increment('votes', 5);

        DB::table('users')->decrement('votes');

        DB::table('users')->decrement('votes', 5);

        // 同时更新其他字段
        DB::table('users')->increment('votes', 1, array('name' => 'John'));
    }

    public function del() {
        
        // 删除数据库表中的数据

        // DB::table('users')->where('votes', '<', 100)->delete();

        // 删除数据库表中的所有数据

        // DB::table('users')->delete();

        // 清空数据库表

        // DB::table('users')->truncate();

    }

    public function unions() {

        // "合并 (union)"两个查询的结果
        $first = DB::table('users')->whereNull('name');

        $users = DB::table('users')->whereNull('updated_at')->union($first)->get();

        dump($users);
    }

    public function other() {

        // 悲观锁定
        // DB::table('users')->where('votes', '>', 100)->sharedLock()->get();
        // 锁住更新(lock for update)
        // DB::table('users')->where('votes', '>', 100)->lockForUpdate()->get();

        // 缓存查询结果

    }

}
