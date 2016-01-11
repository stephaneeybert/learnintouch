<?

class AdminUtils extends AdminDB {

  var $mlText;
  var $websiteText;

  // The reserved login names for the staff members
  var $staffLogins;

  // The admin password that can be used by the staff
  var $staffPassword;

  // The email of the staff
  var $staffEmail;

  // Property names
  var $propertyLastUpdate;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $clockUtils;
  var $adminModuleUtils;
  var $adminOptionUtils;
  var $mailUtils;
  var $userUtils;
  var $smsUtils;
  var $mailHistoryUtils;
  var $smsHistoryUtils;
  var $dynpageUtils;
  var $websiteOptionUtils;
  var $propertyUtils;

  function AdminUtils() {
    $this->AdminDB();

    $this->init();
  }

  function init() {
    global $gAdminPath;

    $this->sessionDuration = 60 * 12;
    $this->cookieLoginDuration = 60 * 60 * 24 * 7;
    $this->staffLogins = Array("root");
    $staffPasswordFile = $gAdminPath . "staffpassword.txt";
    if (is_file($staffPasswordFile)) {
      $passwords = file($staffPasswordFile);
      if (count($passwords) > 0) {
        $password = LibString::stripLineBreaks($passwords[0]);
        if (isset($password)) {
          $this->staffPassword = $password;
        }
      }
    }
    $this->staffEmail = STAFF_EMAIL;
    $this->propertyLastUpdate = "ADMIN_LAST_UPDATE";
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "ADMIN_LOGIN_TOKEN_DURATION" =>
      array($this->mlText[1], $this->mlText[2], PREFERENCE_TYPE_SELECT, array(1 => "1", 2 => "2", 3 => "3", 6 => "6", 12 => "12", 24 => "24", 36 => "36", 48 => "48", 52 => "52")),
        "ADMIN_LIST_STEP" =>
        array($this->mlText[3], $this->mlText[4], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100"))
      );

    $this->preferenceUtils->init($this->preferences);
  }

  // Get the duration of the login token
  function getLoginTokenDuration() {
    $loginTokenDuration = $this->preferenceUtils->getValue("ADMIN_LOGIN_TOKEN_DURATION");

    $loginTokenDuration = $loginTokenDuration * 7;

    return($loginTokenDuration);
  }

  // Get the list of possible urls an administrator is redirected to after logging in
  function getPostLoginUrls() {
    $this->loadLanguageTexts();

    $postLoginUrls = array(
      "modules/elearning/subscription/admin.php" => $this->mlText[6],
      "modules/elearning/assignment/admin.php" => $this->mlText[9],
      "modules/elearning/assignment/results.php" => $this->mlText[8],
      "modules/elearning/lesson/admin.php" => $this->mlText[10],
      "modules/elearning/exercise/admin.php" => $this->mlText[11],
    );

    return($postLoginUrls);
  }

  // Open a new admin session
  function openSession($login) {
    // Open a new session
    LibSession::openSession();

    // Reset the last access date session value
    LibSession::putSessionValue(ADMIN_SESSION_ACCESS_TIME, time());

    // Save the login in the session
    LibSession::putSessionValue(ADMIN_SESSION_LOGIN, $login);
  }

  // Close an admin session
  function closeSession() {
    LibSession::delSessionValue(ADMIN_SESSION_ACCESS_TIME);
    LibSession::delSessionValue(ADMIN_SESSION_LOGIN);
    LibSession::delSessionValue(ADMIN_SESSION_ADMIN_ID);
  }

  // Check the validity of an admin session
  function checkSession() {
    $session = LibSession::checkSession(ADMIN_SESSION_ACCESS_TIME, $this->sessionDuration);

    return($session);
  }

  // Log in an administrator
  function logIn($login) {
    // Open a new session
    $this->openSession($login);

    $this->userUtils->openSocketSession();

    // Save the login name in a cookie
    LibCookie::putCookie(ADMIN_SESSION_LOGIN, $login, $this->cookieLoginDuration);

    // Remove non granted admin modules and options
    if ($admin = $this->selectByLogin($login)) {
      $adminId = $admin->getId();
      $this->adminModuleUtils->removeNonGranted($adminId);
    }

    // Do some administration jobs
    $this->doAdminJobs();
  }

  // Check that an administrator is logged in
  function checkAdminLogin() {
    global $gAdminUrl, $gRedirectDelay;

    // Get the login value
    $login = $this->getSessionLogin();

    if (!$login) {
      // Close the admin session to delete all admin session variables
      $this->closeSession();

      $str = LibHtml::urlRedirect("$gAdminUrl/login.php");
      printMessage($str);
      exit;
    }

    return($login);
  }

  // Check if the admin is a super admin and is currently logged
  function checkSuperAdminLogin() {
    global $gAdminUrl, $gRedirectDelay;

    if (!$this->isLoggedSuperAdmin()) {
      $this->loadLanguageTexts();

      $str = $this->mlText[5];
      $str .= LibHtml::urlDisplayRedirect("$gAdminUrl/menu.php", $gRedirectDelay);
      printMessage($str);
      exit;
    }
  }

  // Check if the admin is a super admin and is currently logged
  function isLoggedSuperAdmin() {
    $login = $this->getSessionLogin();

    return($this->isLogged($login) && $this->isSuperAdmin($login));
  }

  // Get the login name of the currently logged in admin from the session
  function getSessionLogin() {
    // Check that a session is opened
    $session = $this->checkSession();

    // Get the login value
    if ($session) {
      $login = LibSession::getSessionValue(ADMIN_SESSION_LOGIN);
    } else {
      $login = '';
    }

    return($login);
  }

