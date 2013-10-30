<?

class UserUtils extends UserDB {

  var $mlText;
  var $websiteText;

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  var $maxWidth;
  var $maxHeight;

  // The property name holding the list of secured pages
  var $propertySecuredPages;

  // Names of the cookies
  var $cookieAutoLogin;
  var $cookieSocketSessionId;

  // Duration of a user session, expressed in minutes
  var $sessionDuration;

  // The property name holding the post user login pages
  var $propertyComputerPostLoginPage;
  var $propertyPhonePostLoginPage;

  // The property name holding the expired login pages
  var $propertyComputerExpiredLoginPage;
  var $propertyPhoneExpiredLoginPage;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $popupUtils;
  var $profileUtils;
  var $clockUtils;
  var $templateUtils;
  var $addressUtils;
  var $uniqueTokenUtils;
  var $propertyUtils;
  var $facebookUtils;
  var $guestbookUtils;
  var $mailListUserUtils;
  var $smsListUserUtils;
  var $elearningSubscriptionUtils;
  var $elearningCourseUtils;
  var $shopOrderUtils;
  var $fileUploadUtils;

  function UserUtils() {
    $this->UserDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imagePath = $gDataPath . 'user/image/';
    $this->imageUrl = $gDataUrl . '/user/image';
    $this->imageSize = 200000;

    $this->cookieAutoLogin = "userAutoLogin";
    $this->cookieSocketSessionId = "socketSessionId";
    $this->sessionDuration = 120;
    $this->propertyComputerPostLoginPage = "TEMPLATE_COMPUTER_POST_LOGIN_PAGE_";
    $this->propertyPhonePostLoginPage = "TEMPLATE_PHONE_POST_LOGIN_PAGE_";
    $this->propertyComputerExpiredLoginPage = "TEMPLATE_COMPUTER_EXPIRED_LOGIN_PAGE_";
    $this->propertyPhoneExpiredLoginPage = "TEMPLATE_PHONE_EXPIRED_LOGIN_PAGE_";
    $this->propertySecuredPages = "USER_SECURED_PAGES";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'user')) {
        mkdir($gDataPath . 'user');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "USER_ALLOW_EXPIRED_LOGIN" =>
      array($this->mlText[52], $this->mlText[53], PREFERENCE_TYPE_BOOLEAN, ''),
        "USER_AUTO_REGISTER" =>
        array($this->mlText[2], $this->mlText[5], PREFERENCE_TYPE_BOOLEAN, ''),
          "USER_SECURITY_CODE" =>
          array($this->mlText[10], $this->mlText[11], PREFERENCE_TYPE_BOOLEAN, ''),
            "USER_CONFIRM_EMAIL" =>
            array($this->mlText[54], $this->mlText[55], PREFERENCE_TYPE_BOOLEAN, ''),
              "USER_SEND_LOGIN" =>
              array($this->mlText[8], $this->mlText[9], PREFERENCE_TYPE_BOOLEAN, ''),
                "USER_MINI_LOGIN" =>
                array($this->mlText[50], $this->mlText[51], PREFERENCE_TYPE_BOOLEAN, ''),
                  "USER_LOGIN_DISPLAY_PROFILE" =>
                  array($this->mlText[15], $this->mlText[16], PREFERENCE_TYPE_BOOLEAN, ''),
                    "USER_LOGIN_DISPLAY_PASSWORD" =>
                    array($this->mlText[17], $this->mlText[18], PREFERENCE_TYPE_BOOLEAN, ''),
                      "USER_DIRECT_LOGIN" =>
                      array($this->mlText[46], $this->mlText[47], PREFERENCE_TYPE_BOOLEAN, ''),
                        "USER_LOGIN_DURATION" =>
                        array($this->mlText[23], $this->mlText[26], PREFERENCE_TYPE_SELECT, array(1 => "1", 2 => "2", 3 => "3", 6 => "6", 12 => "12", 24 => "24", 36 => "36", 48 => "48", 52 => "52")),
                          "USER_LOGIN_TOKEN_DURATION" =>
                          array($this->mlText[29], $this->mlText[30], PREFERENCE_TYPE_SELECT, array(1 => "1", 2 => "2", 3 => "3", 6 => "6", 12 => "12", 24 => "24", 36 => "36", 48 => "48", 52 => "52")),
                            "USER_VALID_UNTIL_DURATION" =>
                            array($this->mlText[7], $this->mlText[14], PREFERENCE_TYPE_SELECT, array('' => $this->mlText[32], -1 => $this->mlText[31], 1 => "1", 2 => "2", 3 => "3", 6 => "6", 12 => "12", 24 => "24", 36 => "36", 48 => "48", 52 => "52")),
                              "USER_VALIDATE_DOMAIN_NAME" =>
                              array($this->mlText[19], $this->mlText[20], PREFERENCE_TYPE_BOOLEAN, ''),
                                "USER_LIST_STEP" =>
                                array($this->mlText[12], $this->mlText[13], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                                  "USER_DEFAULT_WIDTH" =>
                                  array($this->mlText[21], $this->mlText[22], PREFERENCE_TYPE_TEXT, 100),
                                    "USER_PHONE_DEFAULT_WIDTH" =>
                                    array($this->mlText[27], $this->mlText[28], PREFERENCE_TYPE_TEXT, 70),
                                    );

    $this->preferenceUtils->init($this->preferences);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($this->imagePath . $oneFile)) {
            @unlink($this->imagePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an image is being used
  function imageIsUsed($image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByImage($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Get the post user login redirection url
  function getPostUserLoginUrl() {
    global $gHomeUrl;

    $requestedUrl = $this->templateUtils->retrieveRequestedUrl();

    $postLoginPage = $this->getPostLoginPage();

    if ($postLoginPage) {
      $url = $this->templateUtils->renderPageUrl($postLoginPage);
    } else if ($requestedUrl) {
      // Prevent a redirect to the login page
      if (strstr($requestedUrl, "SYSTEM_PAGE_USER_LOGIN")) {
        $url = $gHomeUrl;
      } else {
        $url = $requestedUrl;
      }
    } else {
      $url = $gHomeUrl;
    }

    return($url);
  }

  // Get the expired user login redirection url
  function getExpiredUserLoginUrl() {
    global $gHomeUrl;

    $expiredLoginPage = $this->getExpiredLoginPage();

    if ($expiredLoginPage) {
      $url = $this->templateUtils->renderPageUrl($expiredLoginPage);
    } else {
      $requestedUrl = $this->templateUtils->retrieveRequestedUrl();
      if ($requestedUrl) {
        // Prevent a redirect to the login page
        if (strstr($requestedUrl, "SYSTEM_PAGE_USER_LOGIN")) {
          $url = $gHomeUrl;
        } else {
          $url = $requestedUrl;
        }
      } else {
        $url = $gHomeUrl;
      }
    }

    return($url);
  }

  // By default, when a user login has an expiration date and that date has passed, the user cannot log in anymore. In that case, the user is not identified and he cannot access the secured pages of the website. But it is possible to allow the user to log in even if the expiration date has passed. In that case, the user is identified but he still cannot access the secured pages of the website. In both cases the user cannot access the secured pages of the website.
  function allowExpiredLogin() {
    $allow = $this->preferenceUtils->getValue("USER_ALLOW_EXPIRED_LOGIN");

    return($allow);
  }

  // Get the duration of the login token
  // This has nothing to do with the cookie based automatic login
  function getLoginTokenDuration() {
    $loginTokenDuration = $this->preferenceUtils->getValue("USER_LOGIN_TOKEN_DURATION");

    $loginTokenDuration = $loginTokenDuration * 7;

    return($loginTokenDuration);
  }

  // Get the duration of the automatic login
  // This has nothing to do with the unique token login
  function getAutoLoginDuration() {
    $duration = $this->preferenceUtils->getValue("USER_LOGIN_DURATION");

    if (!$duration) {
      $duration = 1;
    }

    $autoLoginDuration = (60 * 60 * 24 * 7 * $duration);

    return($autoLoginDuration);
  }

  // Get the duration of the user account validity
  // It is possible to specify a validity period for the new user accounts. When a user registers himself, he'll be able to log in with his password only during a period of time. During that period an administrator will be able to confirm the user registration and the user login will no longer be limited in time. But if the user account is not confirmed by an administrator in time then the user won't be able to log in when this period expires. This period is expressed in weeks.
  function getUserValidUntilDuration() {
    $validUntil = $this->preferenceUtils->getValue("USER_VALID_UNTIL_DURATION");

    return($validUntil);
  }

  // Open a new user session
  function openUserSession($email) {
    // Open a new session
    LibSession::openSession();

    // Reset the last access time session value
    LibSession::putSessionValue(USER_SESSION_SESSION_TIME, time());

    // Save the email in the session
    LibSession::putSessionValue(USER_SESSION_LOGIN, $email);

    // Get the password as stored in the database as the source for the php encrypting
    $password = $this->getUserPassword($email);
    $cryptedPassword = crypt($password);
    LibCookie::putCookie($this->cookieAutoLogin, "$email:$cryptedPassword", $this->getAutoLoginDuration());

    $this->openSocketSession();
  }

  // Open a socket session so as to allow the socket to be authenticated
  function openSocketSession() {
    // Save an hashed session id to be used by the web socket
    $socketSessionId = md5(UTILS_WEB_SOCKET_SECRET_KEY . session_id());
    // The redis server is given the hashed session id for later authorization
    LibSession::putSessionValue($this->cookieSocketSessionId, $socketSessionId);
    // The socket request will later on need to send the hashed session id in its header cookie
    LibCookie::putCookie($this->cookieSocketSessionId, $socketSessionId, $this->sessionDuration * 60);
  }

  // Close a user session
  function closeUserSession() {
    LibCookie::deleteCookie($this->cookieAutoLogin);
    LibCookie::deleteCookie($this->cookieSocketSessionId);
    LibSession::delSessionValue($this->cookieSocketSessionId);

    LibSession::delSessionValue(USER_SESSION_SESSION_TIME);
    LibSession::delSessionValue(USER_SESSION_LOGIN);
  }

  // Check that a user is logged in
  function checkUserLogin() {
    global $gUserUrl;
    global $REQUEST_URI;

    $session = $this->checkUserSession();

    if ($session == false) {
      // Close the user session to delete all user session variables
      $this->closeUserSession();

      $url = "$gUserUrl/login.php";

      $templateModelId = $this->templateUtils->getTemplateModelFromUrl($REQUEST_URI);

      if ($templateModelId) {
        $url .= "?templateModelId=$templateModelId";
      }

      $str = LibHtml::urlRedirect($url);
      printContent($str);
      exit;
    }

    $email = LibSession::getSessionValue(USER_SESSION_LOGIN);

    return($email);
  }

  // Check that the user is logged in and has a valid login, that is, the login has not expired yet
  function checkValidUserLogin() {
    global $gUserUrl;
    global $REQUEST_URI;

    $email = $this->checkUserLogin();

    if ($this->temporaryUserIsNoLongerValid($email)) {
      $url = $this->getExpiredUserLoginUrl();

      $templateModelId = $this->templateUtils->getTemplateModelFromUrl($REQUEST_URI);

      if ($templateModelId) {
        $url .= "?templateModelId=$templateModelId";
      }

      $str = LibHtml::urlRedirect($url);
      printContent($str);
      exit;
    }

    return($email);
  }

  // Check if a user is logged in
  function isLoggedIn() {
    return($this->checkUserSession());
  }

  // Get the id of the user
  function getLoggedUserId() {
    $email = $this->getUserLogin();

    $userId = '';
    if ($user = $this->selectByEmail($email)) {
      $userId = $user->getId();
    }

    return($userId);
  }

  // Store the date of the last login
  function storeLastLogin($email) {
    if ($user = $this->selectByEmail($email)) {
      $systemDateTime = $this->clockUtils->getSystemDateTime();
      $user->setLastLogin($systemDateTime);
      $this->update($user);
    }
  }

  // Check the validity of a user session
  function checkUserSession() {
    // Check the auto login mode
    $cookieAutoLogin = LibCookie::getCookie($this->cookieAutoLogin);

    // At first the user session is considered invalid
    $session = false;

    // If the auto login is on
    if ($cookieAutoLogin) {
      // Then get the email and password from the cookie
      list($email, $cookiePassword) = explode(":", $cookieAutoLogin);

      // Get the password from the database
      $password = $this->getUserPassword($email);

      // Check the user password
      if (crypt($password, $cookiePassword) == $cookiePassword) {
        // Make session valid
        $session = true;

        // Store the login date
        $this->storeLastLogin($email);

        // Open a session
        $this->openUserSession($email);

        // Store the user email in the session
        LibSession::putSessionValue(USER_SESSION_LOGIN, $email);
      }
    } else {
      // Otherwise check the user session
      $session = LibSession::checkSession(USER_SESSION_SESSION_TIME, $this->sessionDuration);
    }

    return($session);
  }

  // Check the password of the user
  function checkUserPassword($email, $password) {
    if ($user = $this->selectByEmail($email)) {
      $passwordSalt = $user->getPasswordSalt();
      $hashedPassword = md5($password . $passwordSalt);
      if ($user = $this->selectByEmailAndPassword($email, $hashedPassword)) {
        return(true);
      } else {
        return(false);
      }
    }
  }

  // Set a random password
  function setRandomPassword($email) {
    if ($user = $this->selectByEmail($email)) {
      $readablePassword = $user->getReadablePassword();
      if (!$readablePassword) {
        $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
        $readablePassword = LibUtils::generateUniqueId();
        $hashedPassword = md5($readablePassword . $passwordSalt);
        $user->setPassword($hashedPassword);
        $user->setPasswordSalt($passwordSalt);
        $user->setReadablePassword($readablePassword);
        $this->updatePassword($user);
        return($readablePassword);
      }
    }
  }

  // Send an email address confirmation link to the user
  function sendConfirmationEmail($userId) {
    global $gUserUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $websiteName = $this->profileUtils->getProfileValue("website.name");
    $websiteEmail = $this->profileUtils->getProfileValue("website.email");
    $emailSubject = $this->mlText[56] . ' ' . $websiteName;

    // Create a temporary url for the link in the email
    $tokenName = USER_TOKEN_NAME;
    $tokenDuration = $this->getLoginTokenDuration();
    $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

    $confirmUrl = "$gUserUrl/validate_email.php?userId=$userId&tokenName=$tokenName&tokenValue=$tokenValue";
    $confirmLink = "<a href='$confirmUrl' $gJSNoStatus>" . $confirmUrl . '</a>';

    if ($user = $this->selectById($userId)) {
      $email = $user->getEmail();
      $emailBody = $this->mlText[57] . ' ' . $confirmLink . '<br><br>' . $websiteName;

      if (LibEmail::validate($email)) {
        LibEmail::sendMail($email, $email, $emailSubject, $emailBody, $websiteEmail, $websiteName);
      }
    }
  }

  // Send the email address and password to the user
  function sendLoginPassword($email, $password) {
    $this->loadLanguageTexts();

    $websiteName = $this->profileUtils->getProfileValue("website.name");
    $websiteEmail = $this->profileUtils->getProfileValue("website.email");
    $emailSubject = $this->mlText[33] . ' ' . $websiteName;
    $emailBody = $this->mlText[35] . ' ' . $email . '<br><br>' . $this->mlText[34] . ' ' . $password . '<br><br>' . $websiteName;

    if (LibEmail::validate($email)) {
      LibEmail::sendMail($email, $email, $emailSubject, $emailBody, $websiteEmail, $websiteName);
    }
  }

  // Set a period of validity for the user account
  // A user that registers himself can log in for a period of time only
  function setValidityPeriod($userId) {
    global $gUserUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $validUntilDuration = $this->getUserValidUntilDuration();
    if ($validUntilDuration) {
      if ($user = $this->selectById($userId)) {
        $email = $user->getEmail();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();

        $systemDateTime = $this->clockUtils->getSystemDateTime();
        $validUntil = $this->clockUtils->incrementWeeks($systemDateTime, $validUntilDuration);
        $localValidUntil = $this->clockUtils->systemToLocalNumericDate($validUntil);
        $user->setValidUntil($validUntil);
        $this->update($user);

        $websiteName = $this->profileUtils->getProfileValue("website.name");
        $websiteEmail = $this->profileUtils->getProfileValue("website.email");
        $emailSubject = $this->mlText[36] . ' ' . $websiteName;

        if ($validUntilDuration > 0) {
          $strValid = $this->mlText[37] . ' ' . $localValidUntil;
        } else {
          $strValid = $this->mlText[38];
        }

        // Create a temporary url for the link in the email
        $tokenName = USER_TOKEN_NAME;
        $tokenDuration = $this->getLoginTokenDuration();
        $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

        $emailBody = $this->templateUtils->renderDefaultModelCssPageProperties();

        $emailBody .= "\n<div class='system'>"
          . "<div class='system_email_content'>";

        $emailBody .= $this->mlText[39] . '<br><br>' . $this->mlText[40] . ' ' . $firstname . ' ' . $lastname . ' ' . $this->mlText[41] . ' ' . $email . ' ' . $this->mlText[42] . '<br><br>' . $strValid;
        $emailBody .= '<br><br>' . $this->mlText[43] . " <a href='$gUserUrl/validate.php?userId=$userId&tokenName=$tokenName&tokenValue=$tokenValue&siteEmail=$websiteEmail&validate=1' $gJSNoStatus>" . $this->mlText[44]. '</a> ' . $this->mlText[45];
        $emailBody .= '<br><br>' . $this->mlText[48] . " <a href='$gUserUrl/validate.php?userId=$userId&tokenName=$tokenName&tokenValue=$tokenValue&siteEmail=$websiteEmail&invalidate=1' $gJSNoStatus>" . $this->mlText[44] . '</a> ' . $this->mlText[49];
        $emailBody .= '<br><br>' . $websiteName;

        $emailBody .= '</div></div>';

        if (LibEmail::validate($email)) {
          LibEmail::sendMail($websiteEmail, $websiteName, $emailSubject, $emailBody, $websiteEmail, $websiteName);
        }
      }
    }
  }

  // Check if the user account is temporary and not valid any longer
  function temporaryUserIsNoLongerValid($email) {
    if ($user = $this->selectByEmail($email)) {
      $validUntil = $user->getValidUntil();
      if ($this->clockUtils->systemDateIsSet($validUntil)) {
        $systemDateTime = $this->clockUtils->getSystemDateTime();
        if ($this->clockUtils->systemDateIsGreater($systemDateTime, $validUntil)) {
          $localValidUntil = $this->clockUtils->systemToLocalNumericDate($validUntil);

          return($localValidUntil);
        }
      }
    }

    return(false);
  }

  // Check if the user with a login date that is no longer valid, cannot log in
  function noLongerValidUserCannotLogin($email) {
    if (!$this->allowExpiredLogin()) {
      if ($this->temporaryUserIsNoLongerValid($email)) {
        return(true);
      }
    }

    return(false);
  }

  // Check if the user email address is not yet confirmed
  // The user may need to confirm his email address by clicking on a single use html link sent to him in an email
  function userEmailAddressIsNotYetConfirmed($email) {
    $notConfirmed = false;

    if ($this->preferenceUtils->getValue("USER_CONFIRM_EMAIL")) {
      $notConfirmed = true;

      if ($user = $this->selectByEmail($email)) {
        if (!$user->getUnconfirmedEmail()) {
          $notConfirmed = false;
        }
      }
    }

    return($notConfirmed);
  }

  // Get the email of the user
  function getUserLogin() {
    $email = LibSession::getSessionValue(USER_SESSION_LOGIN);

    return($email);
  }

  // Get the email of the user
  function getUserEmail() {
    $email = $this->getUserLogin();

    return($email);
  }

  // Get the password of the user
  function getUserPassword($email) {
    $password = '';

    if ($user = $this->selectByEmail($email)) {
      $password = $user->getPassword();
    }

    return($password);
  }

  // Get the post user login page
  function getPostLoginPage($languageCode = '') {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $page = $this->getPhonePostLoginPage($languageCode);
    } else {
      $page = $this->getComputerPostLoginPage($languageCode);
    }

    return($page);
  }

  // Get the post user login page for a language
  function getComputerPostLoginPage($languageCode = '') {
    $page = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $page = $this->propertyUtils->retrieve($this->propertyComputerPostLoginPage . $languageCode);
    }

    return($page);
  }

  // Set the post user login page for a language
  function setComputerPostLoginPage($languageCode, $page) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyComputerPostLoginPage . $languageCode, $page);
    }
  }

  // Get the post user login page for language
  function getPhonePostLoginPage($languageCode = '') {
    $page = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $page = $this->propertyUtils->retrieve($this->propertyPhonePostLoginPage . $languageCode);
    }

    return($page);
  }

  // Set the post user login page for a language
  function setPhonePostLoginPage($languageCode, $page) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyPhonePostLoginPage . $languageCode, $page);
    }
  }

  // Get the expired user login page
  function getExpiredLoginPage($languageCode = '') {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $page = $this->getPhoneExpiredLoginPage($languageCode);
    } else {
      $page = $this->getComputerExpiredLoginPage($languageCode);
    }

    return($page);
  }

  // Get the expired user login page for a language
  function getComputerExpiredLoginPage($languageCode = '') {
    $page = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $page = $this->propertyUtils->retrieve($this->propertyComputerExpiredLoginPage . $languageCode);
    }

    return($page);
  }

  // Set the expired user login page for a language
  function setComputerExpiredLoginPage($languageCode, $page) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyComputerExpiredLoginPage . $languageCode, $page);
    }
  }

  // Get the expired user login page for language
  function getPhoneExpiredLoginPage($languageCode = '') {
    $page = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $page = $this->propertyUtils->retrieve($this->propertyPhoneExpiredLoginPage . $languageCode);
    }

    return($page);
  }

  // Set the expired user login page for a language
  function setPhoneExpiredLoginPage($languageCode, $page) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyPhoneExpiredLoginPage . $languageCode, $page);
    }
  }

  // Delete the last imported users
  function deleteLastImportedUsers() {
    if ($users = $this->selectImported()) {
      foreach($users as $user) {
        $userId = $user->getId();
        $this->deleteUser($userId);
      }
    }
  }

  // Delete a user
  function deleteUser($userId) {
    // Delete the Facebook user if any
    if ($userFacebook = $this->facebookUtils->selectByUserId($userId)) {
      $userFacebookId = $userFacebook->getId();
      $this->facebookUtils->delete($userFacebookId);
    }

    // Delete the postings in the guestbook if any
    $guestbooks = $this->guestbookUtils->selectByUserId($userId);
    foreach ($guestbooks as $guestbook) {
      $guestbookId = $guestbook->getId();
      $this->guestbookUtils->delete($guestbookId);
    }

    // Delete entries from the mail lists if any
    if ($mailListUsers = $this->mailListUserUtils->selectByUserId($userId)) {
      foreach ($mailListUsers as $mailListUser) {
        $mailListUserId = $mailListUser->getId();
        $this->mailListUserUtils->delete($mailListUserId);
      }
    }

    // Delete entries from the sms lists if any
    if ($smsListUsers = $this->smsListUserUtils->selectByUserId($userId)) {
      foreach ($smsListUsers as $smsListUser) {
        $smsListUserId = $smsListUser->getId();
        $this->smsListUserUtils->delete($smsListUserId);
      }
    }

    // Delete entries from the elearning subscriptions if any
    if ($elearningSubscriptions = $this->elearningSubscriptionUtils->selectByUserId($userId)) {
      foreach ($elearningSubscriptions as $elearningSubscription) {
        $elearningSubscriptionId = $elearningSubscription->getId();
        $this->elearningSubscriptionUtils->deleteSubscription($elearningSubscriptionId);
      }
    }

    // Remove the user from elearning courses if any
    if ($elearningCourses = $this->elearningCourseUtils->selectByUserId($userId)) {
      foreach ($elearningCourses as $elearningCourse) {
        $elearningCourse->setUserId('');
        $this->elearningCourseUtils->update($elearningCourse);
      }
    }

    // Delete entries from the shop orders if any
    if ($shopOrders = $this->shopOrderUtils->selectByUserId($userId)) {
      foreach ($shopOrders as $shopOrder) {
        $shopOrderId = $shopOrder->getId();
        $this->shopOrderUtils->deleteOrder($shopOrderId);
      }
    }

    // Delete the address of the user
    if ($user = $this->selectById($userId)) {
      $this->delete($userId);

      $addressId = $user->getAddressId();
      $this->addressUtils->delete($addressId);
    }
  }

  // Delete the post user login page property
  function deletePostUserLoginPage() {
    $languages = $this->languageUtils->selectAll();
    foreach ($languages as $language) {
      $languageCode = $language->getCode();
      if (!$this->languageUtils->isActiveLanguage($languageCode)) {
        $this->propertyUtils->delete($this->propertyPostUserLoginPage . $languageCode);
      }
    }
  }

  // Render the user mini login form
  function renderUserMiniLogin() {
    global $gJSNoStatus;
    global $gUserUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='user_login'>";

    if ($this->checkUserSession() == false) {
      if ($this->preferenceUtils->getValue("USER_MINI_LOGIN")) {

        if ($gIsPhoneClient) {
          $fieldSize = 20;
        } else {
          $fieldSize = 10;
        }

        $str .= "<form action='$gUserUrl/login.php' method='post' name='user_mini_login'>"
          . "<div class='user_login_name'>"
          . $this->websiteText[24]
          . "<br />"
          . "<input class='user_login_input' type='text' name='email' size='$fieldSize' maxlength='255' />"
          . "</div>"
          . "<div class='user_login_password'>"
          . $this->websiteText[25]
          . "<br />"
          . "<input class='user_login_input' type='password' name='password' size='$fieldSize' maxlength='10' />"
          . "</div>"
          . "<div>"
          . $this->websiteText[58]
          . " <input type='checkbox' name='autologin' value='1' />"
          . "</div>"
          . "<div class='user_login_okay'>"
          // An input field is required to have the browser submit the form on Enter key press
          // Otherwise a form with more than one input field is not submitted
          . "<input type='submit' value='' style='display:none;' />"
          . "<a href='#' onclick=\"document.forms['user_mini_login'].submit(); return false;\">" . $this->websiteText[0] . "</a>"
          . "</div>";

        $str .= "<input type='hidden' name='formSubmitted' value='1' />";

        $str .= "</form>";

      } else {

        $str .= "<div class='user_login_link'>"
          . "<a href='$gUserUrl/login.php' $gJSNoStatus>"
          . $this->websiteText[0]
          . "</a></div>";

      }

      if ($this->preferenceUtils->getValue("USER_AUTO_REGISTER")) {
        $str .= "<div class='user_register_link'>"
          . "<a href='$gUserUrl/register.php' $gJSNoStatus>"
          . $this->websiteText[6]
          . "</a></div>";
      }

    } else {

      // Get the currently logged in user
      $email = $this->checkUserLogin();
      if ($user = $this->selectByEmail($email)) {
        $userId = $user->getId();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
      } else {
        $userId = '';
        $firstname = '';
        $lastname = '';
      }

      $str .= "<div class='user_edit_profile_link'>"
        . "<a href='$gUserUrl/editProfile.php' $gJSNoStatus>$firstname $lastname</a></div>";

      $loginDisplayProfile = $this->preferenceUtils->getValue("USER_LOGIN_DISPLAY_PROFILE");

      if ($loginDisplayProfile) {
        $str .= "<div class='user_edit_profile_link'>"
          . "<a href='$gUserUrl/editProfile.php' $gJSNoStatus>"
          . $this->websiteText[4]
          . "</a></div>";
      }

      $loginDisplayPassword = $this->preferenceUtils->getValue("USER_LOGIN_DISPLAY_PASSWORD");

      if ($loginDisplayPassword) {
        $str .= "<div class='user_change_password_link'>"
          . "<a href='$gUserUrl/changePassword.php' $gJSNoStatus>"
          . $this->websiteText[3]
          . "</a></div>";
      }

      $str .= "<div class='user_logout_link'>"
        . "<a href='$gUserUrl/logout.php' $gJSNoStatus>"
        . $this->websiteText[1]
        . "</a></div>";
    }

    $str .= "</div>";

    return($str);
  }

  // Display a popup window for a label tip
  function getTipPopup($anchor, $content, $width, $height) {
    return($this->popupUtils->getUserTipPopup($anchor, $content, $width, $height));
  }

  // Secure a page
  function securePage($pageId) {
    if ($pageId && !$this->isSecuredPage($pageId)) {
      $strSecuredPages = $this->propertyUtils->retrieve($this->propertySecuredPages);
      $securedPages = explode(PROPERTY_SEPARATOR, $strSecuredPages);
      $securedPages[count($securedPages)] = $pageId;
      $strSecuredPages = implode(PROPERTY_SEPARATOR, $securedPages);
      $this->propertyUtils->store($this->propertySecuredPages, $strSecuredPages);
    }
  }

  // Unsecure a page
  function unsecurePage($pageId) {
    if ($pageId && $this->isSecuredPage($pageId)) {
      $strSecuredPages = $this->propertyUtils->retrieve($this->propertySecuredPages);
      $securedPages = explode(PROPERTY_SEPARATOR, $strSecuredPages);
      $key = array_search($pageId, $securedPages);
      unset($securedPages[$key]);
      $strSecuredPages = implode(PROPERTY_SEPARATOR, $securedPages);
      $this->propertyUtils->store($this->propertySecuredPages, $strSecuredPages);
    }
  }

  // Check if a page is secured
  function isSecuredPage($pageId) {
    $securedPages = $this->getSecuredPages();
    if (in_array($pageId, $securedPages)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the list of secured pages
  function getSecuredPages() {
    $strSecuredPages = $this->propertyUtils->retrieve($this->propertySecuredPages);
    $securedPages = explode(PROPERTY_SEPARATOR, $strSecuredPages);

    return($securedPages);
  }

  // Export the users into a csv file
  function exportCSV($filename) {
    // If the file aready exists, delete it before creating a new empty one
    if (@file_exists($filename)) {
      @unlink($filename);
    }

    $fp = @fopen($filename, "w");

    if (!@is_resource($fp)) {
      return(false);
    }

    $this->dataSource->selectDatabase();

    if ($users = $this->selectAll()) {
      foreach ($users as $user) {
        $email = $user->getEmail();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $line = $email . ',' . $firstname . ',' . $lastname;
        fwrite($fp, "$line\n");
      }
    }

    fclose($fp);

    return(true);
  }

  // Render an image
  function renderImage($userId) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    if (!$user = $this->selectById($userId)) {
      return;
    }

    $image = $user->getImage();

    $imagePath  = $this->imagePath;
    $imageUrl  = $this->imageUrl;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("USER_PHONE_DEFAULT_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("USER_DEFAULT_WIDTH");
    }

    $str = '';

    if ($image && @file_exists($imagePath . $image)) {
      $str .= "<div class='user_image'>";

      if (LibImage::isImage($imagePath . $image)) {

        if (!$this->fileUploadUtils->isGifImage($imagePath . $image)) {
          // The image is created on the fly
          $filename = urlencode($imagePath . $image);
          $url = $gUtilsUrl . "/printImage.php?filename=" . $filename
            . "&amp;width=" . $width . "&amp;height=";
        } else {
          $url = "$imageUrl/$image";
        }

        $str .= "<img class='user_image_file' src='$url' title='' alt='' width='$width' />";
      }
      $str .= "</div>";
    }

    return($str);
  }

}

?>
