<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($guestbookUtils->preferences);

$warnings = array();

// Check that the user is logged in
if ($preferenceUtils->getValue("GUESTBOOK_SECURED")) {
  $email = $userUtils->checkUserLogin();

  // Get the user id
  $userId = '';
  if ($user = $userUtils->selectByEmail($email)) {
    $userId = $user->getId();
  }

  // Check if the user exists
  if (!$userId) {
    // Close the user session to delete all user session variables
    $userUtils->closeUserSession();

    $str = LibHtml::urlRedirect("$gUserUrl/login.php");
    printContent($str);
    exit;
  }
} else {
  $email = '';
  $userId = '';
}

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $body = LibEnv::getEnvHttpPOST("body");
  $email = LibEnv::getEnvHttpPOST("email");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $securityCode = LibEnv::getEnvHttpPOST("securityCode");

  $body = LibString::cleanString($body);
  $email = LibString::cleanString($email);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $securityCode = LibString::cleanString($securityCode);

  // The email is required if no user has logged in
  if (!$userId) {
    if (!$email && !$firstname && !$lastname) {
      array_push($warnings, $websiteText[8]);
    } else if ($email && !LibEmail::validate($email)) {
      // The email must have an email format
      array_push($warnings, $websiteText[9]);
    } else if ($email && !LibEmail::validateDomain($email)) {
      // The email domain must be registered as a mail domain
      array_push($warnings, $websiteText[12]);
    }
  }

  // Check for a security code
  if ($preferenceUtils->getValue("GUESTBOOK_SECURITY_CODE")) {
    $randomSecurityCode = LibSession::getSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE);
    if (!$securityCode) {
      // The security code is required
      array_push($warnings, $websiteText[33]);
    } else if ($securityCode != $randomSecurityCode) {
      // The security code is incorrect
      array_push($warnings, $websiteText[34]);
    }
  }

  // The body is required
  if (!$body) {
    array_push($warnings, $websiteText[7]);
  }

  // Get the system date
  $releaseDateTime = $clockUtils->getSystemDateTime();

  if (count($warnings) == 0) {
    $guestbook = new Guestbook();
    $guestbook->setBody($body);
    $guestbook->setReleaseDate($releaseDateTime);
    if ($userId) {
      $guestbook->setUserId($userId);
    } else {
      $guestbook->setEmail($email);
      $guestbook->setFirstname($firstname);
      $guestbook->setLastname($lastname);
    }
    $guestbookUtils->insert($guestbook);
    $guestbookId = $guestbookUtils->getLastInsertId();

    // Collect the email address
    $mailPreferenceUtils->init($mailUtils->preferences);
    $mailCollectAll = $mailPreferenceUtils->getValue("MAIL_COLLECT_ALL");
    if ($mailCollectAll) {
      $mailAddressUtils->subscribe($email);
    }

    // Send an email when a contact message is received
    $mailOnPost = $preferenceUtils->getValue("GUESTBOOK_MAIL_ON_POST");
    if ($mailOnPost) {
      $websiteName = $profileUtils->getProfileValue("website.name");
      $siteEmail = $profileUtils->getProfileValue("website.email");

      if ($firstname || $lastname) {
        $strName = "$firstname $lastname";
      } else {
        $strName = $email;
      }

      // Create a one-time url for the link in the email
      // Generate a unique token and keep it for later use
      $tokenName = GUESTBOOK_TOKEN_NAME;
      $tokenDuration = $adminUtils->getLoginTokenDuration();
      $tokenValue = $uniqueTokenUtils->create($tokenName, $tokenDuration);

      $emailSubject = "$websiteText[16] $websiteName $websiteText[21] $strName";

      $emailBody = "$websiteText[15] $websiteName $websiteText[19] $strName." . "<br /><br />";

      $includeMessage = $preferenceUtils->getValue("GUESTBOOK_INCLUDE_MESSAGE");
      if ($includeMessage) {
        $emailBody .= $websiteText[20] . ' ' . nl2br($body);
      }

      $emailBody .= "<br /><br /><a href='$gGuestbookUrl/read.php?guestbookId=$guestbookId&tokenName=$tokenName&tokenValue=$tokenValue&siteEmail=$siteEmail' $gJSNoStatus>$websiteText[14]</a> $websiteText[17]";

      if (LibEmail::validate($siteEmail)) {
        LibEmail::sendMail($siteEmail, $websiteName, $emailSubject, $emailBody, $siteEmail, $websiteName);
      }
    }

    $str = LibHtml::urlRedirect("$gGuestbookUrl/display.php");
    printContent($str);
    return;
  }

}

if (!$formSubmitted) {
  $email = '';
  $firstname = '';
  $lastname = '';
  $body = '';
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[4]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$securityCodeFontSize = $templateUtils->getSecurityCodeFontSize($gIsPhoneClient);

$str .= "\n<form id='guestbook_post' name='guestbook_post' action='$gGuestbookUrl/post.php' method='post'>";

if (!$userId) {
  $str .= "\n<div class='system_label'>$websiteText[3] *</div>";
  $str .= "\n<div class='system_field'><input class='system_input' type='text' name='email' size='25' maxlength='255' value='$email' /></div>";

  $str .= "\n<div class='system_label'>$websiteText[13]</div>";
  $str .= "\n<div class='system_field'><input class='system_input' type='text' name='firstname' size='25' maxlength='255' value='$firstname' /></div>";

  $str .= "\n<div class='system_label'>$websiteText[11]</div>";
  $str .= "\n<div class='system_field'><input class='system_input' type='text' name='lastname' size='25' maxlength='255' value='$lastname' /></div>";
}

$securityCode = $preferenceUtils->getValue("GUESTBOOK_SECURITY_CODE");
if ($securityCode) {
  $randomSecurityCode = LibUtils::generateUniqueId();
  LibSession::putSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE, $randomSecurityCode);
  $url = $gUtilsUrl . "/printNumberImage.php?securityCodeFontSize=$securityCodeFontSize";
  $label = $userUtils->getTipPopup($websiteText[10], $websiteText[18], 300, 200);

  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'><input class='system_input' type='text' name='securityCode' size='5' maxlength='5' value='' /> <img src='$url' title='$websiteText[22]' alt='' />";
  $str .= "\n</div>";
}

$str .= "\n<div class='system_label'>$websiteText[6] *</div>";
$str .= "\n<div class='system_field'><textarea class='system_input' id='body' name='body' rows='4'>$body</textarea></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['guestbook_post'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[23]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
