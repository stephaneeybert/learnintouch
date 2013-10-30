<?php

class LibSession {

  // Open a new session or get the current one if any
  // To check if a session exists, do not rely on the session id,
  // but rather on a session variable
  static function openSession() {
    // Using sessions might prevent a browser from caching the form input fields content
    // Set the session cache limiter to work around that issue
    session_cache_limiter('nocache');

    if (!session_id()) {
      session_start();
    }
  }

  // Close a session
  static function closeSession() {
    LibCookie::deleteCookie(session_name());

    if (session_id()) {
      // Destroy all the session variables
      $_SESSION = array();

      // Destroy the session
      session_destroy();
    }
  }

  // Put a session value
  // All session variables have a unique name, distinct from any other variables
  // The session variable names should start with the word "session..."
  static function putSessionValue($name, $value) {
    $_SESSION["$name"] = $value;
  }

  // Get a session value
  static function getSessionValue($name) {
    $value = '';

    if (isset($_SESSION)) {
      if (array_key_exists($name, $_SESSION)) {
        $value = $_SESSION["$name"];
      }
    }

    return($value);
  }

  // Delete a session value
  // Note : It is necessary to erase the content of the global variable
  static function delSessionValue($name) {
    LibSession::putSessionValue($name, '');
    unset($_SESSION["$name"]);
  }

  // Check if a value is in a session
  static function isSessionValueRegistered($name) {
    if (isset($_SESSION) && array_key_exists($name, $_SESSION)) {
      $value = $_SESSION["$name"];
    } else {
      $value = '';
    }

    if ($value) {
      return(true);
    } else {
      return(false);
    }
  }

  // Print all session variables
  static function printSessionVariables() {
    foreach ($_SESSION as $name => $value1) {
      $value2 = LibSession::getSessionValue($name);
      $str = "_SESSION[\"$name\"] = $value1 ( $value2 )";
      print($str);
    }
  }

  // Check the validity of a session
  static function checkSession($sessionAccessTime, $timePeriod) {
    $result = false;

    // Get a hold of the current session
    LibSession::openSession();

    // Check if the session value is registered
    if (LibSession::isSessionValueRegistered($sessionAccessTime)) {
      // Check the session timeout
      $lastAccessStr = LibSession::getSessionValue($sessionAccessTime);
      if ($lastAccessStr) {
        $lastAccess = $lastAccessStr;
      } else {
        $lastAccess = time();
      }
      // Express in minutes
      $timePeriod = $timePeriod * 60;
      $timeElapsed = time() - $lastAccess;
      if ($timeElapsed - $timePeriod > 0) {
        // Note that the access date must be deleted if the session times out
        LibSession::delSessionValue($sessionAccessTime);
      } else {
        // Update the last access time of the session
        LibSession::putSessionValue($sessionAccessTime, time());
        $result = true;
      }
    }

    return($result);
  }

}

?>
