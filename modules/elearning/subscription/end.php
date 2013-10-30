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

  if ($subscriptionClose) {
    $subscriptionClose = $clockUtils->localToSystemDate($subscriptionClose);
  }

  // The close date must be after the subscription date
  if ($subscriptionClose) {
    // Validate the date
    if (!$clockUtils->isLocalNumericDateValid($subscriptionClose)) {
      array_push($warnings, $mlText[2] . ' ' . $clockUtils->getDateNumericFormatTip());
    }

    if ($clockUtils->systemDateIsGreater($subscriptionDate, $subscriptionClose)) {
      array_push($warnings, $mlText[3]);
    }
  }

  if (count($warnings) == 0) {

    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $elearningSubscription->setSubscriptionClose($subscriptionClose);
      $elearningSubscriptionUtils->update($elearningSubscription);
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

  $userId = '';
  $subscriptionDate = '';
  $subscriptionClose = '';
  if ($elearningSubscriptionId) {
    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $subscriptionDate = $elearningSubscription->getSubscriptionDate();
      $subscriptionClose = $elearningSubscription->getSubscriptionClose();
      $userId = $elearningSubscription->getUserId();
    }
  }

}

$subscriptionClose = $clockUtils->systemToLocalNumericDate($subscriptionClose);

// Get the user name
$userName = '';
if ($user = $userUtils->selectById($userId)) {
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='subscriptionClose' id='subscriptionClose' value='$subscriptionClose' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('subscriptionDate', $subscriptionDate);
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#subscriptionClose").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#subscriptionClose").datepicker({ dateFormat:'dd-mm-yy' });
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
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
