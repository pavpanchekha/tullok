<?php

class Database {
  public $name = "";
  public $tables = array();
  
  function all() {
    global $conn;
    $arrayOfDatabases = array();
    $result = mysql_query("SHOW DATABASES", $conn);
    
    while ($row = mysql_fetch_row($result)) {
      $db = Database::load($row[0]);
      $arrayOfDatabases[] = $db;
    }
    
    sort($arrayOfDatabases);
    return $arrayOfDatabases;
  }
  
  function load($dbName) {
    global $conn;
    $db = new Database;
    $db->name = mysql_real_escape_string($dbName);
    $result = mysql_query("SHOW TABLES FROM `" . $db->name . "`", $conn);
    
    if ($result) {
      while($row = mysql_fetch_row($result)) $db->tables[] = $row[0];
      return $db;
    }
  }
  
  function add($name = false) {
    global $conn;
    $returner = array();
    
    if (!$name) {
      $name = $this->name;
    }
    
    $name = mysql_real_escape_string($name);
    $result = mysql_query("CREATE DATABASE `$name`", $conn);
    
    $returner['SQL'] = "CREATE DATABASE `$name`";
    
    if (mysql_error() != "") {
      $returner['Status'] = "ERROR";
      $returner['Message'] = "Error adding database $name" . mysql_error();
      return $returner;
    } else {
      $returner['Status'] = "OK";
      $returner['Message'] = "Database $name added successfully";
      return $returner;
    }
  }
  
  function delete($name = false) {
    global $conn;
    
    $returner = array();
    if (!$name) {
      $name = $this->name;
    }
    
    $name = mysql_real_escape_string($name);
    $result = mysql_query("DROP DATABASE `$name`", $conn);
    
    $returner['SQL'] = "DROP DATABASE `$name`";
    
    if (mysql_error() != "") {
      $returner['Status'] = "ERROR";
      $returner['Message'] = "Error deleting database $name: " . mysql_error();
      return $returner;
    } else {
      $returner['Status'] = "OK";
      $returner['Message'] = "Database $name deleted successfully";
      return $returner;
    }
  }
}

?>