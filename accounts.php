<?php

/*
* accounts.php: Handles the 'accounts' subsystem of Tullok
*/

// Include header
require_once("resources/include/header.php");

// Determine what to do next based on the first path argument
switch (args(0)) {
 case "":
 case "login":
   $session = new Session();
   if ($session->loggedIn and !isset($_REQUEST['error'])) header("Location: $path"."databases");
   $session->logout();
   if (isset($_REQUEST['name'], $_REQUEST['pass'], $_REQUEST['host'])) {
     $session = new Session($_REQUEST['name'], $_REQUEST['pass'], $_REQUEST['host']);
     header("Location: $path"."databases");
   } else {
     if (isset($_REQUEST['error'])) {
       $smarty->assign("error", htmlentities(stripslashes($_REQUEST['error'])));
     }
     $smarty->display("login.html");
   }
   break;
 case "logout":
   $session->logout();
   header("Location: $path"."accounts/login");
}
