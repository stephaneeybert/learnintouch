<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$preferenceUtils->init($elearningExerciseUtils->preferences);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  $elearningTeacherId = LibEnv::getEnvHttpPOST("elearningTeacherId");
  $userId = LibEnv::getEnvHttpPOST("userId");
  $subscriptionDate = LibEnv::getEnvHttpPOST("subscriptionDate");
  $subscriptionClose = LibEnv::getEnvHttpPOST("subscriptionClose");
  $userFromDate = LibEnv::getEnvHttpPOST("userFromDate");
  $userToDate = LibEnv::getEnvHttpPOST("userToDate");
  $watchLive = LibEnv::getEnvHttpPOST("watchLive");

  $subscriptionDate = LibString::cleanString($subscriptionDate);
  $subscriptionClose = LibString::cleanString($subscriptionClose);
  $userFromDate = LibString::cleanString($userFromDate);
  $userToDate = LibString::cleanString($userToDate);
  $watchLive = LibString::cleanString($watchLive);

  // The user is required
  if (!$userId && !$userFromDate && !$userToDate) {
    array_push($warnings, $mlText[6]);
  }

  // Validate the from and to dates
  if ($userFromDate && !$clockUtils->isLocalNumericDateValid($userFromDate)) {
    array_push($warnings, $mlText[5] . ' ' . $clockUtils->getDateNumericFormatTip());
  }
  if ($userToDate && !$clockUtils->isLocalNumericDateValid($userToDate)) {
    array_push($warnings, $mlText[5] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  // The session may be required
  $requireSession = $preferenceUtils->getValue("ELEARNING_REQUIRE_SESSION");
  if ($requireSession) {
    if (!$elearningSessionId) {
      array_push($warnings, $mlText[1]);
    }
  }

  // The course may be required
  $requireCourse = $preferenceUtils->getValue("ELEARNING_REQUIRE_COURSE");
  if ($requireCourse) {
    if (!$elearningCourseId) {
      array_push($warnings, $mlText[18]);
    }
  }

  // The class may be required
  $requireClass = $preferenceUtils->getValue("ELEARNING_REQUIRE_CLASS");
  if ($requireClass) {
    if (!$elearningClassId) {
      array_push($warnings, $mlText[20]);
    }
  }

  // The teacher may be required
  $requireTeacher = $preferenceUtils->getValue("ELEARNING_REQUIRE_TEACHER");
  if ($requireTeacher) {
    if (!$elearningTeacherId) {
      array_push($warnings, $mlText[21]);
    }
  }

  if ($subscriptionDate) {
    $subscriptionDate = $clockUtils->localToSystemDate($subscriptionDate);
  }
  if ($subscriptionClose) {
    $subscriptionClose = $clockUtils->localToSystemDate($subscriptionClose);
  }
  if ($userFromDate) {
    $userFromDate = $clockUtils->localToSystemDate($userFromDate);
  }
  if ($userToDate) {
    $userToDate = $clockUtils->localToSystemDate($userToDate);
  }

  // The to date must be after the from date
  if ($userToDate && $userFromDate && $clockUtils->systemDateIsGreater($userFromDate, $userToDate)) {
    array_push($warnings, $mlText[23]);
  }

  if (count($warnings) == 0) {

    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $elearningSubscription->setUserId($userId);
      $elearningSubscription->setTeacherId($elearningTeacherId);
      $elearningSubscription->setSessionId($elearningSessionId);
      $elearningSubscription->setCourseId($elearningCourseId);
      $elearningSubscription->setClassId($elearningClassId);
      $elearningSubscription->setSubscriptionDate($subscriptionDate);
      $elearningSubscription->setSubscriptionClose($subscriptionClose);
      $elearningSubscription->setWatchLive($watchLive);
      $elearningSubscriptionUtils->update($elearningSubscription);
    } else {
      if ($userId) {
        $userIds = array($userId);
      } else {
        if ($users = $userUtils->selectByCreationDateTime($userFromDate, $userToDate)) {
          $userIds = array();
          foreach ($users as $user) {
            $userId = $user->getId();
            array_push($userIds, $userId);
          }
        }
      }
      foreach ($userIds as $userId) {
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
        $elearningSubscription->setSubscriptionDate($subscriptionDate);
        $elearningSubscription->setSubscriptionClose($subscriptionClose);
        $elearningSubscription->setWatchLive($watchLive);
        $elearningSubscriptionUtils->insert($elearningSubscription);
        $elearningSubscriptionId = $elearningSubscriptionUtils->getLastInsertId();
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/subscription/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

  if (!$elearningSubscriptionId) {
    $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  }

  $elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");

  // Check that the number of maximum subscriptions is not exceeded
  if ($website = $websiteUtils->selectBySystemName($websiteUtils->getSetupWebsiteName())) {
    $websiteId = $website->getId();
    $maxSubscriptions = $websiteOptionUtils->getOptionValue(OPTION_ELEARNING, $websiteId);
    $systemDate = $clockUtils->getSystemDate();
    $openedSubscriptions = $elearningSubscriptionUtils->countOpenedSubscriptions($systemDate);

    if ($maxSubscriptions && $openedSubscriptions >= $maxSubscriptions) {
      array_push($warnings, $mlText[10]);
    }
  }

  $userId = '';
  $elearningCourseId = '';
  $elearningTeacherId = '';
  $userFromDate = '';
  $userToDate = '';
  $subscriptionDate = $clockUtils->getSystemDate();
  $subscriptionClose = '';
  $watchLive = '1';
  if ($elearningSubscriptionId) {
    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $userId = $elearningSubscription->getUserId();
      $elearningTeacherId = $elearningSubscription->getTeacherId();
      $elearningCourseId = $elearningSubscription->getCourseId();
      $elearningClassId = $elearningSubscription->getClassId();
      // Do not overwrite the newly selected session
      if (!$elearningSessionId) {
        $elearningSessionId = $elearningSubscription->getSessionId();
      }
      $subscriptionDate = $elearningSubscription->getSubscriptionDate();
      $subscriptionClose = $elearningSubscription->getSubscriptionClose();
      $watchLive = $elearningSubscription->getWatchLive();
    }
  }

}

$subscriptionDate = $clockUtils->systemToLocalNumericDate($subscriptionDate);
$subscriptionClose = $clockUtils->systemToLocalNumericDate($subscriptionClose);

if ($clockUtils->systemDateIsSet($userFromDate)) {
  $userFromDate = $clockUtils->systemToLocalNumericDate($userFromDate);
} else {
  $userFromDate = '';
}
if ($clockUtils->systemDateIsSet($userToDate)) {
  $userToDate = $clockUtils->systemToLocalNumericDate($userToDate);
} else {
  $userToDate = '';
}

$courseName = '';
if ($course = $elearningCourseUtils->selectById($elearningCourseId)) {
  $courseName = $course->getName();
}

$sessionName = '';
if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
  $sessionName = $elearningSession->getName();
}

if ($watchLive == '1') {
  $checkedWatchLive = "CHECKED";
} else {
  $checkedWatchLive = '';
}

$elearningClasses = $elearningClassUtils->selectAll();

$strWarning = '<div id="warnings">';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}
$strWarning .= '</div>';

