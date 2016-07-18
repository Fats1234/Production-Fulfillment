<?php
   require_once "functions.php";
   require_once "dbconfig.php";
   require_once "HTML/Table.php";

   include "header.php";
   
   $proDatabase = new mysqli($dbhost,$dbuser,$dbpass,$database);
   
   if(isset($_POST['complete'])){
      $recordIDs=explode(",",$_POST['recordIDs']);
      
      //Get the current set number
      $query="SELECT ibm_set_number FROM ibm_records_batch WHERE ibm_record_deleted=0 ORDER BY ibm_set_number DESC LIMIT 1";
      $results=$proDatabase->query($query);
      list($currentSet)=$results->fetch_row();
      $nextSet=$currentSet+1;
      
      //complete set by setting all current set 0 records to the next set
      foreach($recordIDs as $recordID){
         if(!empty($recordID)){
            $query="UPDATE ibm_records_batch SET ibm_set_number=$nextSet WHERE ibm_record_id=$recordID";
            //echo $query;
            $result=$proDatabase->query($query);
         }
      }
      
      header("Location: set.php");
   }
   
   //flag to inform there are no incomplete records
   $incompleteRecordsExist=false;
   
   //get a list of all system types and loop through all system types to import records to database
   $query="SELECT system_type_id,system_type_name FROM fulfillment_system_types";
   $results=$proDatabase->query($query);
   
   while($type=$results->fetch_assoc()){      
      //get a list of current servers and file paths for current system type
      $query="SELECT fulfillment_file_id, server_host, fulfillment_file_path, fulfillment_archive_directory
                  FROM fulfillment_servers
                  WHERE system_type_id=".$type['system_type_id'];
      $results=$proDatabase->query($query);
      
      while($file=$results->fetch_assoc()){
         $timestamp=date("Y-m-d-His");
         if(!empty($file['fulfillment_file_path'])){
            $tmpFulfillFile=getRecordFile($file['server_host'],$file['fulfillment_file_path']) or
               die("File was not downloaded correctly.  Please try refreshing page to try again!");
            if(importFileToDatabase($proDatabase,$tmpFulfillFile,$file['fulfillment_file_id'])){
               archiveFile($server['server_host'],$server['fulfillment_file_path'],$server['fulfillment_archive_directory']);
            }
         }
         
         //display records if there are incomplete records
         $query="SELECT record_id FROM fulfillment_records WHERE set_id=0 
                     AND fulfillment_file_id=".$file['fulfillment_file_id'].
                     " AND deleted=0 LIMIT 1";
         $records = $proDatabase->query($query);

         if($records->num_rows){
            echo "<font size=\"5\">The Following </font><font size=\"5\" color=\"red\"><b><u>Incomplete</u></b></font><font size=\"5\"> Records Were Found:</font><br><br>";
            echo genRecordsTable($proDatabase,$type['ibm_system_type_id']);
            $incompleteRecordsExist=true;
         }
         
      }
   }
   if(!$incompleteRecordsExist){
      echo "<font size=\"5\">No Incomplete Records Found</font><br>\n";
      echo "<font size=\"5\"><a href=\"set.php\">Click Here For The Most Recent Completed Set</a></font><br>";
   }
      
   function importFileToDatabase($database,$file,$fulfillFileID){

      $data=file_get_contents($file);
      unlink($file);
      //echo $data;
   
      if(!empty($data)){
         $data=preg_replace("/\r/","\n",$data);
         $records=explode("\n",$data);
         $logfile=fopen("/var/www/html/ibm/logs/log.txt","a") or die("Unable to write to log file!");
         fwrite($logfile,"DATA: ".$data."\n");
         //we need to get the fulfillment file's delimeter
         $query="SELECT fulfillment_file_delimeter FROM fulfillment_files WHERE fulfillment_file_id=$fulfillFileID"
         $result=$database->query($query);
         list($delimeter)=$result->fetch_row();
         //grab the order of the fields from database
         $query="SELECT field_id FROM fulfillment_field_names WHERE fulfillment_file_id=$fulfillFileID ORDER BY fulfillment_file_order";
         $results=$database->query($query);
         $fieldsRequired=array();
         while(list($field)=$results->fetch_row()){
            $fieldsRequired[]=$field;
         }
         
         foreach($records as $record){            
            if(!empty($record)){
               fwrite($logfile,"RECORD: ".$record."\n");
               $timestamp=date("Y-m-d-His");
         
               $values=explode($delimeter,$record);
               
               //check to make sure number of fields is correct
               if(count($fieldOrder) != count($values)) die("Error! Incorrect number of fields found in Record: $record");
               
               //grab a record_id
               $query="INSERT INTO fulfillment_records SET fulfillment_file_id=$fulfillFileID, fulfillment_date='$timestamp'";
               if($database->query($query)) $recordID=$database->insert_id;
               
               //insert field values into database               
               $valuesArray=array()
               foreach($values as $order=>$value){
                  $valuesArray[]="(".$fieldsRequired[$order].",$recordID,$value)";
               }
               $query="INSERT INTO fulfillment_field_values (field_id,record_id,field_value) VALUES ".implode(",",$valuesArray);
               $database->query($query);
            }
         }
         fclose($logfile);
         return TRUE;
      }else{
         return FALSE;
      }
   }
   
   function getRecordFile($ftpServer,$remoteFile){
      //open connection to ftp
      $success=FALSE;
      $conn_id = ftp_connect($ftpServer) or die("Couldn't connect to ftp server: $ftpServer");
      ftp_login($conn_id,'archive','polywell');
      if(ftp_size($conn_id,$remoteFile)==-1){
         $success=TRUE;
      }else{
         $tmpFile=tempnam('tmp','');
         if(ftp_get($conn_id,$tmpFile,$remoteFile,FTP_ASCII)){
            $success=TRUE;
         }
      }
      ftp_close($conn_id);
      
      if($success){
         return $tmpFile;
      }else{
         return FALSE;
      }
   }
   
   function archiveFile($ftpServer,$file,$archiveFile){
      //open connection to ftp
      $conn_id = ftp_connect($ftpServer);
      
      ftp_login($conn_id,'archive','polywell');
      ftp_rename($conn_id,$file,$archiveFile);
      ftp_close($conn_id);

   }
   
   //function to generate records table for a system type
   function genRecordsTable($database,$systemType=1,$setNumber=0){
      $query="SELECT ibm_record_id, ibm_serial_number 
               FROM ibm_records_batch 
               WHERE ibm_record_deleted=0 
               AND ibm_set_number=$setNumber 
               AND ibm_system_type_id=$systemType
               ORDER BY ibm_record_id";
      //echo $query;
      $records=$database->query($query);
      
      $attrs=array('border' => '1');
      $recordsTable = new HTML_TABLE($attrs);
      $recordsTable->setHeaderContents(0,0,"Rec. No.");
      $recordsTable->setHeaderContents(0,1,"Serial Number");
      
      $row=1;
      $recordIDs="";
      $maxMAC=0;
      
      while($record=$records->fetch_assoc()){
         $serial=$record['ibm_serial_number'];
         $recordsTable->setCellContents($row,0,$row);
         $recordsTable->setCellContents($row,1,$record['ibm_serial_number']);
         $recordIDs.=$record['ibm_record_id'].",";
         //grab mac addresses from database
         $query="SELECT ibm_macaddress FROM ibm_batch_macaddress WHERE ibm_record_id=".$record['ibm_record_id']." ORDER BY ibm_interface_number";
         //echo $query;
         $macaddresses=$database->query($query);
         
         $macaddress=array();
         while($results=$macaddresses->fetch_assoc()){
            $macaddress[]=$results['ibm_macaddress'];
         }
         //we want to find the biggest amount of mac addresses in order to set the header columns
         $maxMAC=max($maxMAC,count($macaddress));
         
         for($i=0;$i<count($macaddress);$i++){
            $recordsTable->setCellContents($row,$i+2,$macaddress[$i]);
         }
         
         $row++;
         
         //search for duplicate in database
         $query="SELECT ibm_fulfill_date,ibm_set_number
                     FROM ibm_records_batch
                     WHERE ibm_serial_number='$serial'
                     AND ibm_record_deleted=0
                     AND ibm_set_number!=0";               
         $duplicateRecords=$database->query($query);
         if($duplicateRecords->num_rows){
            while($duplicate=$duplicateRecords->fetch_assoc()){
               echo "<font size='5'><font color='red'>!!!WARNING!!!</font>Serial Number <font color='red'>$serial</font>".
                        " Has Already been Fulfilled on <font color='red'>".$duplicate['ibm_fulfill_date']."</font>".
                        " In Set Number <font color='red'>".$duplicate['ibm_set_number']."</font></font><br>";
            }
         }
      }
      
      //set MAC Address Header column
      for($i=0;$i<$maxMAC;$i++){
         $recordsTable->setHeaderContents(0,$i+2,"MAC Address (eth$i)");
      }
     
      //get system type name
      $query="SELECT ibm_system_type_name FROM ibm_system_type WHERE ibm_system_type_id=$systemType";
      $result=$database->query($query);
      list($systemName)=$result->fetch_row();
     
      $altAttrs=array('class' => 'alt');
      $recordsTable->altRowAttributes(0,null,$altAttrs);
      
      $returnStr = "<br>\n<font size=\"5\"><u>$systemName</u> <br> Total Records: $records->num_rows</font><br>\n";
      $returnStr .= startForm("incomplete.php","POST");
      $returnStr .= genHidden("recordIDs",$recordIDs);
      $returnStr .= genHidden("systemType",$systemType);
      if(checkSerialDuplicates($database,$setNumber,$systemType)){         
         $returnStr .= genButton("complete","complete","Complete Current Set");
      }
      $returnStr .= endForm();
      $returnStr .= $recordsTable->toHTML();
      $returnStr .= "<br>\n";
      return $returnStr;
      
   }
   
   //find duplicated serial within a set
   function checkSerialDuplicates($database,$set=0,$systemType=1){
      $query="SELECT ibm_serial_number, COUNT(*) duplicate_count 
                  FROM ibm_records_batch
                  WHERE ibm_set_number=$set
                  AND ibm_record_deleted=0
                  AND ibm_system_type_id=$systemType
                  GROUP BY ibm_serial_number 
                  HAVING duplicate_count > 1";
      
      $duplicatedSerials=$database->query($query);
      if($duplicatedSerials->num_rows){
         while($duplicate=$duplicatedSerials->fetch_assoc()){
            $duplicateSerialNumber=$duplicate['ibm_serial_number'];
            $duplicateCount=$duplicate['duplicate_count'];
            echo "<font size='5'><font color='red'>!!!ERROR!!!</font>Serial Number <font color='red'>$duplicateSerialNumber</font> ".
                  "was found <font color='red'>$duplicateCount</font> times in this Set</font><br>";            
         }
         return FALSE;
      }else{
         return TRUE;
      }
   }
   
   include "footer.php";
?>