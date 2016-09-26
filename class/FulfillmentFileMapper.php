<?php

require_once('DataMapper.php');
require_once('FulfillmentFile.php');
require_once('SystemTypeMapper.php');
require_once('SystemType.php');
require_once('FieldMapper.php');
require_once('Field.php');

class FulfillmentFileMapper extends DataMapper{
   public function getByID($id){
      $query="SELECT server_host,system_type_id,file_path,
                  archive_directory,delimeter 
                  FROM fulfillment_files WHERE fulfillment_file_id=$id";
                  
      if($result=$this->adapter->query($query)){
         $fulfillmentFile=$result->fetch_assoc();
      }
      
      $sm = new SystemTypeMapper($this->adapter);
      $systemType=$sm->getByID($fulfillmentFile['system_type_id']);
      
      $query="SELECT field_id FROM fulfillment_file_field_order  
                  WHERE fulfillment_file_id=$id ORDER BY fulfillment_file_field_order";
      if($result=$this->adapter->query($query)){
         $fieldsArr=array();
         $fm = new FieldMapper($this->adapter);
         while(list($fieldID)=$result->fetch_row()){
            $fieldsArr[]=$fm->getByID($fieldID);
         }
      }
      
      $fulfillFileObj = new FulfillmentFile($fulfillmentFile['server_host'],$systemType,
                              $fulfillmentFile['file_path'],$fulfillmentFile['archive_directory'],
                              $fulfillmentFile['delimter'],$fieldsArr,$id);
                              
      return $fulfillFileObj;
   }
   
   public function getAll(){
      $query="SELECT fulfillment_file_id FROM fulfillment_files";
      if($result=$this->adapter->query($query)){
         $fileList=array();
         while(list($fileID)=$result->fetch_row()){
            $fileList[]=$this->getByID($fileID);
         }
         return $fileList;
      }
   }
} 

?>