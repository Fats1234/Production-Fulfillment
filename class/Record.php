<?php

require_once('DataObject.php');
require_once('SystemType.php');

class Record extends DataObject{
   private $dateFulfilled;
   private $isDeleted;
   private $systemType;  //systemType object
   private $fieldValues=array(); //array of fieldValues Objects
   
   public function __construct($date,SystemType $systemType,$fieldValues,$isDeleted=0,$id=NULL){
      $this->id=$id;
      $this->dateFulfilled=$date;
      $this->isDeleted=$isDeleted;
      $this->systemType=$systemType;
      $this->fieldValues=$fieldValues;
   }
   
   public function getDateFulfilled(){
      return $this->dateFulfilled;
   }
   
   public function isDeleted(){
      return $this->isDeleted;
   }
   
   public function getSystemType(){
      return $this->systemType;
   }
   
   public function getFieldValues(){
      return $this->fieldValues;
   }
}

?>