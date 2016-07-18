<?php

require_once("Record.php");

class Set{
   private $setID;
   private $dateStart;
   private $dateEnd;
   private $dateComplete;
   private $batchID;
   private $records=array();
   
   public function __construct($setID,$startDate,$endDate,$completeDate,$batchID,$records){
      $this->setID = $setID;
      $this->dateStart = $startDate;
      $this->dateEnd = $endDate;
      $this->dateComplete = $completeDate;
      $this->batchID = $batchID;
      $this->records = $records;
   }
}

?>