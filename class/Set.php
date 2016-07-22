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
}

?>