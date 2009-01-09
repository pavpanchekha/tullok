<?php

/*
* install.php: Handles the initial configuration of Tullok.
*/

error_reporting(E_ALL);

function chmod_R($path, $filemode) {
  if (!is_dir($path)) {
    return chmod($path, $filemode);
  }
  $dh = opendir($path);
  while ($file = readdir($dh)) {
    if($file != '.' && $file != '..') {
      $fullpath = $path.'/'.$file;
      if(!is_dir($fullpath)) {
	if (!chmod($fullpath, $filemode)) {
	  return FALSE;
	}
      }
      else {		
	if (!chmod_R($fullpath, $filemode)) {
	  return FALSE;
	}
      }
    }
  }
  closedir($dh);
  if (chmod($path, $filemode)) {
    return true;
  } else {
    return false;
  }
}

$iniPath = "resources/secure/config.ini";
if (file_exists($iniPath))
  header("Location: databases.php");

// Make ini file
$iniInfo = "[Theme]\n";
$iniInfo .= "; Theme to use. Themes are located in resources/styles/themes.\n";
$iniInfo .= "theme=blue\n\n";

$iniInfo .= "[Libraries]\n";
$iniInfo .= "; Whether to use Google's hosting for Ajax libraries\n";
$iniInfo .= "google=\"true\"\n\n";

$iniInfo .= "[.Crypto]\n";
$iniInfo .= "; Blowfish cryptographic key. Randomly generated on install.\n";
$iniInfo .= "blowfish=" . sha1(rand(1,1000000000)) . "\n\n";

$iniInfo .= "[Logging]\n";
$iniInfo .= "; Where log is located. Should be relative to tullok root or absolute.\n";
$iniInfo .= "; Must end in /\n";
$iniInfo .= "log=\"logs/\"";
$iniInfo .= "; Whether to log load times\n";
$iniInfo .= "log_load=\"false\"\n\n";

$fh = fopen($iniPath, 'w');
fwrite($fh, $iniInfo);
fclose($fh);

// Make /cache/Smarty/compile and /cache/Smarty/cache (PHP defaults to 0777)
@mkdir("cache/");
@mkdir("cache/Smarty/");
@mkdir("cache/Smarty/compile/");
@mkdir("cache/Smarty/cache/");

chmod_R("cache", 0770);
chmod_R("resources/secure", 0770);

// Done!
header("Location: databases.php");
