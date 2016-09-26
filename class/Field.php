<?php

require_once('DataObject.php');

class Field extends DataObject{
   private $name;
   private $description;
   private $displayOrder;
   private $checkDuplicate;
   
   public function __construct($name,$description,$dispOrder,$checkDuplicate,$id=null){
      $this->id = $id;
      $this->name = $name;
      $this->description = $description;
      $this->displayOrder = $dispOrder;
      $this->checkDuplicate = $checkDuplicate;
   }
   
   public function getName(){
      return $this->name;
   }
   
   public function getDescription(){
      return $this->description;
   }
   
   public function getDisplayOrder(){
      return $this->displayOrder;
   }
   
   public function checkDuplicateLevel(){
      return $this->checkDuplicate;
   }
   
}

?>