<?php

require_once('Record.php');

interface RecordMapperInterface{
   public function getByID($id);
   public function getBySetID($id);
   public function getIncomplete();
   public function deleteRecord($id);
}

?>