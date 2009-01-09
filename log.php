<?php

/*
* log.php: Handles logging from javascript to the tullok log
*/

// Include header
require_once("resources/include/header.php");

switch (args(0)) {
case "load":
    if (!isset($_REQUEST["time"])) die("Missing time parameter");
    if ($iniSettings["log_load"] != "true") die("Not allowed to log loads");
    $logfile = $iniSettings["log"] . "load.log";
    
    error_log($_REQUEST["time"] + "\n", 3, $logfile);
}
