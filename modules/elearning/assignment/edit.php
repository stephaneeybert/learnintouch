<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningAssignmentId = LibEnv::getEnvHttpPOST("elearningAssignmentId");
  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $onlyOnce = LibEnv::getEnvHttpPOST("onlyOnce");
  $openingDate = LibEnv::getEnvHttpPOST("openingDate");
  $closingDate = LibEnv::getEnvHttpPOST("closingDate");

  $onlyOnce = LibString::cleanString($onlyOnce);
  $openingDate = LibString::cleanString($openingDate);
  $closingDate = LibString::cleanString($closingDate);

  // The subscription is required
  if (!$elearningSubscriptionId) {
    array_push($warnings, $mlText[3]);
  }

  // The exercise is required
  if (!$elearningExerciseId) {
    array_push($warnings, $mlText[4]);
  }

  // Check the assignment is not already assigned to the participant
  if ($elearningAssignment = $elearningAssignmentUtils->selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId)) {
    if (!$elearningAssignmentId || ($elearningAssignmentId != $elearningAssignment->getId())) {
      array_push($warnings, $mlText[7]);
    }
  } 

  // Validate the opening date
  if ($openingDate && !$clockUtils->isLocalNumericDateValid($openingDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  // Validate the closing date
  if ($closingDate && !$clockUtils->isLocalNumericDateValid($closingDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  if ($openingDate) {
    $openingDate = $clockUtils->localToSystemDate($openingDate);
  }
  if ($closingDate) {
    $closingDate = $clockUtils->localToSystemDate($closingDate);
  }

  // The closing date must be after the opening date
  if ($closingDate && $openingDate && $clockUtils->systemDateIsGreater($openingDate, $closingDate)) {
    array_push($warnings, $mlText[34]);
  }

  if (count($warnings) == 0) {

    if ($elearningAssignment = $elearningAssignmentUtils->selectById($elearningAssignmentId)) {
      $elearningAssignment->setElearningSubscriptionId($elearningSubscriptionId);
      $elearningAssignment->setElearningExerciseId($elearningExerciseId);
      $elearningAssignment->setOnlyOnce($onlyOnce);
      $elearningAssignment->setOpeningDate($openingDate);
      $elearningAssignment->setClosingDate($closingDate);
      $elearningAssignmentUtils->update($elearningAssignment);
    } else {
      $elearningAssignment = new ElearningAssignment();
      $elearningAssignment->setElearningSubscriptionId($elearningSubscriptionId);
      $elearningAssignment->setElearningExerciseId($elearningExerciseId);
      $elearningAssignment->setOnlyOnce($onlyOnce);
      $elearningAssignment->setOpeningDate($openingDate);
      $elearningAssignment->setClosingDate($closingDate);
      $elearningAssignmentUtils->insert($elearningAssignment);

      if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $elearningSubscription->setWatchLive(true);
        $elearningSubscriptionUtils->update($elearningSubscription);
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/assignment/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
  $elearningAssignmentId = LibEnv::getEnvHttpGET("elearningAssignmentId");
  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

  $onlyOnce = '';
  $openingDate = '';
  $closingDate = '';
  if ($elearningAssignment = $elearningAssignmentUtils->selectById($elearningAssignmentId)) {
    $elearningSubscriptionId = $elearningAssignment->getElearningSubscriptionId();
    $elearningExerciseId = $elearningAssignment->getElearningExerciseId();
    $onlyOnce = $elearningAssignment->getOnlyOnce();
    $openingDate = $elearningAssignment->getOpeningDate();
    $closingDate = $elearningAssignment->getClosingDate();
  }

}

$firstname = '';
$lastname = '';
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $userId = $elearningSubscription->getUserId();

  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
  }
}

$participantName = $firstname;
if ($lastname) {
  if ($firstname) {
    $participantName .= ' ';
  }
  $participantName .= $lastname;
}

$exerciseName = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $exerciseName = $elearningExercise->getName();
}

$openingDate = $clockUtils->systemToLocalNumericDate($openingDate);
$closingDate = $clockUtils->systemToLocalNumericDate($closingDate);

if ($onlyOnce == '1') {
  $checkedOnlyOnce = "CHECKED";
} else {
  $checkedOnlyOnce = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/assignment/admin.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/subscription/suggest.php", "participantName", "elearningSubscriptionId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' id='participantName' name='participantName' value='$participantName' />");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExercises.php", "elearningExerciseName", "elearningExerciseId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' id='elearningExerciseName' value='$exerciseName' size='40' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='onlyOnce' $checkedOnlyOnce value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='openingDate' id='openingDate' value='$openingDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[13], $mlText[14], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='closingDate' id='closingDate' value='$closingDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('elearningAssignmentId', $elearningAssignmentId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#openingDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#closingDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#openingDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#closingDate").datepicker({ dateFormat:'dd-mm-yy' });
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
