<?php
// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZergL <lin029011@163.com>
// +----------------------------------------------------------------------

namespace bb\service;

use bb\Service;

class AdminTmplService extends Service {

    // 模型
    protected $model;

    public function setModel($model) {
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function setNavigations($navigations) {

        $navs = [];
        foreach($navigations as $url => $name) {
            $url = admin_url($url);
            if(md5($url) == I('action')) {
                $navs[] = [$name, $url, true];
            } else {
                $navs[] = [$name, $url, false];
            }
        }

        $this->model->navs = $navs;
    }

    public function setID($id) {
        $this->model->_id = $id;
    }

    public function json_data($type) {
        return $this->model->json_data($type);
    }


    public function setPage($page_now = 1, $page_num = 20, $url = 'index') {
        $this->model->page_now = $page_now;
        $this->model->page_num = $page_num;
        $this->model->page_url = admin_url($url);
    }


    public function setFilter($filter = array(), $filter_value = null) {

        $this->model->set_filter_list($filter);

        $_filter = [];
        foreach($filter as $key) {
            if(isset($filter_value[$key]) && !empty($filter_value[$key])) {
                $_filter[$key] = $filter_value[$key];
            } else {
                $_filter[$key] = I($key, '');
            }
        }

        foreach($_filter as $param => $value) {
            if(!empty($value) || $value === "0") {
                if (is_array($value) && !empty($value[0])) {
                    $final_filter[$param] = explode(',', $value[0]);
                } else {
                    $final_filter[$param] = $value;
                }
            }
        }

        if (!empty($final_filter)) {
            $this->model->filter = $final_filter;
        }
    }


    public function insert($data) {
        $duplication = $this->check_duplication($this->model, $data);
        if($duplication !== true) return $duplication;

        $this->model->fill($data);

        // 处理文件类型
        foreach ($this->model->get_model() as $field => $info){
            if($info[1] == 'file'){
                // 上传文件
                $tempFile = $_FILES[$field]['tmp_name'];

                if($tempFile){
                    $uploadDir = 'static/upload';

                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777);
                    }

                    $targetFile = $uploadDir . '/' . time() . $_FILES[$field]['name'];
                    move_uploaded_file($tempFile, $targetFile);
                    $this->model->$field = $targetFile;
                }
            }
        }


        // 一对一
        $onetoone = $this->model->get_onetoone();
        if(!empty($onetoone)) {
            foreach ($onetoone as $field => $info) {
                $pre  = $field.'_';
                $_data = [];
                foreach($data as $key => $value) {
                    if(strpos($key, $pre) === 0) {
                        $_data[substr($key, strlen($pre))] = $value;
                    }
                }
                $model = new $info[0]();
                $s = new AdminTmplService();
                $s->setModel($model);
                // $d[$value[1]] = $this->model['id'];
                if(!$s->insert($_data)) {
                    return false;
                }
                // $duplication = $this->check_duplication($model, $_data);
                // if($duplication !== true) return $duplication;
                // $model->fill($_data);
                // $model->save();
                $this->model->$field = $model->id;
            }
        }

        // self
        if(!$this->model->save()) {
            if(!empty($this->model->error)) {
                return $this->model->error;
            }
            return false;
        }

        // 多对一
        $manytoone = $this->model->get_manytoone();
        if(!empty($manytoone)) {
            foreach ($manytoone as $field => $value) {
                $relate_model = $value[0];
                $relate_id = $value[1];

                if(in_array($field, $this->model->get_field()) && isset($data[$field])) {
                    foreach ($data[$field] as $d) {
                        $m = new $relate_model();
                        $s = new AdminTmplService();
                        $s->setModel($m);
                        $d[$value[1]] = $this->model['id'];
                        if(!$s->insert($d)) {
                            return false;
                        }
                    }
                }
            }
        }

        // 多对多
        $manytomany = $this->model->get_manytomany();
        if(!empty($manytomany)) {
            foreach ($manytomany as $field => $value) {
                if(in_array($field, $this->model->get_field()) && isset($data[$field])) {
                    $relate_model = $value[0];  // 关联模型
                    $inner_table  = $value[1];  // 中间表
                    $id           = $value[2];  // id
                    $relate_id    = $value[3];  // 关联id
                    $many = $this->model->belongsToMany($relate_model, $inner_table, $id, $relate_id);
                    $ret = $many->attach($data[$field]);
                }
            }
        }

