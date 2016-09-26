<?php

require_once('DataObject.php');
require_once('Set.php');
require_once('Record.php');

class Batch extends DataObject{
   private $dateStart;
   private $dateEnd;
   private $dateComplete;
   private $reference;
   private $batchLink;
   private $sets=array(); //array of Set Objects
}

?>