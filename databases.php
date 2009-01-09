<?php

/*
* databases.php: Handles the 'databases' subsystem of Tullok
*/

// Include header
require_once("resources/include/header.php");
require_once("resources/include/databases.class.php");

// Thickbox variable
$thickbox = isset($pathAddArgs['thickbox']) ? true : false;
$smarty->assign("thickbox", $thickbox);

// Select template from mode
switch (args(0)) {
case "":
    $allDatabases = Database::all();
    $smarty->assign("databases", $allDatabases);
    $smarty->display("databases.html");
    break;
case "table":
    $allDatabases = Database::all();
    $smarty->assign("databases", $allDatabases);
    $smarty->display("databases.table.html");
    break;
case "add":
    if (isset($_REQUEST['dbName']) or isset($target)) {
        $dbName = args(1) == "" ? $_REQUEST['dbName'] : args(1);
     $dbName = urldecode($dbName);
     echo $dbName;
     if ($dbName == "") {
         $smarty->assign("added", true);
         $smarty->assign("Status", "ERROR");
         $smarty->assign("Error", "Please enter a database name!");
         $smarty->assign("dbName",htmlentities($dbName));
         $smarty->display("databases.add.html");
     } else {
         $returner = Database::add($dbName);
         $smarty->assign("added", true);
         $smarty->assign("Status", htmlentities($returner['Status']));
         $smarty->assign("Error", htmlentities($returner['Error']));
         $smarty->assign("dbName",htmlentities($dbName));
         $smarty->display("databases.add.html");
     }
     
   } else {
     $smarty->assign("added", false);
     $smarty->display("databases.add.html");
   }
   
   break;
 case "del":
 case "delete":
   if (isset($_REQUEST['dbName']) or $target != "") {
     if (isset($_REQUEST['sure'])) {
       $dbName = args(2) == "" ? $_REQUEST['dbName'] : args(2);
       $dbName = urldecode($dbName);
       $jsonReturn = json_decode(Database::delete($dbName), true);
       $smarty->assign("deleted", true);
       $smarty->assign("sure", true);
       $smarty->assign("dbName", htmlentities($dbName));
       $smarty->assign("Status", htmlentities($jsonReturn['Status']));
       $smarty->assign("Error", htmlentities($jsonReturn['Error']));
       $smarty->display("databases.delete.html");
     } else {
       $dbName = args(2) == "" ? $_REQUEST['dbName'] : args(2);
       $dbName = urldecode($dbName);
       $smarty->assign("deleted", false);
       $smarty->assign("sure", false);
       $smarty->assign("dbName", $dbName);
       $smarty->display("databases.delete.html");
     }
   } else {
     $smarty->assign("deleted", false);
     $smarty->display("databases.delete.html");
   }
   
   break;
 case "json":
   switch (args(1)) {
   case "add":
     $dbName = args(2) == "" ? $_REQUEST['dbName'] : args(2);
     $dbName = urldecode($dbName);
     if ($dbName == "") {
       echo json_encode(array("Status"=>"ERROR","Error"=>"Please enter a database name!"));
     } else {
       $returner = Database::add($dbName);
       echo json_encode($returner);
     }
     break;
   case "del":
   case "delete":
     $dbName = args(2) == "" ? $_REQUEST['dbName'] : args(2);
     $dbName = urldecode($dbName);
     if (isset($_REQUEST['sure'])) {
       $returner = Database::delete($dbName);
     } else {
       $returner = array("Status"=>"UNSURE");
     }
     echo json_encode($returner);
     break;
   case "list":
     $returner = array();
     $key = 0;
     foreach (Database::all() as $dbObject) {
       $returner[$key]['name'] = $dbObject->name;
       $returner[$key++]['numTables'] = count($dbObject->tables);
     }
     echo json_encode($returner);
     break;
   case "all":
     $returner = array();
     $key = 0;
     foreach (Database::all() as $dbObject) {
       $returner[$key]['name'] = $dbObject->name;
       $returner[$key]['numTables'] = count($dbObject->tables);
       $returner[$key++]['tables'] = implode(", ", $dbObject->tables);
     }
     echo json_encode($returner);
     break;
   case "tables":
     $found = false;
     $dbName = args(2) == "" ? $_REQUEST['dbName'] : args(2);
     foreach (Database::all() as $dbObject) {
       if ($dbObject->name == $dbName) {
	 echo json_encode($dbObject->tables);
	 $found = true;
       }
     }
     if (!$found) {
       echo json_encode(array("Status"=>"ERROR", "Error"=>"$dbName not found"));
     }
     break;
   }
   
   break;
 case "xml":
   $dbObjects = Database::all();
   header("Content-type: text/xml");
   echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<databaselist>\n";
   foreach ($dbObjects as $dbObject) {
     echo "<database>\n";
     echo "\t<name>".$dbObject->name."</name>\n";
     echo "\t<numtables>".count($dbObject->tables)."</numtables>\n";
     echo "\t<tablelist>\n";
     foreach ($dbObject->tables as $table) {
			echo "\t\t<table>".$table."</table>\n";
     }
     echo "\t</tablelist>\n";
     echo "</database>\n";
   }
   echo "</databaselist>";
   break;
}
