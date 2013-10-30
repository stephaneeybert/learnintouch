<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$email = $userUtils->checkUserLogin();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $message = LibEnv::getEnvHttpPOST("message");

  $message = LibString::cleanString($message);

  // The message is required
  if (!$message) {
    array_push($warnings, $websiteText[7]);
  }

  if (count($warnings) == 0) {

    $twitterUtils->postNotification($message);

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;

  }

}

if (!$formSubmitted) {
  $message = LibEnv::getEnvHttpGET("message");
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[4]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form id='post' name='post' action='$gTwitterUrl/post.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[6] *</div>";
$str .= "\n<div class='system_field'><textarea class='system_input' id='message' name='message' cols='50' rows='4'>$message</textarea></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['post'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[23]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

print($templateUtils->renderPopup($str));

?>
