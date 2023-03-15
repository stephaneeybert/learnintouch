<?php

class SmsGatewayUtils {

  // The gateway provider
  var $gateway;

  // Simulation flag (yes/no)
  var $isSimulation;

  // The account user name
  var $accountUser;

  // The account user password
  var $accountaccountPassword;

  // Default international phone code
  var $defaultPrefix;

  // The sender identifies the sender of the SMS message
  var $senderName;

  var $preferenceUtils;
  var $profileUtils;

  function __construct() {
    $this->init();
  }

  function init() {
    // Simulation flag (yes/no)
    $this->isSimulation = 'no';

    $this->routeTM4B = 'GD02'; // For Business or GD01 for First class
  }

  function loadProperties() {
    $this->gateway = $this->preferenceUtils->getValue("SMS_GATEWAY");
    $this->accountUser = trim($this->preferenceUtils->getValue("SMS_ACCOUNT_USER"));
    $this->accountPassword = trim($this->preferenceUtils->getValue("SMS_ACCOUNT_PASSWORD"));
    $this->defaultPrefix = $this->preferenceUtils->getValue("SMS_DEFAULT_PREFIX");
    $this->senderName = $this->preferenceUtils->getValue("SMS_SENDER_NAME");
    $this->defaultPhoneNumber = $this->preferenceUtils->getValue("SMS_DEFAULT_PHONE_NUMBER");
  }

  // Clean up a phone number
  function cleanUpNumber($number) {

    // Remove the + prefix if any
    if (substr($number, 0, 1) == '+') {
      $number = substr($number, 1, strlen($number) - 1);
    } else if (substr($number, 0, 2) == '00') {
      // Remove the 00 prefix if any
      $number = substr($number, 2, strlen($number) - 2);
    } else if (substr($number, 0, 1) == '0') {
      // Add the default prefix if the phone number is a country wise number
      $number = $this->defaultPrefix . substr($number, 1, strlen($number) - 1);
    }

    return($number);
  }

  // Clean up the name of the sender
  function cleanUpSenderName($name) {

    if (is_numeric($name)) {
      if (strlen($name) > 16) {
        $name = substr($name, 0, 16);
      } else if (strlen($name) < 8) {
        $name = str_pad($name, 8, '0');
      }
    } else {
      if (strlen($name) > 11) {
        $name = substr($name, 0, 11);
      }
    }

    return($name);
  }

  // Send an SMS message
  function sendSMS($message, $mobilePhoneNumbers) {

    if (!$message) {
      return(false);
    }

    if ($this->gateway == SMS_GATEWAY_TM4B) {
      $result = $this->sendSMSTM4B($message, $mobilePhoneNumbers);
    }

    return($result);
  }

  // Send an SMS message via the TM4B gateway provider
  function sendSMSTM4B($message, $mobilePhoneNumbers) {

    // Create the numbers string
    if (is_array($mobilePhoneNumbers)) {
      $strMobilePhoneNumbers = '';
      foreach ($mobilePhoneNumbers as $mobilePhoneNumber) {
        $mobilePhoneNumber = $this->cleanUpNumber($mobilePhoneNumber);
        $strMobilePhoneNumbers = '|' . $mobilePhoneNumber;
      }
    } else {
      $strMobilePhoneNumbers = $this->cleanUpNumber($mobilePhoneNumbers);
    }

    // Methods to access the gateway
    $accessMethods = array(
        'curl' => 0,
        'sockets' => 1,
        'file_get_contents' => 0,
        );

    $websiteEmail = $this->profileUtils->getProfileValue("website.email");

    // Set the access details
    $param = array();
    $param['msg'] = $message;
    $param['to'] = $strMobilePhoneNumbers;
    $param['username'] = $this->accountUser;
    $param['password'] = $this->accountPassword;

    $param['type'] = 'broadcast';

    // Sender ('20170000' or 'tm4b.com' or custom)
    $senderName = $this->senderName;
    if (!$senderName) {
      $senderName = '20170000';
    } else {
      $senderName = $this->cleanUpSenderName($senderName);
    }
    $param['from'] = $senderName;

    // The reply email is used to receive the response when requesting the account balance
    $param['reply_email'] = $websiteEmail;

    // The name of the variable returning the response
    $param['rrmid'] = 'balance';

    // To send to France the business class is required
    $param['route'] = $this->routeTM4B;

    $param['sim'] = $this->isSimulation;

    $accessRequest = '';
    foreach($param as $key => $value) {
      if ($key) {
        $accessRequest .= $key . '=' . urlencode($value);
        $accessRequest .= '&';
      }
    }

    if ($accessMethods['curl'] == 1) {

      // Set the gateway url
      $url = "http://www.tm4b.com/client/api/http.php";

      // Init curl
      $ch = curl_init();

      // The parameters
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return a variable instead of displaying it
      curl_setopt($ch, CURLOPT_POST, 1); // Active the POST method
      curl_setopt($ch, CURLOPT_POSTFIELDS, $accessRequest);

      // Connect to CURL
      $response = curl_exec($ch);

      // Close the connection
      curl_close($ch);

    } else if ($accessMethods['sockets'] == 1) {

      $response = $this->socketRequestTM4B($accessRequest);

    } else if ($accessMethods['file_get_contents'] == 1) {

      // Set the gateway url
      $url = "http://www.tm4b.com/client/api/http.php";

      // Connect
      $response = file_get_contents($url . '?' . $accessRequest);

    }

    return($response);
  }

