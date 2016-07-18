<?php

class Record{
   private $recordID;
   private $dateFulfilled;
   private $setID;
   private $isDeleted;
   private $systemTypeID;
   private $fulfillmentFileID;   
   private $fieldValues=array();
   
   public function __construct($id,$date,$setID,$isDeleted,$systemType,$fulfillmentFileID,$fieldValues){
      $this->recordID=$id;
      $this->dateFulfilled=$date;
      $this->setID=$setID;
      $this->isDeleted=$isDeleted;
      $this->systemTypeID=$systemType;
      $this->fulfillmentFileID=$fulfillmentFileID;
      $this->fieldValues=$fieldValues;
   }
}

?>