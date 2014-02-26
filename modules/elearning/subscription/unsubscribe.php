<?php

require_once("website.php");

$preferenceUtils->init($userUtils->preferences);

$email = $userUtils->checkUserLogin();

$userId = '';
if ($user = $userUtils->selectByEmail($email)) {
  $userId = $user->getId();
}

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$systemDate = $clockUtils->getSystemDate();
$systemDateTime = $clockUtils->getSystemDateTime();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  $unsubscribe = LibEnv::getEnvHttpPOST("unsubscribe");

  // The subscription is required
  if (!$elearningSubscriptionId) {
    array_push($warnings, $websiteText[4]);
  }

  // The confirm checkbox must be ticked
  if (!$unsubscribe) {
    array_push($warnings, $websiteText[6]);
  }

  // Check that the course allows the unsubscription
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $elearningCourseId = $elearningSubscription->getCourseId();
    if ($course = $elearningCourseUtils->selectById($elearningCourseId)) {
      $autoUnsubscription = $course->getAutoUnsubscription();
      if (!$autoUnsubscription) {
        array_push($warnings, $websiteText[5]);
      }
    }
  }

  if (count($warnings) == 0) {

    if ($unsubscribe) {
      $elearningSubscriptionUtils->deleteSubscription($elearningSubscriptionId);

      $str = LibHtml::urlRedirect("$gElearningUrl/subscription/display_participant_subscriptions.php");
      printContent($str);
      exit;
    }

  }

} else {

  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

}

$courseName = '';
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $elearningCourseId = $elearningSubscription->getCourseId();
  if ($course = $elearningCourseUtils->selectById($elearningCourseId)) {
    $courseName = $course->getName();
  }
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='unsubscribe' id='unsubscribe' action='$gElearningUrl/subscription/unsubscribe.php' method='post'>";

$label = $userUtils->getTipPopup($websiteText[1], $websiteText[2], 300, 400);
$str .= "<div class='system_label'>$label</div>";
$str .= "<div class='system_field'>$courseName";
$str .= "<input type='checkbox' name='unsubscribe' value='1' />";
$str .= "</div>";

$str .= "<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['unsubscribe'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[3]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
