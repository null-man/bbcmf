<?php

namespace app\db\model;

use bb\Model;

class Students extends Model {

    // 可写入值白名单
    protected $fillable = ['name'];

    // 更新上层时间戳
    protected $touches = array('classes');

	// 一对一
	public function phone() {
        return $this->hasOne('Phone', 'student_id');
    }

    // 反一对多（外键）
    public function classes() {
    	return $this->belongsTo('Classes', 'class_id');
    }

    // 多对多
	public function interests() {
        return $this->belongsToMany('Interests', 'students_interests')->withPivot('id');// 枢纽表属性
        // 自动维护枢纽表的 created_at 和 updated_at 时间戳
        // withTimestamps
    }

    // 重写日期转换器
    // public function getDates() {
    //     return array('created_at');
    // }

    // 注册事件绑定
    protected static function boot() {
        // creating 、 created 、 
        // updating 、 updated 、 
        // saving 、 saved 、 
        // deleting 、 deleted 、 
        // restoring 、 restored 
        parent::boot();
        Students::saving(function($student)
        {
            if (!$student->isValid()) return false;
        });
        // Setup event bindings...
    }

    public function isValid() {
        dump(strlen($this->name) < 10);
        return strlen($this->name) < 10;
    }
}