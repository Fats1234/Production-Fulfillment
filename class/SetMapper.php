<?php

require_once('Set.php');
require_once('Record.php');
require_once('DataMapper.php');
require_once('RecordMapper.php');

class SetMapper extends DataMapper{
   public function getByID($setID){
      //get all records by set id
      $query="SELECT record_id FROM fulfillment_records WHERE set_id=$setID ORDER_BY record_id";
      if($records=$this->adapter->query($query)){
         $recordArr=array();
         $rm = new RecordMapper($this->adapter);
         while($record=$records->fetch_assoc()){
            $recordArr[]=$rm->getByID($record['record_id']);
         }
      }
      
      //get start date
      $query="SELECT fulfillment_date FROM fulfillment_records WHERE set_id=$setID ORDER_BY fulfillment_date LIMIT 1";
      if($result=$this->adapter->query($query)){
         list($startDate) = $result->fetch_row();
      }
      
      //get end date
      $query="SELECT fulfillment_date FROM fulfillment_records WHERE set_id=$setID ORDER_BY fulfillment_date DESC LIMIT 1";
      if($result=$this->adapter->query($query)){
         list($endDate) = $result->fetch_row();
      }
      
      //get set details
      $query="SELECT set_completion_date, batch_id FROM fulfillment_sets WHERE set_id=$setID";
      if($setResult=$this->adapter->query($query)){
         $setDetails = $setResult->fetch_assoc();
      }
      
      $setObj = new Set($setID,$startDate,$endDate,$setDetails['set_completion_date'],$setDetails['batch_id'],$recordArr);
      
      return $setObj;
   }
   
   public function getIncomplete(){
      return $this->getByID(0);
   }
}

?>