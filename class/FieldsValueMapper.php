<?php

require_once('DataMapper.php');
require_once('FieldValue.php');
require_once('Field.php');
require_once('FieldMapper.php');

class FieldValuesMapper extends DataMapper{

   public function getByID($id){
      $query="SELECT field_id,field_value FROM fulfillment_field_values WHERE field_value_id=$id";
      if($fieldValueResult=$this->adapter->query($query)){
         list($fieldID,$fieldValue) = $fieldValueResult->fetch_row();
      }
            
      $fm = new FieldMapper($this->adapter);
      $fieldObj = $fm->getByID($fieldID);
      
      $fieldValueObj = new FieldValue($fieldValue,$fieldObj,$id);
      
      return $fieldValueObj;
   }

   public function searchValues($searchStr){
      $query="SELECT field_value_id,field_id,field_value FROM fulfillment_field_values WHERE field_value LIKE '%$searchStr%'";
      if($results = $this->adapater->query($query)){
         $fieldValuesMatched = array();
         while($match = $results->fetch_assoc()){
            $fm = new FieldMapper($this->adapter);
            $fieldObj = $fm->getByID($match['field_id']);
            
            $fieldValueObj = new FieldValue($match['field_value'],$fieldObj,$match['field_value_id']);
            $fieldValuesMatched[] = $fieldValueObj;
         }
      }
      
      return $fieldValuesMatched;
   }

}

?>