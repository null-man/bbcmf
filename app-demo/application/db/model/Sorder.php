<?php

namespace app\db\model;

use bb\Model;

class Sorder extends Model {

	// 多态关联
	public function photos() {
        return $this->morphMany('Sphoto', 'imageable');
    }
}