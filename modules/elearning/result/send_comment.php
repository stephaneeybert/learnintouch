<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$templateUtils->storeRequestedUrl();

$email = $userUtils->checkUserLogin();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningResultId = LibEnv::getEnvHttpPOST("elearningResultId");
  $comment = LibEnv::getEnvHttpPOST("comment");

  $comment = LibString::cleanString($comment);

  // The comment is required
  if (!$comment) {
    array_push($warnings, $websiteText[5]);
  }

  // Make sure a teacher cannot comment on a result that is not his
  if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
    $elearningSubscriptionId = $elearningResult->getSubscriptionId();
    if ($elearningSubscriptionId) {
      if ($user = $userUtils->selectByEmail($email)) {
        $userId = $user->getId();
        if ($elearningTeacher = $elearningTeacherUtils->selectByUserId($userId)) {
          if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
            // Only if the subscription has a teacher, make sure it is the same teacher as the logged in one
            if ($elearningSubscription->getTeacherId() && $elearningTeacher->getId() != $elearningSubscription->getTeacherId()) {
              array_push($warnings, $websiteText[7]);
            }
          }
        }
      }
    }
  }

  if (count($warnings) == 0) {

    if ($elearningResult) {
      $elearningResult->setComment($comment);
      $elearningResult->setHideComment(false);
      $elearningResultUtils->update($elearningResult);

      $elearningResultUtils->sendCommentToParticipant($elearningResult);
    }

    $str = $websiteText[2];
    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");

  }

} else {

  $elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

  $comment = '';
  if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
    $comment = $elearningResult->getComment();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_comment'>$websiteText[1]</div>";

$str .= "\n<form name='comment_form' id='comment_form' action='$gElearningUrl/result/send_comment.php' method='post'>";

$label = $userUtils->getTipPopup($websiteText[4], $websiteText[6], 300, 400);

$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><textarea class='system_input' name='comment' cols='20' rows='5'>$comment</textarea></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['comment_form'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[3]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningResultId' value='$elearningResultId' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
