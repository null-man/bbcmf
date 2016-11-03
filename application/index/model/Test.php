<?php

namespace app\index\model;

use bb\Model;
// use think\Model;

class Test extends Model {

    protected $table = 'users';

    // 默认会加入created_at 和 updated_at 字段 所以要禁止掉
     public $timestamps = false;

    // 只允许赋值
    protected $fillable = ['name', ];
    // 不允许赋值
//    protected $guarded = array('id');

    // 定义范围查询
    public function scopeAge($query)
    {
        return $query->where('age', '>', 15);
    }

    // 添加数据
    public function add(){
        $this->create([
            'name' => 'test'
        ]);
    }





    // 定义 一对一
    // 第二个参数都是 foreign_key，第三个参数一般都是 local_key
    // 这段代码除了展示了一对一关系该如何使用之外，还传达了三点信息，也是我对于大家使用 Eloquent 时候的建议：
        // 每一个 Model 中都指定表名
        // has one account 这样的关系写成 hasOneAccount() 而不是简单的 account()
        // 每次使用模型间关系的时候都写全参数，不要省略
    public function hasOnePhone()
    {
        return $this->hasOne('app\index\model\Phone', 'user_id', 'id');
    }


    // 定义 一对多
    public function hasManyPays()
    {
        return $this->hasMany('app\index\model\Pay', 'user_id', 'id');
    }

    // 定义 多对多
    public function belongsToManyHby()
    {
        return $this->belongsToMany('app\index\model\Hby', 'user_hby', 'user_id', 'hby_id');
    }

}