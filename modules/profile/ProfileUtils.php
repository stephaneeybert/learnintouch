<?

class ProfileUtils extends ProfileDB {

  var $mlText;

  var $propertyLogoFilename;
  var $propertyFaviconFilename;
  var $propertyIPhoneIconFilename;
  var $propertyMapFilename;

  var $fileSize;
  var $filePath;
  var $fileUrl;

  var $profileNames;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $propertyUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->fileSize = 2000000;
    $this->filePath = $gDataPath . 'profile/file/';
    $this->fileUrl = $gDataUrl . '/profile/file';

    $this->propertyLogoFilename = "PROFILE_LOGO_FILENAME";
    $this->propertyFaviconFilename = "PROFILE_FAVICON_FILENAME";
    $this->propertyIPhoneIconFilename = "PROFILE_IPHONEICON_FILENAME";
    $this->propertyMapFilename = "PROFILE_MAP_FILENAME";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->filePath)) {
      if (!is_dir($gDataPath . 'profile')) {
        mkdir($gDataPath . 'profile');
      }
      mkdir($this->filePath);
      chmod($this->filePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadProfileNames() {
    global $gGoogleUrl;

    $this->loadLanguageTexts();

    $googleRegisterLabel = $this->mlText[45]
      . ' ' . $this->fileUrl . '/' . $this->getLogoFilename()
      . ' ' . $this->mlText[46]
      . ' ' . $this->mlText[48]
      . ' ' . $gGoogleUrl . '/login.php'
      . ' ' . $this->mlText[49];

    $this->profileNames = array(
      array("website.name", $this->mlText[1], $this->mlText[101]),
      array("website.email", $this->mlText[8], $this->mlText[108]),
      array("webmaster.name", $this->mlText[9], $this->mlText[109]),
      array("webmaster.email", $this->mlText[10], $this->mlText[110]),
      array("mail.smtp.host", $this->mlText[58], $this->mlText[59]),
      array("mail.smtp.port", $this->mlText[60], $this->mlText[61]),
      array("mail.smtp.username", $this->mlText[62], $this->mlText[63]),
      array("mail.smtp.password", $this->mlText[64], $this->mlText[65]),
      array("facebook.apikey", $this->mlText[3], $this->mlText[4]),
      array("facebook.secret", $this->mlText[5], $this->mlText[11]),
      array("facebook.appid", $this->mlText[13], $this->mlText[14]),
      array("linkedin.apikey", $this->mlText[37], $this->mlText[38]),
      array("google.apikey", $this->mlText[39], $this->mlText[40] . $googleRegisterLabel),
      array("google.client.id", $this->mlText[41], $this->mlText[42] . $googleRegisterLabel),
      array("google.client.secret", $this->mlText[43], $this->mlText[44] . $googleRegisterLabel),
    );
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "PROFILE_WEBSITE_TITLE" =>
      array($this->mlText[22], $this->mlText[21] . ' ' . $this->mlText[23], PREFERENCE_TYPE_MLTEXT, ''),
        "PROFILE_WEBSITE_DESCRIPTION" =>
        array($this->mlText[24], $this->mlText[25], PREFERENCE_TYPE_MLTEXT, ''),
          "PROFILE_WEBSITE_KEYWORDS" =>
          array($this->mlText[26], $this->mlText[27], PREFERENCE_TYPE_MLTEXT, ''),
            "PROFILE_WEBSITE_COPYRIGHT" =>
            array($this->mlText[17], $this->mlText[117], PREFERENCE_TYPE_MLTEXT, ''),
              "PROFILE_WEBSITE_TERMS_OF_SERVICE" =>
              array($this->mlText[30], $this->mlText[31], PREFERENCE_TYPE_MLTEXT, ''),
                "PROFILE_WEBSITE_ADDRESS" =>
                array($this->mlText[2], $this->mlText[12], PREFERENCE_TYPE_MLTEXT, ''),
                  "PROFILE_WEBSITE_TELEPHONE" =>
                  array($this->mlText[6], $this->mlText[18], PREFERENCE_TYPE_TEXT, ''),
                    "PROFILE_WEBSITE_FAX" =>
                    array($this->mlText[7], $this->mlText[19], PREFERENCE_TYPE_TEXT, ''),
                      "PROFILE_INVITE_MESSAGE" =>
                      array($this->mlText[32], $this->mlText[33], PREFERENCE_TYPE_MLTEXT, $this->mlText[34]),
                        "PROFILE_SOCIAL_DESCRIPTION" =>
                        array($this->mlText[15], $this->mlText[16], PREFERENCE_TYPE_MLTEXT),
                          "PROFILE_WEBSITE_GOOGLE_ANALYTICS" =>
                          array($this->mlText[28], $this->mlText[29], PREFERENCE_TYPE_RAW_CONTENT, ''),
                          "PROFILE_JS_BODY_END" =>
                          array($this->mlText[56], $this->mlText[57], PREFERENCE_TYPE_RAW_CONTENT, ''),
          "PROFILE_JS_DISABLE" =>
          array($this->mlText[66], $this->mlText[67], PREFERENCE_TYPE_BOOLEAN, ''),          
                          );

    $this->preferenceUtils->init($this->preferences);
  }

  // Render the web site telephone
  function renderWebSiteTelephone() {
    $str = "\n<div class='profile_telephone'>";

    $str .= $this->preferenceUtils->getValue("PROFILE_WEBSITE_TELEPHONE");

    $str .= "\n</div>";

    return($str);
  }

  // Render the web site fax number
  function renderWebSiteFax() {
    $str = "\n<div class='profile_fax'>";

    $str .= $this->preferenceUtils->getValue("PROFILE_WEBSITE_FAX");

    $str .= "\n</div>";

    return($str);
  }

  // Get the web site name
  function getWebSiteName() {
    if ($profile = $this->selectByName("website.name")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the web site address
  function getWebSiteAddress() {
    $address = trim($this->preferenceUtils->getValue("PROFILE_WEBSITE_ADDRESS"));

    return($address);
  }

  // Render the web site address
  function renderWebSiteAddress() {
    $str = "\n<div class='profile_address'>";

    $str .= $this->getWebSiteAddress();

    $str .= "\n</div>";

    return($str);
  }

  // Get the web site terms of service
  function getWebSiteTermsOfService() {
    $termsOfService = trim($this->preferenceUtils->getValue("PROFILE_WEBSITE_TERMS_OF_SERVICE"));

    return($termsOfService);
  }

  // Render the web site terms of service
  function renderWebSiteTermsOfService() {
    $str = '';

    $terms = $this->getWebSiteTermsOfService();

    if (!LibString::containsHtmlLineBreak($terms)) {
      $terms = nl2br($terms);
    }

    $str .= $terms;

    return($str);
  }

  // Get the web site copyright
  function getWebSiteCopyright() {
    $copyright = trim($this->preferenceUtils->getValue("PROFILE_WEBSITE_COPYRIGHT"));

    return($copyright);
  }

  // Render the web site copyright
  function renderWebSiteCopyright() {
    $str = "\n<div class='profile_copyright'>";

    $str .= $this->getWebSiteCopyright();

    $str .= "\n</div>";

    return($str);
  }

  // Get a property value
  function getProfileValue($name) {
    $value = '';

    // If the pair exists then return its value
    if ($name) {
      if ($profile = $this->selectByName($name)) {
        $value = $profile->getValue();
      } else {
        // Otherwise create it
        $profile = new Profile();
        $profile->setName($name);
        $this->insert($profile);
      }
    }

    return($value);
  }

  // Get the profiles
  // Select from the hard coded names
  function getProfileNames() {
    $profileNames = array();
    foreach ($this->profileNames as $profileName) {
      $name = $profileName[0];
      // If it does not exist then create it
      if (!$profile = $this->selectByName($name)) {
        $profile = new Profile();
        $profile->setName($name);
        $this->insert($profile);
      } else {
        // Otherwise get the value
        $profileName[3] = $profile->getValue();
      }

      array_push($profileNames, $profileName);
    }

    return($profileNames);
  }

  // Get the title
  function getWebsiteTitle() {
    $title = $this->preferenceUtils->getValue("PROFILE_WEBSITE_TITLE");

    return($title);
  }

  // Get the description
  function getWebsiteDescription() {
    $description = $this->preferenceUtils->getValue("PROFILE_WEBSITE_DESCRIPTION");

    return($description);
  }

  // Get the invite message
  function getInviteMessage() {
    $description = $this->preferenceUtils->getValue("PROFILE_INVITE_MESSAGE");

    return($description);
  }

  // Get the keywords
  function getWebsiteKeywords() {
    $keywords = $this->preferenceUtils->getValue("PROFILE_WEBSITE_KEYWORDS");

    return($keywords);
  }

  // Get the Google Analytics
  function getGoogleAnalytics() {
    $googleAnalytics = $this->preferenceUtils->getValue("PROFILE_WEBSITE_GOOGLE_ANALYTICS");

    return($googleAnalytics);
  }

  function isEnabled() {
    if ($this->getJsBodyEnd() && !$this->preferenceUtils->getValue("PROFILE_JS_DISABLE")) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the javascript to be inserted at the end of the page body
  function getJsBodyEnd() {
    $googleAnalytics = $this->preferenceUtils->getValue("PROFILE_JS_BODY_END");

    return($googleAnalytics);
  }

  // Get the mail SMTP hostname
  function getSMTPHostname() {
    if ($profile = $this->selectByName("mail.smtp.host")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the mail SMTP port number
  function getSMTPPort() {
    if ($profile = $this->selectByName("mail.smtp.port")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the mail SMTP username
  function getSMTPUsername() {
    if ($profile = $this->selectByName("mail.smtp.username")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the mail SMTP password
  function getSMTPPassword() {
    if ($profile = $this->selectByName("mail.smtp.password")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the Facebook api key
  function getFacebookApiKey() {
    if ($profile = $this->selectByName("facebook.apikey")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the Facebook application secret
  function getFacebookApplicationSecret() {
    if ($profile = $this->selectByName("facebook.secret")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the Facebook application id
  function getFacebookApplicationId() {
    if ($profile = $this->selectByName("facebook.appid")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the LinkedIn api key
  function getLinkedinApiKey() {
    if ($profile = $this->selectByName("linkedin.apikey")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the Google site Id
  function getGoogleApiKey() {
    if ($profile = $this->selectByName("google.apikey")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the Google client id
  function getGoogleClientId() {
    if ($profile = $this->selectByName("google.client.id")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the Google client secret
  function getGoogleClientSecret() {
    if ($profile = $this->selectByName("google.client.secret")) {
      $value = $profile->getValue();

      return($value);
    }
  }

  // Get the description used when publishing messages on social websites like Facebook
  function getWebsiteSocialDescription() {
    $description = $this->preferenceUtils->getValue("PROFILE_SOCIAL_DESCRIPTION");

    return($description);
  }

  // Set the logo file name
  function setLogoFilename($filename) {
    $this->propertyUtils->store($this->propertyLogoFilename, $filename);
  }

  // Get the logo file name
  function getLogoFilename() {
    $filename = $this->propertyUtils->retrieve($this->propertyLogoFilename);

    return($filename);
  }

  // Set the iPhone start app file name
  function setIPhoneIconFilename($filename) {
    $this->propertyUtils->store($this->propertyIPhoneIconFilename, $filename);
  }

  // Get the iPhone start app file name
  function getIPhoneIconFilename() {
    $filename = $this->propertyUtils->retrieve($this->propertyIPhoneIconFilename);

    return($filename);
  }

  // Set the favicon file name
  function setFaviconFilename($filename) {
    $this->propertyUtils->store($this->propertyFaviconFilename, $filename);
  }

  // Get the favicon file name
  function getFaviconFilename() {
    $filename = $this->propertyUtils->retrieve($this->propertyFaviconFilename);

    return($filename);
  }

  // Set the logo file name
  function setMapFilename($filename) {
    $this->propertyUtils->store($this->propertyMapFilename, $filename);
  }

  // Get the logo file name
  function getMapFilename() {
    $filename = $this->propertyUtils->retrieve($this->propertyMapFilename);

    return($filename);
  }

  // Render the iPhone icon
  function renderIPhoneIcon() {
    $str = '';

    $filename = $this->getIPhoneIconFilename();

    if (is_file($this->filePath . $filename)) {
      $str .= "\n<link rel='apple-touch-icon' href='" . $this->fileUrl . "/" . $filename . "' />";
    }

    return($str);
  }

  // Render the favicon
  function renderFavicon() {
    $str = '';

    $filename = $this->getFaviconFilename();

    if (is_file($this->filePath . $filename)) {
      $str .= "\n<link rel='shortcut icon' href='" . $this->fileUrl . "/" . $filename . "' />";
    }

    return($str);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedFiles() {
    $handle = opendir($this->filePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->fileIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->filePath . $oneFile)) {
            unlink($this->filePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if a file is being used
  function fileIsUsed($filename) {
    if ($filename == $this->getLogoFilename() || $filename == $this->getFaviconFilename() || $filename == $this->getMapFilename() || $filename == $this->getIPhoneIconFilename()) {
      return(true);
    }

    return(false);
  }

}

?>
