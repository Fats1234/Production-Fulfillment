<?php

class FieldValue{
   private $fieldValueID;
   private $value;
   private $field; //field object
   
   public function __construct($id,$value,$field){
      $this->fieldValueID = $id;
      $this->value = $value;
      $this->field = $field;
   }
}

?>