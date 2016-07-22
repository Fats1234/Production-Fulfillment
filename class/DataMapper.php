<?php

abstract class DataMapper{
   protected $adapter;
   
   public function __construct(mysqli $dbadapter){
      $this->adapter = $dbadapter;
   }
   
   abstract public function getByID($id);
}

?>