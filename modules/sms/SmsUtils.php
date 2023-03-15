<?

class SmsUtils extends SmsDB {

  var $mlText;
  var $websiteText;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $smsHistoryUtils;

  function __construct() {
    parent::__construct();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function getMetaNames() {
    $this->loadLanguageTexts();

    $metaNames = array(
      array(SMS_META_USER_FIRSTNAME, 'firstname', $this->mlText[1]),
      array(SMS_META_USER_LASTNAME, 'lastname', $this->mlText[2]),
      array(SMS_META_USER_MOBILE_PHONE, 'mobilePhone', $this->mlText[3]),
      array(SMS_META_USER_PASSWORD, 'password', $this->mlText[17]),
    );

    return($metaNames);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $defaultTitle = $this->mlText[25];
    $defaultDescription = $this->mlText[26];
    $defaultAcknowledge = $this->mlText[28];

    $this->preferences = array(
      "SMS_GATEWAY" =>
      array($this->mlText[12], $this->mlText[14], PREFERENCE_TYPE_SELECT,
        array(SMS_GATEWAY_TM4B => "tm4b.com")),
      "SMS_ACCOUNT_USER" =>
      array($this->mlText[15], $this->mlText[18], PREFERENCE_TYPE_TEXT, ''),
        "SMS_ACCOUNT_PASSWORD" =>
        array($this->mlText[19], $this->mlText[20], PREFERENCE_TYPE_TEXT, ''),
          "SMS_DEFAULT_PHONE_NUMBER" =>
          array($this->mlText[30], $this->mlText[31], PREFERENCE_TYPE_TEXT, ''),
            "SMS_DEFAULT_PREFIX" =>
            array($this->mlText[21], $this->mlText[22], PREFERENCE_TYPE_TEXT, SMS_FRANCE_PREFIX),
              "SMS_SENDER_NAME" =>
              array($this->mlText[23], $this->mlText[24], PREFERENCE_TYPE_TEXT, ''),
                "SMS_DISPLAY_SIGNATURE" =>
                array($this->mlText[11], $this->mlText[51], PREFERENCE_TYPE_BOOLEAN, ''),
                  "SMS_LIST_STEP" =>
                  array($this->mlText[8], $this->mlText[4], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                    "SMS_REGISTER_TITLE" =>
                    array($this->mlText[5], $this->mlText[6], PREFERENCE_TYPE_MLTEXT, $defaultTitle),
                      "SMS_REGISTER_DESCRIPTION" =>
                      array($this->mlText[7], $this->mlText[6], PREFERENCE_TYPE_MLTEXT, $defaultDescription),
                        "SMS_SIGNATURE" =>
                        array($this->mlText[10], $this->mlText[50], PREFERENCE_TYPE_MLTEXT, ''),
                          "SMS_SUBSCRIPTION_ACKNOWLEDGE" =>
                          array($this->mlText[9], $this->mlText[13], PREFERENCE_TYPE_MLTEXT, $defaultAcknowledge),
                          );

    $this->preferenceUtils->init($this->preferences);
  }

  // Render the body
  function renderBody($sms) {
    global $gHomeUrl;

    $body = $sms->getBody();

    // Render the signature
    $signature = $this->preferenceUtils->getValue("SMS_SIGNATURE");
    $displaySignature = $this->preferenceUtils->getValue("SMS_DISPLAY_SIGNATURE");
    $signature = nl2br($signature);

    $str = $body;

    if ($signature && $displaySignature) {
      $str .= " " . $signature;
    }

    $str = $this->cleanUpMessage($str);

    return($str);
  }

  // Clean up message
  function cleanUpMessage($message) {
    // Remove all pipe characters if any
    // The pipe character is used to build an SMS message request
    // and cannot be used within the message
    if (strstr($message, '|')) {
      $message = str_replace('|', '', $message);
    }

    // Keep only the first 160 characters
    // And SMS message size is limited
    if (strlen($message) > SMS_MESSAGE_LENGTH) {
      $message = substr($message, 0, SMS_MESSAGE_LENGTH);
    }

    return($message);
  }

  function deleteSms($smsId) {
    if ($smsHistories = $this->smsHistoryUtils->selectBySmsId($smsId)) {
      foreach ($smsHistories as $smsHistory) {
        $smsHistoryId = $smsHistory->getId();
        $this->smsHistoryUtils->delete($smsHistoryId);
      }
    }

    $this->delete($smsId);
  }

}

?>
