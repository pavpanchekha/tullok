<?php

try {
    require_once("/usr/share/php/smarty/Smarty.class.php");
} catch (Exception $e) {
    require_once("smarty/Smarty.class.php");
}

$fixpath = dirname(__FILE__)."/../..";
$smarty = new Smarty;
$smarty->template_dir = "$fixpath/resources/templates";
$smarty->compile_dir = "$fixpath/cache/Smarty/compile";
$smarty->cache_dir = "$fixpath/cache/Smarty/cache";
$smarty->config_dir = "$fixpath/resources/secure";
$smarty->config_load("config.ini", "Theme");
$smarty->config_load("config.ini", "Libraries");
$smarty->config_load("config.ini", "Logging");
?>