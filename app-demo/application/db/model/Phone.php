<?php

namespace app\db\model;

use bb\Model;

class Phone extends Model {

	// 反一对一
	public function student() {
        return $this->belongsTo('Students', 'student_id');
    }


    // classes
    // 	id
    // 	name

    // students
    // 	id
    // 	class_id
    // 	name

    // phone
    // 	id
    // 	student_id
    // 	phone

    // interests
    // 	id
    // 	name

    // students_interests
    // 	id
    // 	students_id
    // 	interests_id

}