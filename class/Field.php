<?php

class Field{
   private $fieldID;
   private $name;
   private $description;
   private $fieldOrder;
   private $checkDuplicate;
   
   public function __construct($fieldID,$name,$description,$fieldOrder,$checkDuplicate){
      $this->fieldID = $fieldID;
      $this->name = $name;
      $this->description = $description;
      $this->fieldOrder = $fieldOrder;
      $this->checkDuplicate = $checkDuplicate;
   }
}

?>