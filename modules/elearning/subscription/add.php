<?php

require_once("website.php");

$preferenceUtils->init($userUtils->preferences);

if (!$elearningCourseUtils->autoSubscriptions()) {
  $str = LibHtml::urlRedirect("$gElearningUrl/subscription/display_participant_subscriptions.php");
  printContent($str);
  return;
}

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

  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  $elearningTeacherId = LibEnv::getEnvHttpPOST("elearningTeacherId");

  // The session may be required
  if ($preferenceUtils->getValue("ELEARNING_REQUIRE_SESSION")) {
    if (!$elearningSessionId) {
      array_push($warnings, $websiteText[6]);
    }
  }

  // The course may be required
  if ($preferenceUtils->getValue("ELEARNING_REQUIRE_COURSE") || $preferenceUtils->getValue("ELEARNING_REQUIRE_SESSION")) {
    if (!$elearningCourseId) {
      array_push($warnings, $websiteText[7]);
    }
  }

  // The participant can subscribe only once to a course 
  if ($preferenceUtils->getValue("ELEARNING_REQUIRE_SESSION")) {
    if ($elearningSubscription = $elearningSubscriptionUtils->selectByUserIdAndCourseIdAndSessionId($userId, $elearningCourseId, $elearningSessionId)) {
      array_push($warnings, $websiteText[14]);
    }
  } else {
    if ($elearningSubscription = $elearningSubscriptionUtils->selectByUserIdAndCourseId($userId, $elearningCourseId)) {
      array_push($warnings, $websiteText[13]);
    }
  }

  // The class may be required
  if ($preferenceUtils->getValue("ELEARNING_REQUIRE_CLASS")) {
    if (!$elearningClassId) {
      array_push($warnings, $websiteText[8]);
    }
  }

  // The teacher may be required
  if ($preferenceUtils->getValue("ELEARNING_REQUIRE_TEACHER")) {
    if (!$elearningTeacherId) {
      array_push($warnings, $websiteText[9]);
    }
  }

  // Check that the number of maximum subscriptions is not exceeded
  if ($website = $websiteUtils->selectBySystemName($websiteUtils->getSetupWebsiteName())) {
    $websiteId = $website->getId();
    $maxSubscriptions = $websiteOptionUtils->getOptionValue(OPTION_ELEARNING, $websiteId);
    $systemDate = $clockUtils->getSystemDate();
    $openedSubscriptions = $elearningSubscriptionUtils->countOpenedSubscriptions($systemDate);

    if ($maxSubscriptions && $openedSubscriptions >= $maxSubscriptions) {
      array_push($warnings, $websiteText[10]);
    }
  }

  if (count($warnings) == 0) {

    if ($user = $userUtils->selectById($userId)) {
      $subscribe = $user->getMailSubscribe();
      if (!$subscribe) {
        $user->setMailSubscribe(true);
        $userUtils->update($user);
      }
      $smsSubscribe = $user->getSmsSubscribe();
      if (!$smsSubscribe) {
        $user->setSmsSubscribe(true);
        $userUtils->update($user);
      }
    }

    $elearningSubscription = new ElearningSubscription();
    $elearningSubscription->setUserId($userId);
    $elearningSubscription->setTeacherId($elearningTeacherId);
    $elearningSubscription->setSessionId($elearningSessionId);
    $elearningSubscription->setCourseId($elearningCourseId);
    $elearningSubscription->setClassId($elearningClassId);
    $elearningSubscription->setSubscriptionDate($systemDateTime);
    $elearningSubscriptionUtils->insert($elearningSubscription);
    $elearningSubscriptionId = $elearningSubscriptionUtils->getLastInsertId();

    $str = LibHtml::urlRedirect("$gElearningUrl/subscription/display_participant_subscriptions.php");
    printContent($str);
    exit;

  }

} else {

  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  $elearningTeacherId = LibEnv::getEnvHttpPOST("elearningTeacherId");

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

if ($preferenceUtils->getValue("ELEARNING_REQUIRE_SESSION")) {
  if ($elearningSessions = $elearningSessionUtils->selectNotClosed($systemDate)) {
    $str .= "\n<form name='select_session' id='select_session' action='$gElearningUrl/subscription/add.php' method='post'>";
    $elearningSessionList = Array('' => '');
    foreach ($elearningSessions as $elearningSession) {
      $wId = $elearningSession->getId();
      $name = $elearningSession->getName();
      $elearningSessionList[$wId] = $name;
    }
    $strSelectSession = LibHtml::getSelectList("elearningSessionId", $elearningSessionList, $elearningSessionId, true);

    $str .= "<div class='system_label'>$websiteText[3]</div>";
    $str .= "<div class='system_field'>$strSelectSession</div>";

    $str .= "\n<input type='hidden' name='elearningTeacherId' value='$elearningTeacherId' />";
    $str .= "\n<input type='hidden' name='elearningClassId' value='$elearningClassId' />";

    $str .= "\n</form>";
  }
}

$str .= "\n<form name='register_form' id='register_form' action='$gElearningUrl/subscription/add.php' method='post'>";

if ($elearningSessionId) {
  $elearningCourses = $elearningCourseUtils->selectBySessionIdAndAutoSubscription($elearningSessionId);
} else {
  $elearningCourses = $elearningCourseUtils->selectAutoSubscription();
}
if ($elearningCourses) {
  $elearningCourseList = Array('' => '');
  foreach ($elearningCourses as $elearningCourse) {
    $wId = $elearningCourse->getId();
    if ($elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($wId)) {
      $name = $elearningCourse->getName();
      $elearningCourseList[$wId] = $name;
    }
  }
  $strSelectCourse = LibHtml::getSelectList("elearningCourseId", $elearningCourseList, $elearningCourseId);

  $str .= "<div class='system_label'>$websiteText[4]</div>";
  $str .= "<div class='system_field'>$strSelectCourse</div>";
}

if ($preferenceUtils->getValue("ELEARNING_REQUIRE_CLASS")) {
  if ($elearningClasses = $elearningClassUtils->selectAll()) {
    $elearningClassList = Array('' => '');
    foreach ($elearningClasses as $elearningClass) {
      $wId = $elearningClass->getId();
      $name = $elearningClass->getName();
      $elearningClassList[$wId] = $name;
    }
    $strSelectClass = LibHtml::getSelectList("elearningClassId", $elearningClassList, $elearningClassId);

    $str .= "<div class='system_label'>$websiteText[2]</div>";
    $str .= "<div class='system_field'>$strSelectClass</div>";
  }
}

if ($preferenceUtils->getValue("ELEARNING_REQUIRE_TEACHER")) {
  if ($elearningTeachers = $elearningTeacherUtils->selectAll()) {
    $elearningTeacherList = Array('' => '');
    foreach ($elearningTeachers as $elearningTeacher) {
      $wTeacherId = $elearningTeacher->getId();
      $wFirstname = $elearningTeacherUtils->getFirstname($wTeacherId);
      $wLastname = $elearningTeacherUtils->getLastname($wTeacherId);
      $elearningTeacherList[$wTeacherId] = "$wFirstname $wLastname";
    }
    $strSelectTeacher = LibHtml::getSelectList("elearningTeacherId", $elearningTeacherList, $elearningTeacherId);

    $str .= "<div class='system_label'>$websiteText[1]</div>";
    $str .= "<div class='system_field'>$strSelectTeacher</div>";
  }
}

$str .= "<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['register_form'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[5]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningSessionId' value='$elearningSessionId' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
