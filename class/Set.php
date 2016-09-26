<?php

require_once('DataObject.php');
require_once('Record.php');

class Set extends DataObject{
   private $dateStart;
   private $dateEnd;
   private $dateComplete;
   private $records=array();
   private $systemType; //systemType Object
   
   public function __construct($startDate,$endDate,$completeDate,SystemType $systemType,$records,$id=null){
      $this->id = $id;
      $this->dateStart = $startDate;
      $this->dateEnd = $endDate;
      $this->dateComplete = $completeDate;
      $this->systemType = $systemType;
      $this->records = $records;
   }
   
   public function getDateStart(){
      return $this->dateStart;
   }
   
   public function getDateEnd(){
      return $this->dateEnd;
   }
   
   public function getDateComplete(){
      return $this->dateComplete;
   }
   
   public function getRecords(){
      return $this->records;
   }
   
   public function getSysType(){
      return $this->systemType;
   }
}

?>