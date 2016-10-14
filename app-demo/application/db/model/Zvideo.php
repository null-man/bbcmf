<?php

namespace app\db\model;

use bb\Model;

class Zvideo extends Model {

	// 多态关联
	public function tags() {
        return $this->morphToMany('Ztag', 'ztaggable');
    }
}
