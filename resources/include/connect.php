<?php

// Session
$dbHostname = $session->hostname();
$dbUsername = $session->username();
$dbPassword = $session->password();
$conn = @mysql_connect($dbHostname, $dbUsername, $dbPassword) or header("Location: {$path}accounts/login?error=" . mysql_error());
unset($dbHostname, $dbUsername, $dbPassword); // We don't want them hanging around in memory!

?>