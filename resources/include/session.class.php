<?php

class Session {
  var $iv = "";
  var $loggedIn = 0;
  
  // Constructor
  function Session($sesPostUsername=false, $sesPostPassword=false, $sesPostHostname=false) {
    $this->loggedIn = 0;
    ini_set("session.gc_maxlifetime",259200); //set session lifetime to 3 days
    
    session_name("tullokID");
    session_start();
    setcookie("tullokID", session_id(), time()+60*60*24*30, "/");
    
    if (!isset($_SESSION['sesUsername']) or !isset($_SESSION['sesPassword']) or !isset($_SESSION['sesHostname'])) {
      if ($sesPostUsername and $sesPostPassword and $sesPostHostname) {
	
	$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
	$this->iv = base64_encode(mcrypt_create_iv($ivSize, MCRYPT_RAND));
	
	$_SESSION['sesUsername'] = $this->encryptWrapper($sesPostUsername);
	$_SESSION['sesPassword'] = $this->encryptWrapper($sesPostPassword);
	$_SESSION['sesHostname'] = $this->encryptWrapper($sesPostHostname);
	$_SESSION['iv'] = $this->iv;
	$this->loggedIn = 1;
      }
    } else {
      $this->iv = $_SESSION['iv'];
      $this->loggedIn = 1;
    }
  }
  
  function username() {
    return $this->decryptWrapper($_SESSION['sesUsername']);
  }
  
  function password() {
    return $this->decryptWrapper($_SESSION['sesPassword']);
  }
  
  function hostname() {
    return $this->decryptWrapper($_SESSION['sesHostname']);
  }
  
  function logout() {
    return session_destroy();
  }
  
  // Encryption wrappers
  function encryptWrapper($string) {
    global $encryptKey;
    $string = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $encryptKey, $string, MCRYPT_MODE_CBC, base64_decode($this->iv));
    return base64_encode($string);
  }
  
  function decryptWrapper($string){
    global $encryptKey;
    $string = base64_decode($string);
    $string = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $encryptKey, $string, MCRYPT_MODE_CBC, base64_decode($this->iv));
    return trim($string);
  }
}

?>