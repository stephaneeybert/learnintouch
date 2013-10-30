<?PHP

require_once("website.php");

$mlText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
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
    array_push($warnings, $mlText[10]);
  } else if (!LibEmail::validate($toEmail)) {
    // The email must have an email format
    array_push($warnings, $mlText[38]);
  } else if (!LibEmail::validateDomain($toEmail)) {
    // The email domain must be registered as a mail domain
    array_push($warnings, $mlText[44]);
  } else if (!$fromEmail) {
    // The email is required
    array_push($warnings, $mlText[12]);
  } else if (!LibEmail::validate($fromEmail)) {
    // The email must have an email format
    array_push($warnings, $mlText[39]);
  } else if (!LibEmail::validateDomain($fromEmail)) {
    // The email domain must be registered as a mail domain
    array_push($warnings, $mlText[45]);
  }

  if (!$fromName) {
    $fromName = $fromEmail;
  }

  if (!$toName) {
    $toName = $toEmail;
  }

  if (count($warnings) == 0) {

    if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
      $headline = $newsStory->getHeadline();
      $excerpt = $newsStory->getExcerpt();
      $releaseDate = $newsStory->getReleaseDate();
      $editor = $newsStoryUtils->renderEditorName($newsStory);
    }

    $strLink = "$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId";
    $websiteName = $profileUtils->getProfileValue("website.name");

    $emailSubject = "$mlText[19] $fromName";

    $emailBody = "$mlText[20] $toName,"
      . "<br /><br /><br />$mlText[13] $fromName."
      . "<br /><br />$mlText[8]"
      . "<br /><br /><a href='$strLink' $gJSNoStatus>$headline</a>"
      . "<br /><br />$excerpt"
      . "<br /><br />" . nl2br($message)
      . "<br /><br /><br />$mlText[18]"
      . "<br /><br />$websiteName";

    LibEmail::sendMail($toEmail, $toName, $emailSubject, $emailBody, $fromEmail, $fromName);

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

$newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");
if (!$newsStoryId) {
  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
}
if (!$newsStoryId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

// Init the unset variables
if (!$formSubmitted) {
  $headline = '';
  $excerpt = '';
  $releaseDate = '';
  $editor = '';
  $toName = '';
  $toEmail = '';
  $fromName = '';
  $fromEmail = '';
  $message = '';
}

if ($newsStoryId) {
  if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
    $headline = $newsStory->getHeadline();
    $excerpt = $newsStory->getExcerpt();
    $releaseDate = $newsStory->getReleaseDate();
    $editor = $newsStoryUtils->renderEditorName($newsStory);
  }
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form action='$PHP_SELF' method='post'>";

$str .= "<div class='system_label'>$mlText[1]</div>";
$str .= "<div class='system_field'>$headline<br />$releaseDate $editor</div>";

$str .= "<div class='system_label'>$mlText[2]</div>";
$str .= "<div class='system_field'>$excerpt</div>";

$label = $userUtils->getTipPopup($mlText[7], $mlText[17], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='toEmail' size='30' maxlength='255' value='$toEmail' /></div>";

$label = $userUtils->getTipPopup($mlText[6], $mlText[16], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='toName' size='30' maxlength='50' value='$toName' /></div>";

$label = $userUtils->getTipPopup($mlText[5], $mlText[15], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='fromEmail' size='30' maxlength='255' value='$fromEmail' /></div>";

$label = $userUtils->getTipPopup($mlText[4], $mlText[14], 300, 140);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='fromName' size='30' maxlength='50' value='$fromName' /></div>";

$label = $userUtils->getTipPopup($mlText[23], $mlText[24], 300, 140);
$str .= "<div class='system_label' style='vertical-align:top;'>$label</div>";
$str .= "<div class='system_field'><textarea class='system_input' name='message' cols='30' rows='6'>$message</textarea></div>";

$str .= "<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' /></div>";

$str .= "\n<input type='hidden' name='newsStoryId' value='$newsStoryId' />";
$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

print($templateUtils->renderPopup($str));

?>
