<?php

require_once('Record.php');
require_once('DataMapper.php');
require_once('FieldValuesMapper.php');
require_once('RecordMapperInterface.php');

class RecordMapper extends DataMapper implements RecordMapperInterface{
   public function getByID($id,$searchDeleted=0){
      //get the record based on record id
      $query="SELECT set_id,system_type_id,fulfillment_file_id,fulfillment_date,deleted FROM fullfillment_records WHERE record_id=$id";
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
         $fieldsValue=$fvm->getByID($fieldValueID);
         $fieldValuesArr[]$fieldsValue
      }
      
      //$id,$date,$setID,$isDeleted,$systemType,$fulfillmentFileID,$fieldValues
      $recordObj = new Record($id,$record['fulfillment_date'],$record['set_id'],
                                 $record['deleted'],$record['systemTypeID'],
                                 $record['fulfillment_file_id'],$fieldValues);
                                 
      return $recordObj;
   }
   
   public function create(Record &$record){
      $query="INSERT INTO fulfillment_records SET system_type_id=?,fulfillment_file_id=?,fulfillment_date=?,set_id=?";
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