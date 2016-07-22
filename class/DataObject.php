<?php

abstract class DataObject{
   private $id;
   
   public function getDataID(){
      return $this->id;
   }
   
}

?>