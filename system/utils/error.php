<?php

function isDebug() {
  return(isLocalhost());
}

function isThalasoft() {
  global $gSetupWebsiteName;

  if (strstr($gSetupWebsiteName, "thalasoft")) {
    return(true);
  } else  {
    return(false);
  }

}

function isTest() {
  global $gSetupWebsiteName;

  if (strstr($gSetupWebsiteName, "test.thalasoft")) {
    return(true);
  } else  {
    return(false);
  }
}

// Set a custom error handler
function errorHandler($errorType, $message, $filename, $line) {
  // The php error types
  $errorTypes = array (
    E_ERROR   =>  "Error",
    E_WARNING   =>  "Warning",
    E_PARSE   =>  "Parsing Error",
    E_NOTICE   =>  "Notice",
    E_CORE_ERROR  =>  "Core Error",
    E_CORE_WARNING  =>  "Core Warning",
    E_COMPILE_ERROR  =>  "Compile Error",
    E_COMPILE_WARNING =>  "Compile Warning",
    E_USER_ERROR =>  "User Error",
    E_USER_WARNING =>  "User Warning",
    E_USER_NOTICE =>  "User Notice"
  );

  // Subset of errors that are reported
  $userErrorTypes = array(E_ERROR, E_WARNING, E_PARSE);
  $systemErrorTypes = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_ERROR, E_WARNING, E_PARSE, E_NOTICE);

  if (!isDebug()) {
    // Display a maintenance message when the user is not the developer
    $reportedErrorTypes = $userErrorTypes;

    if (in_array($errorType, $reportedErrorTypes)) {
      $str = "A problem occured during the operation."
        . "\n\nIf the problem persists contact the system administrator.";
      printMessage(nl2br($str));

      reportError($message, $errorType, $filename, $line);
      exit;
    }
  } else {
    // Report even minor errors when the user is the developer
    $reportedErrorTypes = $systemErrorTypes;

    error_log("errorType: $errorType");
    if (in_array($errorType, $reportedErrorTypes)) {
      reportError($message, $errorType, $filename, $line);
    }
  }

  // Stop processing
  //  exit();
}

// Report a system error
function reportError($message, $errorType = '', $filename = '', $line = '') {
  global $HTTP_HOST;
  global $HTTP_USER_AGENT;
  global $REMOTE_ADDR;
  global $REQUEST_URI;

  if ($filename) {
    $fileName = basename($filename);
    $filePath = dirname($filename);
  } else {
    $fileName = '';
    $filePath = '';
  }

  $str = "Error message: $message\n";

  if (LibUtils::isCLI()) {
    if ($fileName) {
      $str .= "Filename: $filePath/$fileName\n"
        . "Line: $line\n";
    }
  } else {
    if ($fileName) {
      $str .= "Filename: $filePath/<span style='color:red; font-size:x-large;'>$fileName</span>\n"
        . "Line: <span style='color: red; font-size: xx-large'>$line</span>\n";
    }
  }

  $commonUtils = new CommonUtils();
  $ipUrl = $commonUtils->mapIP($REMOTE_ADDR);

  $str .= "Error type: $errorType\n"
    . "Client: $HTTP_USER_AGENT\n"
    . "Client IP: $REMOTE_ADDR\n"
    . "Location: <a href='$ipUrl' title=''>View on map</a>\n"
    . "Date: " . date("d-m-Y H:i:s\n", time())
    . "Host: $HTTP_HOST\n"
    . "Request: $REQUEST_URI\n";
  foreach ($_GET as $key => $entry) {
    $str .= "Get [$key]: $entry\n";
  }
  foreach ($_POST as $key => $entry) {
    $str .= "Post [$key]: $entry\n";
  }

  error_log($str);

  $str = nl2br($str);

  // Send an email to the system administrator to report about the error
  if (!isDebug()) {
    $email = STAFF_EMAIL;
    $emailSubject = "A PHP error occured on a website";
    $emailBody = nl2br($str);
    if (LibEmail::validate($email)) {
      LibEmail::sendMail($email, $email, $emailSubject, $emailBody);
    }
  } else {
    // Print the error message
    if (LibUtils::isCLI()) {
      print($str);
    } else {
      printMessage($str);
    }

    // Wait a few seconds
    exit();
  }
}

// Send an error message to the staff
function emailError($message) {
  if (!isDebug()) {
    return;
  }

  emailStaff($message);
}

// Send an email to the staff
function emailStaff($message) {
  $email = $adminUtils->staffEmail;
  if (LibEmail::validate($email)) {
    LibEmail::sendMail($email, $email, $message, $message, $email, $email);
  }
}

?>
