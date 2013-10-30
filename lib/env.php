<?php

class LibEnv {

  // Return the value of an HTTP GET environment variable
  static function getEnvHttpGET($var) {
    $value = LibUtils::getArrayValue($var, $_GET);

    return($value);
  }

  // Return the value of an HTTP POST environment variable
  static function getEnvHttpPOST($var) {
    $value = LibUtils::getArrayValue($var, $_POST);

    return($value);
  }

  // Return the value of an HTTP FILE environment variable
  static function getEnvHttpFILE($filename) {
    // Note how the form parameter creates several variables
    $uploaded_file = Array();
    $uploaded_file['tmp_name'] = $_FILES["$filename"]['tmp_name'];
    $uploaded_file['name'] = $_FILES["$filename"]['name'];
    $uploaded_file['type'] = $_FILES["$filename"]['type'];
    $uploaded_file['size'] = $_FILES["$filename"]['size'];
    return($uploaded_file);
  }

  // Return the value of an HTTP cookie
  static function getEnvCookie($var) {
    $value = LibUtils::getArrayValue($var, $_COOKIE);

    return($value);
  }

  // Return the value of a SERVER environment variable
  static function getEnvSERVER($var) {
    $value = LibUtils::getArrayValue($var, $_SERVER);

    return($value);
  }

  // Return the value of a GLOBALS environment variable
  static function getEnvGLOBALS($var) {
    $value = LibUtils::getArrayValue($var, $GLOBALS);

    return($value);
  }

  // Return the value of an ENV environment variable
  static function getEnvENV($var) {
    $value = LibUtils::getArrayValue($var, $_ENV);

    return($value);
  }

}

?>
