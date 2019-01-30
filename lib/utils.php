<?php

class LibUtils {

  // Shuffle an associative array an keep the keys
  static function shuffleArray($list) {
    if (!is_array($list)) {
      return($list);
    }

    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)  {
      $random[$key] = $list[$key];
    }

    return($random);
  }

  // Search for a sub string in an array
  // Return the key of the first found occurence
  static function searchArraySubstring($substring, $wArray) {
    $lineNumbers = array();

    for ($i = 0; $i < count($wArray); $i++) {
      if (strstr($wArray[$i], $substring)) {
        array_push($lineNumbers, $i);
      }
    }

    return($lineNumbers);
  }

  // Return a value from an array
  static function getArrayValue($key, $wArray) {
    $value = '';

    if (is_array($wArray)) {
      if (array_key_exists($key, $wArray)) {
        $value = $wArray[$key];
      }
    }

    return($value);
  }

  // Delete a value from an array
  static function deleteValue($key, $wArray) {
    if (is_array($wArray)) {
      if (array_key_exists($key, $wArray)) {
        // Delete the value
        unset($wArray[$key]);
        // Reshuffle the array to plug the hole
        $wArray= array_values($wArray);
      }
    }

    return($wArray);
  }

  // Merge two arrays without renumbering their keys
  static function arrayMerge($array1, $array2) {
    $result = array();
    $result = $array1 + $array2;

    return($result);
  }

  // $myarr = array("hello", "hey", "whatup", "goodbye", "see ya");
  // print_r(extractArray($myarr, 4, 3, 1));
  // Return an array from another array
  // Extract only the specified columns from the source array
  static function extractArray() {
    $return_arr = array();
    $num_args = func_num_args();

    if ($num_args <= 1) {
      return($return_arr);
    }

    $arg_list = func_get_args();
    $arr = array_shift($arg_list);

    foreach ($arg_list as $index) {
      if (isset($arr[$index])) {
        $return_arr[] = $arr[$index];
      }
    }

    return($return_arr);
  }

  // Check if a url is valid
  static function isInvalidUrl($url) {
    // Ignore the case
    $url = strtolower($url);

    if (!$url) {
      return(true);
    }

/*
    // Lower case
    $url = strtolower($url);

    $protocol = 'http://';

    // Remove the protocol if any
    if (strstr($url, $protocol)) {
      $url = substr($url, 7, strlen($url) - 7);
      }
 */

    return(false);
  }

/*
  // Get the relative path from an absolute one
  static function getRelativePath($path) {
    global $DOCUMENT_ROOT;

    if (!$path) {
      return;
      }

    // Remove the document root
    $path = substr($path, strlen($DOCUMENT_ROOT), strlen($path) - strlen($DOCUMENT_ROOT));

    return($path);
    }
 */

  // Get the relative url from an absolute one
  static function getRelativeUrl($url) {
    global $HOSTNAME;

    if (!$url) {
      return;
    }

    // Remove the protocol if any
    $protocol = 'http://';
    $url = str_replace($protocol, '', $url);
    $protocol = 'https://';
    $url = str_replace($protocol, '', $url);

    // Remove the domain name
    if (strstr($url, $HOSTNAME)) {
      $url = substr($url, strlen($HOSTNAME), strlen($url) - strlen($HOSTNAME));
    }

    return($url);
  }

  // Add a parameter and its value to a url
  static function addUrlParameter($url, $name, $value) {
    $url = LibString::stripTraillingSlash($url);

    $separator = "?";
    if (strpos($url, "?") !== false) {
      $separator = "&";
    }

    $insertPosition = strlen($url); 
    if (strpos($url, "#") !== false) {
      $insertPosition = strpos($url,"#");
    }

    $newUrl = substr_replace($url, "$separator$name=$value", $insertPosition, 0);

    return($newUrl);
  }

  // Get the parameters of a url
  static function getUrlParameters($url) {
    $parsedUrl = parse_url($url);
    if (isset($parsedUrl['query'])) {
      parse_str($parsedUrl['query'], $parameters);

      return($parameters);
    }
  }

  // Check if a url is relative
  static function isRelativeUrl($url) {
    $isRelative = false;

    if (!strstr($url, 'http://') && !strstr($url, 'https://') && !strstr($url, 'www') && ($url == LibUtils::getRelativeUrl($url))) {
      $isRelative = true;
    }

    return($isRelative);
  }

  // Check if a url is mailto url
  static function isMailtoUrl($url) {
    $isMailto = false;

    if (strstr($url, "mailto")) {
      $isMailto = true;
    }

    return($isMailto);
  }

  // Get the website protocol
  static function getProtocol() {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
      $protocol = 'https';
    } else {
      $protocol = 'http';
    }
    return($protocol);
  }

  // Format the url
  static function formatUrl($url) {
    if (!$url) {
      return;
    }

    $url = LibUtils::normalizeUrl($url);
    $protocol = LibUtils::getProtocol();
    $subDomain = '';

    // Remove the protocol if any
    if (strstr($url, $protocol)) {
      $url = substr($url, 7, strlen($url) - 7);
    }

    // Get a specific sub domain if any
    if (strstr($url, "/")) {
      $bits = explode("/", "$url");
      $baseUrl = $bits[0];
    } else {
      $baseUrl = $url;
    }
    $bits = explode(".", "$baseUrl");
    if (count($bits) > 2) {
      $subDomain = $bits[0] . ".";
    }

    // Remove the sub domain if any
    if ($subDomain && strstr($url, $subDomain)) {
      $url = substr($url, strlen($subDomain), strlen($url) - strlen($subDomain));
    }

    // Add the protocol and the sub domain
    $url = $protocol . $subDomain . $url;

    return($url);
  }

