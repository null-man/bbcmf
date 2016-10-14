<?php

namespace app\db\model;

use bb\Model;

class Ztag extends Model {

	// 多态多对多关联
	public function posts() {
        return $this->morphedByMany('Zpost', 'taggable', 'ztaggable', 'taggable_id', 'tag_id');
    }

    // 多态多对多关联
    public function videos() {
        return $this->morphedByMany('Zvideo', 'taggable', 'ztaggable', 'taggable_id', 'tag_id');
    }


}
