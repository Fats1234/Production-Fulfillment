<?php

abstract class DataObject{
   protected $id;
   
   public function getDataID(){
      return $this->id;
   }
   
}

?>