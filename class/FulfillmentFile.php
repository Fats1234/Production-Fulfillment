<?php

require_once('DataObject.php');
require_once('SystemType.php');

class FulfillmentFile extends DataObject{
   private $host;
   private $systemType;  //system type object
   private $path;
   private $archiveDir;
   private $delimeter;
   private $fields;  //array of Field objects in the order to be mapped

   public function __construct($host,SystemType $sysType,$filePath,$archiveDir,$delimeter,$fields,$id=null){
      $this->id=$id;
      $this->host=$host;
      $this->systemType=$sysType;
      $this->path=$filePath;
      $this->archiveDir=$archiveDir;
      $this->delimeter=$delimeter;
      $this->fields=$fields;
   }
   
   public function getHost(){
      return $this->host;
   }
   
   public function systemType(){
      return $this->systemType;
   }
   
   public function getDelimeter(){
      return $this->delimeter;
   }
   
   public function getFilepath(){
      return $this->path;
   }
   
   public function getArchiveDir(){
      return $this->archiveDir;
   }
   
   public function getFields(){
      return $this->fields;
   }
}

?>