// Get the participant's name
$userName = '';
if ($user = $userUtils->selectById($userId)) {
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
}

// Get the teacher's name
$teacherName = '';
if ($elearningTeacher = $elearningTeacherUtils->selectById($elearningTeacherId)) {
  $teacherUserId = $elearningTeacher->getUserId();
  if ($teacherUser = $userUtils->selectById($teacherUserId)) {
    $teacherName = $teacherUser->getFirstname() . ' ' . $teacherUser->getLastname();
  }
}

// Get the class's name
$className = '';
if ($class = $elearningClassUtils->selectById($elearningClassId)) {
  $className = $class->getName();
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));

$requireSession = $preferenceUtils->getValue("ELEARNING_REQUIRE_SESSION");
if ($requireSession) {
  $strLabel = $mlText[2] . ' *';
} else {
  $strLabel = $mlText[2];
}
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[4], $mlText[15], 300, 300);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gUserUrl/suggestUsers.php", "userName", "userId");
if (!$elearningSubscriptionId) {
  $strJsSuggest .= <<<HEREDOC
<script type='text/javascript'>
$(document).ready(function() {
$("#userId").change(function() {
  if ($("#userId")) {
    var userId = $("#userId").attr("value");
    var userName = $("#userName").attr("value");
    var url = "$gUserUrl/get.php?userId="+userId;
    ajaxAsynchronousRequest(url, retrieveFullUserName);
  }
});
});

function retrieveFullUserName(responseText) {
  var response = eval('(' + responseText + ')');
  var user = response;
  if (user) {
    var url = "$gElearningUrl/subscription/suggest.php?term="+user.name+"&displayClass=1";
    ajaxAsynchronousRequest(url, warnOfAnotherSubscription);
  }
}

function warnOfAnotherSubscription(responseText) {
  var response = eval('(' + responseText + ')');
  var elearningSubscriptions = response;
  if (elearningSubscriptions.length > 0) {
    var existingSubscriptionsMessage = "$mlText[29]";
    for (i = 0; i < elearningSubscriptions.length; i++) {
      var elearningSubscriptionId = elearningSubscriptions[i].id;
      var name = elearningSubscriptions[i].value;
      existingSubscriptionsMessage += " - " + name;
    }
    $("#warnings").text(existingSubscriptionsMessage);
  }
}
</script>
HEREDOC;
}
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('userId', $userId);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='userName' value='$userName' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[16], $mlText[17], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='userFromDate' id='userFromDate' value='$userFromDate' size='12' maxlength='10'> $mlText[22] <input type='text' name='userToDate' id='userToDate' value='$userToDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip(), "b"));
$panelUtils->addLine();
$strJsSuggestSession = $commonUtils->ajaxAutocomplete("$gElearningUrl/session/suggest.php", "sessionName", "elearningSessionId");
$panelUtils->addContent($strJsSuggestSession);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$label = $popupUtils->getTipPopup($strLabel, $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' id='sessionName' name='$sessionName' value='$sessionName' />", "n"));
$panelUtils->addLine();
$requireCourse = $preferenceUtils->getValue("ELEARNING_REQUIRE_COURSE");
if ($requireCourse) {
  $strLabel = $mlText[19] . ' *';
} else {
  $strLabel = $mlText[19];
}
$label = $popupUtils->getTipPopup($strLabel, $mlText[25], 300, 300);
$strJsSuggestCourse = $commonUtils->ajaxAutocomplete("$gElearningUrl/course/suggest.php", "courseName", "elearningCourseId");
$panelUtils->addContent($strJsSuggestCourse);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' id='courseName' name='$courseName' value='$courseName' />", "n"));
$panelUtils->addLine();
$requireTeacher = $preferenceUtils->getValue("ELEARNING_REQUIRE_TEACHER");
if ($requireTeacher) {
  $strLabel = $mlText[11] . ' *';
} else {
  $strLabel = $mlText[11];
}
$label = $popupUtils->getTipPopup($strLabel, $mlText[7], 300, 300);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/teacher/suggest.php", "teacherName", "suggestedElearningTeacherId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addContent("<input type='hidden' id='suggestedElearningTeacherId' name='elearningTeacherId' value='$elearningTeacherId' />");
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='teacherName' value='$teacherName' />");
$panelUtils->addLine();
$requireClass = $preferenceUtils->getValue("ELEARNING_REQUIRE_CLASS");
if ($requireClass) {
  $strLabel = $mlText[3] . ' *';
} else {
  $strLabel = $mlText[3];
}
$label = $popupUtils->getTipPopup($strLabel, $mlText[12], 300, 300);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='className' value='$className' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[27], $mlText[28], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='watchLive' $checkedWatchLive value='1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[24], "nbr"), "<input type='text' name='subscriptionDate' id='subscriptionDate' value='$subscriptionDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[26], "nbr"), "<input type='text' name='subscriptionClose' id='subscriptionClose' value='$subscriptionClose' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#subscriptionDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#subscriptionClose").datepicker({ dateFormat:'mm/dd/yy' });
  $("#userFromDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#userToDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#subscriptionDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#subscriptionClose").datepicker({ dateFormat:'dd-mm-yy' });
  $("#userFromDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#userToDate").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;

$defaultSubscriptionDuration = $preferenceUtils->getValue("ELEARNING_DEFAULT_SUBSCRIPTION_DURATION");
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  var defaultSubscriptionDuration = parseInt('$defaultSubscriptionDuration');
  if (defaultSubscriptionDuration > 0) {
    $('#subscriptionDate').datepicker().change(function() {
      var subscriptionDate = $.datepicker.parseDate('dd-mm-yy', $('#subscriptionDate').val());
      var subscriptionClose = new Date();
      subscriptionClose.setDate(subscriptionDate.getDate() + defaultSubscriptionDuration);
      $('#subscriptionClose').datepicker('setDate', subscriptionClose);
    });
  }
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
