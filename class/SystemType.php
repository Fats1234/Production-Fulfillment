<?php

require_once('DataObject.php');

class SystemType extends DataObject{
   private $name;
   private $description;
   private $fields=array();  //array of field objects for this system type
   
   public function __construct($name,$desc,$fields,$id=null){
      $this->id = $id;
      $this->name = $name;
      $this->description = $desc;
      $this->fields = $fields;
   }
   
   public function getName(){
      return $this->name;
   }
   
   public function getDescription(){
      return $this->description;
   }
   
   public function getFields(){
      return $this->fields;
   }
}

?>