  // Send a request to the TM4B gateway provider
  function socketRequestTM4B($accessRequest) {

    // Set the gateway details
    $host = "tm4b.com";
    $script = "/client/api/http.php";
    $requestLength = strlen($accessRequest);
    $method = "POST"; // POST to send several messages
    if ($method == "GET") {
      $script .= '?' . $accessRequest;
    }

    $response = $this->socketRequest($host, $script, $requestLength, $method, $accessRequest);

    return($response);
  }

  // Send a request through a socket
  function socketRequest($host, $script, $requestLength, $method, $accessRequest) {
    // Set the header
    $header  = $method . " " . $script . " HTTP/1.1\r\n";
    $header .= "Host: " . $host . "\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . $requestLength . "\r\n";
    $header .= "Connection: close\r\n\r\n";
    $header .= $accessRequest . "\r\n";

    // Open the connection
    $socket = fsockopen($host, 80, $errno, $errstr);

    if ($socket) {
      fputs($socket, $header); // Send the header
      while(!feof($socket)) {
        $response[] = fgets($socket); // Retrieve the results
      }

      fclose($socket);
    } else {
      $response = array();
    }

    return($response);
  }

  // Check the balance of the account
  // The balance is expressed in credits
  function checkBalance() {
    $balance = 0;

    if ($this->gateway == SMS_GATEWAY_TM4B) {
      $balance = $this->checkBalanceTM4B();
    }

    return($balance);
  }

  // Check the balance of the account for the TM4B gateway provider
  // The balance is in the field 8 of response and is expressed in credits
  function checkBalanceTM4B() {

    $param = array();
    $param['username'] = $this->accountUser;
    $param['password'] = $this->accountPassword;

    $param['version'] = '2.1';
    $param['type'] = 'check_balance';

    $accessRequest = '';
    foreach($param as $key => $value) {
      if ($key) {
        if ($accessRequest) {
          $accessRequest .= '&';
        }
        $accessRequest .= $key . '=' . urlencode($value);
      }
    }

    $response = $this->socketRequestTM4B($accessRequest);

    if (count($response) > 8) {
      $balance = $response[8];
      $balance = LibString::stripTags($balance);
    } else {
      $balance = '';
    }

    return($balance);
  }

  // Get the cost per country for the route chosen
  function getRouteCosts() {
    if ($this->gateway == SMS_GATEWAY_TM4B) {
      $routeCosts = $this->getRouteCostsTM4B();

      return($routeCosts);
    }
  }

  // Get the cost per country for the route chosen for the TM4B gateway provider
  function getRouteCostsTM4B() {
    $routeCosts = array();

    $fileUrl = SMS_TM4B_RATES_FILE_URL;
    $separator = SMS_TM4B_RATES_FILE_SEPARATOR;

    $content = LibFile::curlGetFileContent($fileUrl);
    $lines = explode("\n", $content);
    foreach ($lines as $line) {
      $fields = explode($separator, $line);
      if (count($fields) > 0) {
        $countryCode = $fields[0];
        $routeCost = $fields[4];
        // Remove the currency sign
        $routeCost = substr($routeCost, 1, strlen($routeCost) - 1);
        $routeCosts[$countryCode] = $routeCost;
      }
    }

    return($routeCosts);
  }

  // Check cost per sms for the route chosen for the TM4B gateway provider
  // The cost per sms is in the field 3 of response and is expressed in credits
  function NOT_USED_old_and_deprecated_checkRouteCostTM4B() {

    $param = array();
    $param['username'] = $this->accountUser;
    $param['password'] = $this->accountPassword;

    $param['type'] = 'check_destination';

    $defaultPhoneNumber = $this->cleanUpNumber($this->defaultPhoneNumber);

    $param['dest'] = $defaultPhoneNumber;

    $param['route'] = $this->routeTM4B;

    $accessRequest = '';
    foreach($param as $key => $value) {
      if ($key) {
        $accessRequest .= $key . '=' . urlencode($value);
        $accessRequest .= '&';
      }
    }

    $response = $this->socketRequestTM4B($accessRequest);

    $cost = '';
    if (count($response) > 8) {
      $strCost = $response[8];
      $costData = explode('|', $strCost);
      if (count($costData) > 2) {
        $cost = $costData[2];
      }
    }

    return($cost);
  }

}

?>
