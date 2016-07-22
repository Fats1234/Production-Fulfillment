<?php

require_once('DataObject.php');
require_once('Field.php');

class FieldValue extends DataObject{
   private $value;
   private $field; //field object
   
   public function __construct($value,Field $field,$id=null){
      $this->id = $id;
      $this->value = $value;
      $this->field = $field;
   }
}

?>