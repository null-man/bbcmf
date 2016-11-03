<?php

namespace app\index\model;

use bb\Model;

class Cls extends Model {

    protected $table = 'class';

    // 定义关联
    public function users()
    {
        return $this->belongsTo('Users');
    }
}