<?php

/*
* sql.php: Handles the manual execution of SQL
*/

// Include header
require_once("resources/include/header.php");
require_once("resources/include/databases.class.php");

error_reporting(0);

// Thickbox variable
$thickbox = isset($additionalArguments['thickbox'])?true:false;
$smarty->assign("thickbox", $thickbox);

// Select template
$dbName = urldecode(args(0));
$sql = urldecode($_REQUEST["query"]);
mysql_select_db($dbName);
if(mysql_error()) {
  echo json_encode(array("Status"=>"ERROR", "Message"=>"$dbName not found"));
} else {
  $result = mysql_query($sql);
  if(mysql_error()) {
    echo json_encode(array("Status"=>"ERROR", "SQL"=>$sql, "Message"=>mysql_error()));
  } else {
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) $resultArray[] = $row;
    if (count($resultArray) > 0) {
      echo json_encode(array("Status"=>"OK", "SQL"=>$sql, "Result"=>$resultArray, "Columns"=>array_keys($resultArray[0]), "Message"=>"Query Executed Successfully"));
    } else {
      echo json_encode(array("Status"=>"OK", "SQL"=>$sql, "Result"=>$resultArray, "Columns"=>array(), "Message"=>"Query Executed Successfully"));
    }
  }
}
