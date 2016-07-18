<?php

require_once('Field.php');

class FieldMapper extends DataMapper{
   public function getByID($fieldID){
      $query="SELECT field_name,field_description,fulfillment_file_order,duplicate_check_level FROM fulfillment_fields WHERE field_id=$fieldID";
      if($fieldResult=$this->adapter->query($query)){
         $field=$fieldResult->fetch_assoc();
         //$fieldID,$name,$description,$fieldOrder,$checkDuplicate
         $fieldObj = new Field($fieldID,$field['field_name'],$field['description'],$field['fulfillment_file_order'],$field['duplicate_check_level']);
         return $fieldObj;      
      }
   }
}

?>