<?php

require_once('Record.php');
require_once('FieldValue.php');
require_once('DataMapper.php');
require_once('FieldValuesMapper.php');

class RecordMapper extends DataMapper{
   public function getByID($id,$searchDeleted=0){
      //get the record based on record id
      $query="SELECT system_type_id,fulfillment_date,deleted FROM fullfillment_records WHERE record_id=$id";
      if(!$searchDeleted) $query.=" AND deleted=0";
      if($recordResult = $this->adapter->query($query)){
         $record=$recordResult->fetch_assoc();
      }
      
      //get the field values for the associated record id
      $query="SELECT field_value_id FROM fulfillment_field_values WHERE record_id=$id";
      $fvResults = $this->adapter->query($query);
            
      $fvm = new FieldValuesMapper($this->adapter);
      $fieldValuesArr=array();
      
      while(list($fieldValueID) = $fvResults->fetch_row()){
         $fieldValue=$fvm->getByID($fieldValueID);
         $fieldValuesArr[]=$fieldValue;
      }
      
      $sm = new SystemTypeMapper($this->adapter);
      $systemTypeObj = $sm->getByID($record['system_type_id']);
      
      //$id,$date,$setID,$isDeleted,$systemType,$fieldValues
      $recordObj = new Record($record['fulfillment_date'],$systemTypeObj,
                                 $fieldValues, $record['deleted'],$id);
                                 
      return $recordObj;
   }
   
   public function create(Record &$record){
      $query="INSERT INTO fulfillment_records SET".
                     " system_type_id=".$record->getSystemType()->getID().
                     ",fulfillment_file_id=".$record->getFulfillmentFile()->getID().
                     ",fulfillment_date='".$record->getFulfillmentDate()."'";
      
      if($this->adapter->query($query)){
         $newRecordID=$this->adapater->insert_id);
         
         foreach(&$record->getFieldValues() as &$fieldValue){
            $query="INSERT INTO fulfillment_field_values SET".
                           " field_id=".$fieldValue->getField()->getDataID().
                           ",record_id=".$newRecordID.
                           ",field_value='".$fieldValue->getValue()."'";
            
            if(!$this->adapter->query($query)){
               return FALSE;
            }
         }
         $newRecord=$this->adapter->getByID($newRecordID);
         return $newRecord;
      }else{
         return FALSE;
      }
   }
   
   public function undeleteRecord($id){
      $query="UPDATE fulfillment_records SET deleted=0 WHERE record_id=$id";
      if($this->adapter->query($query)){
         return TRUE;
      }else{
         return FALSE;
      }
   }
   
   public function deleteRecord($id){
      $query="UPDATE fulfillment_records SET deleted=1 WHERE record_id=$id";
      if($this->adapter->query($query)){
         return TRUE;
      }else{
         return FALSE;
      }
   }
   
}

?>