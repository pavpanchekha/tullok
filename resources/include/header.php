<?php

// Sessions
require_once("resources/include/session.class.php");

// Grab variables from URI
$pathAllArgs = explode("?", $_SERVER['REQUEST_URI']);
$pathMainArgs = explode("/", $pathAllArgs[0]);
$pathAddArgsRaw = $pathAllArgs[1];
$basename = basename($_SERVER['SCRIPT_NAME'], ".php");
$indexOfFile = -1;
foreach ($pathMainArgs as $key=>$dir) {
  if ($dir == $basename or $dir == $basename . ".php")
    $indexOfFile = $key;
}

for ($i=0; $i<$indexOfFile; $i++)
  $path .= $pathMainArgs[$i]."/";
if ($path == "")
  $path = "/";
$root = "http://" . $_SERVER['HTTP_HOST'];
for ($i=0; $i<=$indexOfFile; $i++)
  unset($pathMainArgs[$i]);
$pathMainArgs = array_values($pathMainArgs);
$pathAddArgs = array();
parse_str($pathAddArgsRaw, $pathAddArgs);

// Check to see if tullok is actually installed
$iniPath = "resources/secure/config.ini";
if (!file_exists($iniPath)) {
  header("Location: ".$path."install.php");
  die();
}

// Parse INI file
$iniSettings = parse_ini_file($iniPath);
$theme = $iniSettings['theme'];
$encryptKey = $iniSettings['key'];

$session = new Session();
if (!$session->loggedIn and $basename != "accounts")
  header("Location: $path"."accounts/login");

// MySQL connection
if ($session->loggedIn)
  require_once("resources/include/connect.php");

// Smarty files
require_once("resources/include/smarty.php");

// URI Variable handling
function args($index, $additional = false) {
  global $pathAddArgs;
  global $pathMainArgs;
  if ($additional)
    return $pathAddArgs[$index];
  else
    return $pathMainArgs[$index];
}

// Assign Smarty variables
$smarty->assign("root", $root);
$smarty->assign("path", $path);
$smarty->assign("mode", args(0));
$smarty->assign("target", args(1));
$smarty->assign("additionalArguments", $pathAddArgs);

?>