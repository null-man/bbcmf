<?php

namespace app\db\model;

use bb\Model;

class Classes extends Model {

	protected $table = 'classes';

	protected $fillable = ['name'];

	// protected $timestamps = false;




	// 一对多 (反外键)
	public function students() {
        return $this->hasMany('Students', 'class_id');
    }

    // 远程一对多
    public function phone() {
    	return $this->hasManyThrough('Phone', 'Students', 'class_id', 'student_id');
    }


}