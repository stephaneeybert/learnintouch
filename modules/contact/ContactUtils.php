<?

class ContactUtils extends ContactDB {

  var $mlText;
  var $websiteText;

  var $currentStatus;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $clockUtils;
  var $profileUtils;
  var $mailAddressUtils;
  var $contactStatusUtils;
  var $adminUtils;
  var $uniqueTokenUtils;
  var $contactRefererUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    $this->currentStatus = "contactCurrentStatus";
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "CONTACT_MESSAGE_DURATION" =>
      array($this->mlText[7], $this->mlText[8], PREFERENCE_TYPE_SELECT, array('' => " ", 1 => "1", 3 => "3", 6 => "6", 12 => "12")),
        "CONTACT_REGISTER_EMAIL" =>
        array($this->mlText[5], $this->mlText[6], PREFERENCE_TYPE_BOOLEAN, ''),
          "CONTACT_MAIL_ON_POST" =>
          array($this->mlText[1], $this->mlText[31], PREFERENCE_TYPE_BOOLEAN, ''),
            "CONTACT_INCLUDE_MESSAGE" =>
            array($this->mlText[9], $this->mlText[10], PREFERENCE_TYPE_BOOLEAN, ''),
              "CONTACT_NO_SECURITY_CODE" =>
              array($this->mlText[13], $this->mlText[14], PREFERENCE_TYPE_BOOLEAN, ''),
                "CONTACT_FORM_EMAIL_ADDRESS" =>
                array($this->mlText[15], $this->mlText[16], PREFERENCE_TYPE_TEXT, ''),
                  "CONTACT_LIST_STEP" =>
                  array($this->mlText[28], $this->mlText[29], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                    "CONTACT_ACKNOWLEDGEMENT_PAGE" =>
                    array($this->mlText[11], $this->mlText[12], PREFERENCE_TYPE_URL, ''),
                      "CONTACT_ACKNOWLEDGE" =>
                      array($this->mlText[2], $this->mlText[32], PREFERENCE_TYPE_MLTEXT, ''),
                        "CONTACT_REFERER_COMMENT" =>
                        array($this->mlText[19], $this->mlText[30], PREFERENCE_TYPE_MLTEXT, $this->mlText[33]),
                          "CONTACT_COMMENT" =>
                          array($this->mlText[3], $this->mlText[4], PREFERENCE_TYPE_MLTEXT, ''),
                          );

    $this->preferenceUtils->init($this->preferences);
  }

  // Move a message into the garbage bin
  function putInGarbage($contactId) {
    if ($contact = $this->selectById($contactId)) {
      $contact->setGarbage(true);
      $this->update($contact);
    }
  }

  // Restore a message from the garbage
  function restoreFromGarbage($contactId) {
    if ($contact = $this->selectById($contactId)) {
      $contact->setGarbage(false);

      $this->update($contact);
    }
  }

  // Delete the old messages
  function deleteOldMessages() {
    $duration = $this->preferenceUtils->getValue("CONTACT_MESSAGE_DURATION");

    if ($duration) {
      $systemDate = $this->clockUtils->getSystemDate();

      // Get the date since which to delete the messages
      $sinceDate = $this->clockUtils->incrementMonths($systemDate, -1 * $duration);

      if ($this->clockUtils->systemDateIsSet($sinceDate)) {
        $this->deleteByDate($sinceDate);
      }
    }
  }

  // Register the email address in the list of email addresses
  function registerEmailAddress($email) {
    $registerEmail = $this->preferenceUtils->getValue("CONTACT_REGISTER_EMAIL");
    if ($registerEmail) {
      if (!$mailAddress = $this->mailAddressUtils->selectByEmail($email)) {
        $mailAddress = new MailAddress();
        $mailAddress->setEmail($email);
        $mailAddress->setSubscribe(1);
        $this->mailAddressUtils->insert($mailAddress);
      }
    }
  }

  // Send an email when a contact message is received
  function registerMessage($email, $subject, $message, $firstname = '', $lastname = '', $organisation = '', $telephone = '', $contactRefererId = '') {
    global $gContactUrl;
    global $gJSNoStatus;

    $contact = new Contact();
    $contact->setFirstname($firstname);
    $contact->setLastname($lastname);
    $contact->setEmail($email);
    $contact->setOrganisation($organisation);
    $contact->setTelephone($telephone);
    $contact->setSubject($subject);
    $contact->setMessage($message);
    $contact->setContactDate($this->clockUtils->getSystemDateTime());
    $contact->setContactRefererId($contactRefererId);

    // Get the first status from the list order
    $statusId = 0;
    if ($status = $this->contactStatusUtils->selectFirst()) {
      $statusId = $status->getId();
    }
    $contact->setStatus($statusId);

    $this->insert($contact);
    $contactId = $this->getLastInsertId();

    $mailOnPost = $this->preferenceUtils->getValue("CONTACT_MAIL_ON_POST");
    if ($mailOnPost) {
      $websiteName = $this->profileUtils->getProfileValue("website.name");
      $siteEmail = $this->profileUtils->getProfileValue("website.email");

      if ($firstname || $lastname) {
        $strName = $firstname . ' ' . $lastname . ' ' . $email;
      } else {
        $strName = $email;
      }

      // Thus create a one-time url for the link in the email
      // Generate a unique token and keep it for later use
      $tokenName = CONTACT_TOKEN_NAME;
      $tokenDuration = $this->adminUtils->getLoginTokenDuration();
      $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

      $emailSubject = $this->websiteText[22] . ' ' . $strName . ' ' . $this->websiteText[21] . ' ' . $websiteName;

      $emailBody = $this->websiteText[23] . ' ' . $websiteName . ' ' . $this->websiteText[24] . ' ' . $strName . "<br /><br />";

      $includeMessage = $this->preferenceUtils->getValue("CONTACT_INCLUDE_MESSAGE");
      if ($includeMessage) {
        $emailBody .= $this->websiteText[27] . ' ' . nl2br($message);
      }

      if ($contactReferer = $this->contactRefererUtils->selectById($contactRefererId)) {
        $description = $contactReferer->getDescription();
        $languageCode = $this->languageUtils->getCurrentLanguageCode();
        $description = $this->languageUtils->getTextForLanguage($description, $languageCode);

        $emailBody .= '<br /><br />' . $this->websiteText[0] . ' ' . $description;
      }

      $emailBody .= "<br /><br /><a href='$gContactUrl/read.php?contactId=$contactId&tokenName=$tokenName&tokenValue=$tokenValue&siteEmail=$siteEmail' $gJSNoStatus>" .  $this->websiteText[25] . "</a> " . $this->websiteText[26];

      if (LibEmail::validate($siteEmail)) {
        LibEmail::sendMail($siteEmail, $websiteName, $emailSubject, $emailBody, $siteEmail, $websiteName);
      }
    }
  }

}

?>
