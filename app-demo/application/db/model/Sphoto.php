<?php

namespace app\db\model;

use bb\Model;

class Sphoto extends Model {

	// 多态关联(被关联方)
	public function imageable() {
        return $this->morphTo();
    }


 //    staff
 //    	id - integer
 //    	name - string

	// sorders
 //    	id - integer
 //    	price - integer

	// sphotos
 //    	id - integer
 //    	path - string
 //    	imageable_id - integer
 //    	imageable_type - string

}