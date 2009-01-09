<?php

/*
* users.php: Handles the 'users' subsystem of Tullok
*/

// Include header
require_once("resources/include/header.php");
require_once("resources/include/users.class.php");

// Thickbox variable
$thickbox = isset($pathAddArgs['thickbox']) ? true : false;
$smarty->assign("thickbox", $thickbox);

// Select template from mode
switch (args(0)) {
 case "":
   $allUsers = User::all();
   $smarty->assign("users", $allUsers);
   $smarty->display("users.html");
   break;
}