        return true;
    }


    public function update($id, $data) {

        $this->model = $this->model->find($id);
        $duplication = $this->check_duplication($this->model, $data);
        if($duplication !== true) return '数据没有变化';
        $this->model->fill($data);

        // 处理文件类型
        foreach ($this->model->get_model() as $field => $info){
            if($info[1] == 'file'){
                // 上传文件
                $tempFile = $_FILES[$field]['tmp_name'];

                if($tempFile){
                    $uploadDir = 'static/upload';

                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777);
                    }

                    $targetFile = $uploadDir . '/' . time() . $_FILES[$field]['name'];
                    move_uploaded_file($tempFile, $targetFile);
                    $this->model->$field = $targetFile;
                }
            }
        }

        // 一对一
        $onetoone = $this->model->get_onetoone();
        if(!empty($onetoone)) {
            foreach ($onetoone as $field => $info) {
                $pre  = $field.'_';
                $_data = [];
                foreach($data as $key => $value) {
                    if(strpos($key, $pre) === 0) {
                        $_data[substr($key, strlen($pre))] = $value;
                    }
                }
                $model = $info[0]::find($this->model->$field);
                // $duplication = $this->check_duplication($model, $_data);
                // if($duplication !== true) return '数据没有变化';
                // $model->fill($_data);

                $s = new AdminTmplService();
                $s->setModel($model);
                // $d[$value[1]] = $this->model['id'];
                if(!$s->update($model['id'], $_data)) {
                    return false;
                }

                // if(!$model->save()) {
                //     if(!empty($model->error)) {
                //         return $model->error;
                //     }
                //     return false;
                // }
            }
        }
        
        // 多对多
        $manytomany = $this->model->get_manytomany();
        if(!empty($manytomany)) {
            foreach ($manytomany as $field => $value) {
                if(in_array($field, $this->model->get_field())) {
                    $relate_model = $value[0];  // 关联模型
                    $inner_table  = $value[1];  // 中间表
                    $id           = $value[2];  // id
                    $relate_id    = $value[3];  // 关联id
                    $many = $this->model->belongsToMany($relate_model, $inner_table, $id, $relate_id);
                    if(isset($data[$field])) {
                        $many->sync($data[$field]);
                    } else {
                        $many->detach();
                    }
                }
            }
        }

        // 多对一
        $manytoone = $this->model->get_manytoone();
        if(!empty($manytoone)) {
            foreach ($manytoone as $field => $value) {
                $relate_model = $value[0];
                $relate_id = $value[1];

                if(in_array($field, $this->model->get_field()) && isset($data[$field])) {

                    $many = $this->model->hasMany($relate_model, $relate_id)->get();

                    $updated = [];

                    foreach ($data[$field] as $d) {
                        $m = new $relate_model();
                        $s = new AdminTmplService();
                        $s->setModel($m);
                        $d[$value[1]] = $id;
                        if(!empty($d['id'])) {
                            if(!$s->update($d['id'], $d)) {
                                return false;
                            }
                            $updated[] = $d['id'];
                        } else {
                            if(!$s->insert($d)) {
                                return false;
                            }
                        }
                    }

                    

                    foreach($many as $m) {
                        if(!in_array(strval($m->id), $updated)) {
                            $m->delete();
                        }
                    }
                } else {
                    $relate_model::where($relate_id, $id)->delete();
                }
            }
        }
        

        // self
        if(!$this->model->save()) {
            if(!empty($this->model->error)) {
                return $this->model->error;
            }
            return false;
        }


        

        return true;

    }

    public function delete($id) {

        $this->model = $this->model->find($id);

        // 一对一
        $onetoone = $this->model->get_onetoone();
        if(!empty($onetoone)) {
            foreach ($onetoone as $field => $info) {
                $model = $info[0]::find($this->model->$field);
                $model->delete();
            }
        }

        // 多对多
        $manytomany = $this->model->get_manytomany();
        if(!empty($manytomany)) {
            foreach ($manytomany as $field => $value) {
                $relate_model = $value[0];  // 关联模型
                $inner_table  = $value[1];  // 中间表
                $id           = $value[2];  // id
                $relate_id    = $value[3];  // 关联id
                $many = $this->model->belongsToMany($relate_model, $inner_table, $id, $relate_id);
                $many->detach();
            }
        }

        // self
        $this->model->delete();

    }

    // 判断重复
    protected function check_duplication($model, $data) {
        if($model->get_duplication()) {

            $fields = $model->get_field();
            $_data = [];
            foreach ($data as $key => $value) {
                if(in_array($key, $fields)) $_data[$key] = $value;
            }
            
            $exists = $model->where($_data)->first();
            if(!is_null($exists)) {
                return '表' . $this->model->getTable(). '中已存在该数据';
            }
        } 
        return true;
    }                

}