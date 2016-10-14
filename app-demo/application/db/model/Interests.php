<?php

namespace app\db\model;

use bb\Model;

class Interests extends Model {

	// 多对多
	public function students() {
        return $this->belongsToMany('Students', 'students_interests');
    }

}