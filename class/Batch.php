<?php

require_once('DataObject.php');
require_once('Sets.php');
require_once('Records.php');

class Batch extends DataObject{
   private $batchID;
   private $dateStart;
   private $dateEnd;
   private $dateComplete;
   private $reference;
   private $batchLink;
   private $sets=array(); //array of Set Objects
}

?>