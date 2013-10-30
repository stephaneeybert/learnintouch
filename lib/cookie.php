<?php

// Cookies have a path and are only visible from that path
// To set a cookie visible to the entire web site set its path to '/'
// To avoid cookie over messing between different web sites it is recommended
// to include some web site specific name or initials in the names of its cookies

class LibCookie {

  // Set a cookie
  // The valid duration of the cookie is expressed in minutes
  static function putCookie($name, $value, $valid) {
    if (!$name) {
      return;
    }

    setcookie($name, $value, time() + $valid, '/');
  }

  // Get a cookie's value
  static function getCookie($name) {
    if (!$name) {
      return;
    }

    $value = LibEnv::getEnvCookie($name);

    if ($value != null && strlen($value) > 0) {
      return($value);
    } else {
      return("");
    }
  }

  // Delete a cookie
  static function deleteCookie($name) {
    if (!$name) {
      return;
    }

    unset($_COOKIE[$name]);

    setcookie($name, '' , time() - 3600, '/', '', 0);
  }

}

?>
