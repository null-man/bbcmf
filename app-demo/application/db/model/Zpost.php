<?php

namespace app\db\model;

use bb\Model;

class Zpost extends Model {

	// 多态关联
	public function tags() {
        return $this->morphToMany('Ztag', 'taggable', 'ztaggable', 'taggable_id', 'tag_id');
    }

}
