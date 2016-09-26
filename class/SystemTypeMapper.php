<?php

require_once('SystemType.php');
require_once('Field.php');
require_once('FieldMapper.php');
require_once('DataMapper.php');

class SystemTypeMapper extends DataMapper{
   public function getByID($id){
      $query="SELECT system_type_name,system_type_desc FROM fulfillment_system_types WHERE system_type_id=$id";
      if($result=$this->adapter->query($query)){
         $systemType = $result->fetch_assoc();
      }
      
      //get fields associated with this system type
      $query="SELECT field_id FROM fulfillment_fields WHERE system_type_id=$id";
      if($result=$this->adapter->query($query)){
         $fm = new FieldMapper($this->adapter);
         $systemFieldsArr=array();
         while(list($fieldID) = $result->fetch_row()){
            $systemFieldsArr[] = $fm->getByID($fieldID);
         }
      }
      
      $systemTypeObj = new SystemType($systemType['system_type_name'],$systemType['system_type_desc'],$systemFieldsArr,$id);
      return $systemTypeObj;
   }
}

?>