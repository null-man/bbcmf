<?php

namespace app\index\model;

use bb\Model;

class Phone extends Model {

    protected $table = 'phone';

    // 一对一
    // 'local_key', 'parent_key'
    public function belongsToUser()
    {
        return $this->belongsTo('app\index\model\Users', 'user_id', 'id');
    }
}