/*
  // Check for a non NULL value and type
  static function isNotNull($value) {
    return(!LibUtils::isNull($value));
    }

  // Check for a NULL value and type
  static function isNull($value) {
    // Note the 3 equal signs in the following test
    // It is to compare the 2 values AND their type
    // Otherwise, a value of 0 (zero) would be deemed a null value with a 2 equal signs test
    if ($value === NULL) {
      return(true);
      } else {
      return(false);
      }
    }

  // Check for a NULL value only
  static function isNullValue($value) {
    // Note the 2 equal signs in the following test
    // It is to compare the 2 values but NOT their type
    if ($value == NULL) {
      return(true);
      } else {
      return(false);
      }
    }

  // Check for a set value
  static function isSetValue($value) {
    return(isset($value));
    }

  // Delete the content of a variable
  static function delete($var) {
    unset($var);
    $var = NULL;
    }
 */

  // Check if the php is used from the command line interface
  static function isCLI() {
    $isCLI = (php_sapi_name() == 'cli');

    return($isCLI);
  }

  // Generate a unique random id
  static function generateUniqueId($length = 4) {
    $id = md5(uniqid(rand(), 1));

    $id = substr($id, 0, $length);

    return($id);
  }

  // Send a get curl request
  function sendGetCurlRequest($url, $port = 80, $headers = NULL) {
    $retarr = array();

    $curlOptions = array(
      CURLOPT_URL => $url,
      CURLOPT_PORT => $port,
      CURLOPT_POST => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_RETURNTRANSFER => true
    );

    if ($headers) {
      $curlOptions[CURLOPT_HTTPHEADER] = $headers;
    }

    $response = LibUtils::sendCurlRequest($curlOptions);

    if (!empty($response)) {
      $retarr = $response;
    }

    return($retarr);
  }

  // Send a post curl request
  function sendPostCurlRequest($url, $postbody, $port = 80, $headers = NULL) {
    $retarr = array();

    $curlOptions = array(
      CURLOPT_URL => $url,
      CURLOPT_PORT => $port,
      CURLOPT_POST => true,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_POSTFIELDS => $postbody,
      CURLOPT_RETURNTRANSFER => true
    );

    if ($headers) {
      $curlOptions[CURLOPT_HTTPHEADER] = $headers;
    }

    $response = LibUtils::sendCurlRequest($curlOptions);

    if (!empty($response)) {
      $retarr = $response;
    }

    return($retarr);
  }

  // Send an http request with curl
  function sendCurlRequest($curlOptions) {
    $retarr = array();

    if (!$curlOptions) {
      return($retarr);
    }

    $ch = curl_init();
    if (!$ch) {
      return($retarr);
    }

    foreach ($curlOptions as $name => $value) {
      curl_setopt($ch, $name, $value);
    }

    curl_setopt($ch, CURLOPT_HEADER, true);

    ob_start();
    $response = curl_exec($ch);
    $curl_spew = ob_get_contents();
    ob_end_clean();

    if (curl_errno($ch)) {
      $errno = curl_errno($ch);
      $errmsg = curl_error($ch);
      curl_close($ch);
      unset($ch);
      return($retarr);
    }

    $info = curl_getinfo($ch);

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize );

    curl_close($ch);
    unset($ch);

    array_push($retarr, $info, $header, $body);

    return($retarr);
  }

  // Get the normalized signature base string of the request
  function signatureBaseString($httpMethod, $url, $params) {
    // Decompose and pull query params out of the url
    $queryStr = parse_url($url, PHP_URL_QUERY);
    if ($queryStr) {
      $parsedQuery = LibUtils::queryStringToArray($queryStr);
      // merge params from the url with params array from caller
      $params = array_merge($params, $parsedQuery);
    }

    // Remove oauth_signature from params array if present
    if (isset($params['oauth_signature'])) {
      unset($params['oauth_signature']);
    }

    // Create the signature base string. Yes, the $params are double encoded.
    $baseString = LibUtils::rfc3986Encode(strtoupper($httpMethod)) . '&' .
      LibUtils::rfc3986Encode(LibUtils::normalizeUrl($url)) . '&' .
      LibUtils::rfc3986Encode(LibUtils::oauthHttpBuildQuery($params));

    return($baseString);
  }

  // Make the URL conform to the format scheme://host/path
  function normalizeUrl($url) {
    $bits = parse_url($url);

    $scheme = $bits['scheme'];
    $host = $bits['host'];
    $port = $bits['port'];
    $path = $bits['path'];

    if (!$port) {
      $port = ($scheme == 'https') ? '443' : '80';
    }

    if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
      $host = "$host:$port";
    }

    return("$scheme://$host$path");
  }

  // Encode input per RFC 3986
  function rfc3986Encode($rawInput) {
    if (is_array($rawInput)) {
      return(array_map('LibUtils::rfc3986Encode', $rawInput));
    } else if (is_scalar($rawInput)) {
      return(str_replace('%7E', '~', rawurlencode($rawInput)));
    } else {
      return('');
    }
  }

  // Decode input per RFC 3986
  function rfc3986Decode($rawInput) {
    return(rawurldecode($rawInput));
  }

  // Build a query parameter string according to OAuth 
  function oauthHttpBuildQuery($params, $excludeOauthParams = false) {
    $queryString = '';
    if (!empty($params)) {
      $keys = LibUtils::rfc3986Encode(array_keys($params));
      $values = LibUtils::rfc3986Encode(array_values($params));
      $params = array_combine($keys, $values);

      // Parameters are sorted by name, using lexicographical byte value ordering.
      // http://oauth.net/core/1.0/#rfc.section.9.1.1
      uksort($params, 'strcmp');

      // Turn params array into an array of "key=value" strings
      $kvpairs = array();
      foreach ($params as $k => $v) {
        if ($excludeOauthParams && substr($k, 0, 5) == 'oauth') {
          continue;
        }
        if (is_array($v)) {
          // If two or more parameters share the same name,
          // they are sorted by their value. OAuth Spec: 9.1.1 (1)
          natsort($v);
          foreach ($v as $value_for_same_key) {
            array_push($kvpairs, ($k . '=' . $value_for_same_key));
          }
        } else {
          // For each parameter, the name is separated from the corresponding
          // value by an '=' character (ASCII code 61). OAuth Spec: 9.1.1 (2)
          array_push($kvpairs, ($k . '=' . $v));
        }
      }

      // Each name-value pair is separated by an '&' character, ASCII code 38.
      // OAuth Spec: 9.1.1 (2)
      $queryString = implode('&', $kvpairs);
    }

    return($queryString);
  }

  // Parse a query string into an array
  function queryStringToArray($queryString) {
    $queryArray = array();

    if (isset($queryString)) {
      // Separate single string into an array of "key=value" strings
      $kvpairs = explode('&', $queryString);

      // Separate each "key=value" string into an array[key] = value
      foreach ($kvpairs as $pair) {
        $k = array();
        if (strstr($pair, '=')) {
          list($k, $v) = explode('=', $pair, 2);
        }

        // Handle the case where multiple values map to the same key
        // by pulling those values into an array themselves
        if ($k) {
          if (isset($queryArray[$k])) {
            // If the existing value is a scalar, turn it into an array
            if (is_scalar($queryArray[$k])) {
              $queryArray[$k] = array($queryArray[$k]);
            }
            array_push($queryArray[$k], $v);
          } else {
            $queryArray[$k] = $v;
          }
        }
      }
    }

    return($queryArray);
  }

}

?>
