<?php

require_once('Field.php');
require_once('DataMapper.php');

class FieldMapper extends DataMapper{
   public function getByID($id){
      $query="SELECT field_name,field_description,display_order,duplicate_check_level FROM fulfillment_fields WHERE field_id=$id";
      if($fieldResult=$this->adapter->query($query)){
         $field=$fieldResult->fetch_assoc();
         //$fieldID,$name,$description,$fieldOrder,$checkDuplicate
         $fieldObj = new Field($field['field_name'],$field['description'],$field['display_orer'],$field['duplicate_check_level'],$id);
         return $fieldObj;      
      }
   }
}

?>