  // Get the id of the currently logged in administrator
  function getLoggedAdminId() {
    $adminId = '';

    $login = $this->getSessionLogin();

    if ($admin = $this->selectByLogin($login)) {
      $adminId = $admin->getId();
    }

    return($adminId);
  }

  // Check if the admin is currently logged
  function isLogged($login) {
    if (!$login) {
      return(false);
    }

    // Get the login value
    $loginSession = $this->getSessionLogin();

    if (!$loginSession) {
      return(false);
    }

    if ($loginSession != $login) {
      return(false);
    }

    return(true);
  }

  // Check if the admin can edit the preferences
  function isPreferenceAdmin($login) {
    $preferenceAdmin = false;

    if ($this->isSuperAdmin($login)) {
      return(true);
    }

    if ($admin = $this->selectByLogin($login)) {
      $preferenceAdmin = $admin->getPreferenceAdmin();
    }

    return($preferenceAdmin);
  }

  // Check if the admin is a super admin
  function isSuperAdmin($login) {
    $superAdmin = false;

    if ($this->isStaffLogin($login)) {
      return(true);
    }

    if ($admin = $this->selectByLogin($login)) {
      $superAdmin = $admin->getSuperAdmin();
    }

    return($superAdmin);
  }

  // Check that the administrator has a staff login name
  function checkForStaffLogin() {
    global $gAdminUrl, $gRedirectDelay;

    $login = $this->checkAdminLogin();

    if (!$this->isStaffLogin($login)) {
      $this->loadLanguageTexts();

      $str = $this->mlText[5];
      $str .= LibHtml::urlDisplayRedirect("$gAdminUrl/menu.php", $gRedirectDelay);
      printMessage($str);
      exit;
    }
  }

  // Check that the admin is a staff member
  function isStaff() {
    $isStaff = false;

    $login = $this->checkAdminLogin();

    if ($this->isStaffLogin($login)) {
      $isStaff = true;
    }

    return($isStaff);
  }

  // Check if the login name is a staff login name
  function isStaffLogin($login) {
    $isStaff = false;

    if (in_array($login, $this->staffLogins)) {
      $isStaff = true;
    }

    return($isStaff);
  }

  // Delete an administrator
  function deleteAdmin($adminId) {
    if ($mails = $this->mailUtils->selectByAdminId($adminId)) {
      foreach ($mails as $mail) {
        $mailId = $mail->getId();
        $this->mailUtils->delete($mailId);
      }
    }

    if ($mailHistories = $this->mailHistoryUtils->selectByAdminId($adminId)) {
      foreach ($mailHistories as $mailHistory) {
        $mailHistoryId = $mailHistory->getId();
        $this->mailHistoryUtils->delete($mailHistoryId);
      }
    }

    if ($smss = $this->smsUtils->selectByAdminId($adminId)) {
      foreach ($smss as $sms) {
        $smsId = $sms->getId();
        $this->smsUtils->delete($smsId);
      }
    }

    if ($smsHistories = $this->smsHistoryUtils->selectByAdminId($adminId)) {
      foreach ($smsHistories as $smsHistory) {
        $smsHistoryId = $smsHistory->getId();
        $this->smsHistoryUtils->delete($smsHistoryId);
      }
    }

    if ($dynpages = $this->dynpageUtils->selectByAdminId($adminId)) {
      foreach ($dynpages as $dynpage) {
        $dynpage->setAdminId('');
        $this->dynpageUtils->update($dynpage);
      }
    }

    $this->adminModuleUtils->deleteAdminModules($adminId);

    $this->adminOptionUtils->deleteAdminOptions($adminId);

    $this->delete($adminId);
  }

  // Get the languages the admin can translate into
  function getTranslateLanguageCodes() {
    $login = LibSession::getSessionValue(ADMIN_SESSION_LOGIN);
    if ($admin = $this->selectByLogin($login)) {
      $adminId = $admin->getId();
      $value = $this->adminOptionUtils->getOptionValue(OPTION_LANGUAGE_TRANSLATE, $adminId);
      if ($value) {
        $values = $this->websiteOptionUtils->getOptionValues(OPTION_LANGUAGE_TRANSLATE);
        if (isset($values[$value])) {
          $translateLanguageCode = $values[$value];
          return($translateLanguageCode);
        }
      }
    } else if ($this->isStaffLogin($login)) {
      $translateLanguageCode = "se";
      return($translateLanguageCode);
    }
  }

  // Do some administrative jobs
  function doAdminJobs() {
    // Set the last update date
    $this->clockUtils->setLocale();
    $timeStamp = $this->clockUtils->getLocalTimeStamp();
    $this->propertyUtils->store($this->propertyLastUpdate, $timeStamp);
  }

  // Render the last update date
  function renderLastUpdateDate() {
    $timeStamp = $this->propertyUtils->retrieve($this->propertyLastUpdate);

    $this->clockUtils->setWebsiteLocale();
    $dateFormat = $this->clockUtils->getDateFormat();
    // Forcing the string to a number
    $localDate = ucwords(strftime($dateFormat, (1 *$timeStamp)));

    return($localDate);
  }

  // Render a message indicating when the web site was last updated
  function renderLastUpdate() {
    $str = "\n<div class='last_update'>";

    $str .= $this->websiteText[0] . ' ' . $this->renderLastUpdateDate();

    $str .= "</div>";

    return($str);
  }

}

?>
