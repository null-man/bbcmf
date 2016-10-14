<?php

class Crondb{

   /**
    * query语句
    *
    * @param $sql 要执行的sql语句
    * @return array
    */
   public function _query($sql){
      $data = array();
      $data['state'] = '0';
      $data['data'] = $this->lastErrorMsg();

      if(!$this){
         return $data;
      }

      $ret = $this->query($sql);

      if(!$ret){
         return $data;
      }

      $data['state'] = '1';
      while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
         $data['data'] = $row;
      }

      return $data;
   }


   /**
    * exec语句
    *
    * @param $sql 要执行的sql语句
    * @return array
    */
   public function _exec($sql, $query_sql = '', $type = ''){
      $data = array();
      $data['state'] = '0';
      $data['data'] = $this->lastErrorMsg();

      if(!$this){
         return $data;
      }

      $ret = $this->exec($sql);
      if(!$ret){
         return $data;
      }

      $data['state'] = '1';
      $data['data'] = $ret;

      if($type === 'add'){
         $ret = $this->query($query_sql);

         while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            $data['data'] = $row;
         }
      }

      return $data;
   }

   /**
    * 关闭数据库
    *
    */
   public function _close_db(){
      $this->close();
   }

}