<?PHP

require_once("website.php");

$mlText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningResultId = LibEnv::getEnvHttpPOST("elearningResultId");
  $toName = LibEnv::getEnvHttpPOST("toName");
  $toEmail = LibEnv::getEnvHttpPOST("toEmail");
  $fromName = LibEnv::getEnvHttpPOST("fromName");
  $fromEmail = LibEnv::getEnvHttpPOST("fromEmail");
  $message = LibEnv::getEnvHttpPOST("message");

  $toName = LibString::cleanString($toName);
  $toEmail = LibString::cleanString($toEmail);
  $fromName = LibString::cleanString($fromName);
  $fromEmail = LibString::cleanString($fromEmail);
  $message = LibString::cleanString($message);

  // The email is required
  if (!$toEmail) {
    array_push($warnings, $mlText[40]);
  } else if (!LibEmail::validate($toEmail)) {
    // The email must have an email format
    array_push($warnings, $mlText[38]);
  } else if (!LibEmail::validateDomain($toEmail)) {
    // The email domain must be registered as a mail domain
    array_push($warnings, $mlText[44]);
  } else if (!$fromEmail) {
    // The email is required
    array_push($warnings, $mlText[21]);
  } else if (!LibEmail::validate($fromEmail)) {
    // The email must have an email format
    array_push($warnings, $mlText[39]);
  } else if (!LibEmail::validateDomain($fromEmail)) {
    // The email domain must be registered as a mail domain
    array_push($warnings, $mlText[45]);
  }

  // The email is case insensitive
  $toEmail = strtolower($toEmail);
  $fromEmail = strtolower($fromEmail);

  if (!$toName) {
    $toName = $toEmail;
  }

  if (!$fromName) {
    $fromName = $fromEmail;
  }

  if (count($warnings) == 0) {

    if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
      $elearningSubscriptionId = $elearningResult->getSubscriptionId();
      $elearningExerciseId = $elearningResult->getElearningExerciseId();
      $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
      $exerciseTime = $clockUtils->dateTimeToSystemTime($elearningResult->getExerciseDate());
      $firstname = $elearningResult->getFirstname();
      $lastname = $elearningResult->getLastname();
      $email = $elearningResult->getEmail();
    }

    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $name = $elearningExercise->getName();
      $description = $elearningExercise->getDescription();
    }

    $websiteName = $profileUtils->getProfileValue("website.name");

    $emailSubject = "$mlText[19] $websiteName";

    $strResult = $elearningResultUtils->renderResult($elearningResultId, true);

    $attachedImages = $elearningResultUtils->getEmailAttachment($strResult);

    $strResult = $elearningResultUtils->replaceImageTags($strResult);

    if ($fromName) {
      $strFromName = "<a href='mailto:$fromEmail'>$fromName</a>";
    } else {
      $strFromName = "<a href='mailto:$fromEmail'>$fromEmail</a>";
    }

    $emailBody = $templateUtils->renderDefaultModelCssPageProperties();

    $emailBody .= "\n<div class='system'>"
      . "<div class='system_email_content'>";

    $emailBody .= "$mlText[20] $toName,"
      . "<br /><br />$strFromName $mlText[13] " . $name . ' ' . $description;

    if ($message) {
      $emailBody .= "<br />" . nl2br($message);
    }

    $emailBody .= "<br /><br />" . $strResult
      . "<br /><br />$mlText[18]"
      . "<br /><br />$websiteName";

    $emailBody .= '</div></div>';

    if ($toEmail) {
      LibEmail::sendMail($toEmail, $toName, $emailSubject, $emailBody, $fromEmail, $fromName, $attachedImages);
    }

    // Collect the email addresses
    $preferenceUtils->init($mailUtils->preferences);
    $mailCollectAll = $preferenceUtils->getValue("MAIL_COLLECT_ALL");
    if ($mailCollectAll) {
      $mailAddressUtils->subscribe($toEmail);
      $mailAddressUtils->subscribe($fromEmail);
    }

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

}

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");
if (!$elearningResultId) {
  $elearningResultId = LibEnv::getEnvHttpPOST("elearningResultId");
}
if (!$elearningResultId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

// Init the unset variables
if (!$formSubmitted) {
  $elearningSubscriptionId = '';
  $elearningExerciseId = '';
  $exerciseDate = '';
  $exerciseTime = '';
  $firstname = '';
  $lastname = '';
  $email = '';
  $name = '';
  $description = '';
  $message = '';
  $toName = '';
  $toEmail = '';
  $fromName = '';
  $fromEmail = '';
}

if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
  $elearningSubscriptionId = $elearningResult->getSubscriptionId();
  $elearningExerciseId = $elearningResult->getElearningExerciseId();
  $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
  $exerciseTime = $clockUtils->dateTimeToSystemTime($elearningResult->getExerciseDate());
  $firstname = $elearningResult->getFirstname();
  $lastname = $elearningResult->getLastname();
  $email = $elearningResult->getEmail();
}

// Get the exercise details
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $name = $elearningExercise->getName();
  $description = $elearningExercise->getDescription();
}

// Get the participant details
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $userId = $elearningSubscription->getUserId();
  $elearningSessionId = $elearningSubscription->getSessionId();
  if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $email = $user->getEmail();
    }
  }
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form action='$PHP_SELF' method='post'>";

$str .= "<div class='system_label'>$mlText[1]</div>";
$str .= "<div class='system_field'>$name</div>";

$str .= "<div class='system_label'>$mlText[3]</div>";
$str .= "<div class='system_field'>$description</div>";

$str .= "<div class='system_label'>$mlText[10]</div>";
$str .= "<div class='system_field'>$firstname $lastname $email</div>";

$str .= "<div class='system_label'>$mlText[25]</div>";
$str .= "<div class='system_field'>$exerciseDate $exerciseTime</div>";

$label = $userUtils->getTipPopup($mlText[7], $mlText[17], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='toEmail' size='30' maxlength='255' value='$toEmail' /></div>";

$label = $userUtils->getTipPopup($mlText[8], $mlText[16], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='toName' size='30' maxlength='50' value='$toName' /></div>";

$label = $userUtils->getTipPopup($mlText[11], $mlText[15], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='fromEmail' size='30' maxlength='255' value='$fromEmail' /></div>";

$label = $userUtils->getTipPopup($mlText[12], $mlText[14], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='fromName' size='30' maxlength='50' value='$fromName' /></div>";

$label = $userUtils->getTipPopup($mlText[23], $mlText[24], 300, 140);
$str .= "<div class='system_label' style='vertical-align:top;'>$label</div>";
$str .= "<div class='system_field'><textarea class='system_input' name='message' cols='30' rows='6'>$message</textarea></div>";

$str .= "<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' /></div>";

$str .= "\n<input type='hidden' name='elearningResultId' value='$elearningResultId' />";
$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

print($templateUtils->renderPopup($str));

?>
