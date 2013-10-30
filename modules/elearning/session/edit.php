<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $openDate = LibEnv::getEnvHttpPOST("openDate");
  $closeDate = LibEnv::getEnvHttpPOST("closeDate");
  $closed = LibEnv::getEnvHttpPOST("closed");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $openDate = LibString::cleanString($openDate);
  $closeDate = LibString::cleanString($closeDate);
  $closed = LibString::cleanString($closed);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[14]);
  }

  // The name must not already exist
  if (!$elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
    if ($elearningSession = $elearningSessionUtils->selectByName($name)) {
      array_push($warnings, $mlText[13]);
    }
  }

  // Validate the opening date
  if ($openDate && !$clockUtils->isLocalNumericDateValid($openDate)) {
    array_push($warnings, $mlText[22] . " " . $clockUtils->getDateNumericFormatTip());
  }

  // Validate the close date
  if ($closeDate && !$clockUtils->isLocalNumericDateValid($closeDate)) {
    array_push($warnings, $mlText[22] . " " . $clockUtils->getDateNumericFormatTip());
  }

  if ($openDate) {
    $openDate = $clockUtils->localToSystemDate($openDate);
  } else {
    $openDate = $clockUtils->getSystemDate();
  }

  if ($closeDate) {
    $closeDate = $clockUtils->localToSystemDate($closeDate);
  }

  // The end date must be after the opening date
  if ($closeDate && $openDate && $clockUtils->systemDateIsGreater($openDate, $closeDate)) {
    array_push($warnings, $mlText[19]);
  }

  // If the session has no course yet then check that it has one specified
  if (!$elearningSessionUtils->sessionHasCourses($elearningSessionId) && !$elearningCourseId) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
      $elearningSession->setName($name);
      $elearningSession->setDescription($description);
      $elearningSession->setOpenDate($openDate);
      $elearningSession->setCloseDate($closeDate);
      $elearningSession->setClosed($closed);
      $elearningSessionUtils->update($elearningSession);
    } else {
      $elearningSession = new ElearningSession();
      $elearningSession->setName($name);
      $elearningSession->setDescription($description);
      $elearningSession->setOpenDate($openDate);
      $elearningSession->setCloseDate($closeDate);
      $elearningSession->setClosed($closed);
      $elearningSessionUtils->insert($elearningSession);
      $elearningSessionId = $elearningSessionUtils->getLastInsertId();
    }

    if (!$elearningSessionUtils->sessionHasCourses($elearningSessionId) && $elearningSessionId && $elearningCourseId) {
      $elearningSessionCourse = new ElearningSessionCourse();
      $elearningSessionCourse->setElearningSessionId($elearningSessionId);
      $elearningSessionCourse->setElearningCourseId($elearningCourseId);
      $elearningSessionCourseUtils->insert($elearningSessionCourse);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/session/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningSessionId = LibEnv::getEnvHttpGET("elearningSessionId");

  if (!$elearningSessionId) {
    $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  }

  $name = '';
  $description = '';
  $openDate = '';
  $closeDate = '';
  $closed = '';
  $elearningCourseId = '';
  if ($elearningSessionId) {
    if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
      $name = $elearningSession->getName();
      $description = $elearningSession->getDescription();
      $openDate = $elearningSession->getOpenDate();
      $closeDate = $elearningSession->getCloseDate();
      $closed = $elearningSession->getClosed();
    }
  }

}

if (!$clockUtils->systemDateIsSet($openDate)) {
  $openDate = $clockUtils->getSystemDate();
}

$openDate = $clockUtils->systemToLocalNumericDate($openDate);

if ($clockUtils->systemDateIsSet($closeDate)) {
  $closeDate = $clockUtils->systemToLocalNumericDate($closeDate);
} else {
  $closeDate = '';
}

if ($closed == '1') {
  $checkedClosed = "CHECKED";
} else {
  $checkedClosed = '';
}

// If the session has no course yet then offer to add one
if (!$elearningSessionUtils->sessionHasCourses($elearningSessionId)) {
  $elearningCourses = $elearningCourseUtils->selectAll();
  // If no courses exist yet then redirect to the course creation page
  if (count($elearningCourses) == 0) {
    array_push($warnings, $mlText[11]);
  }
  $elearningCourseList = Array('' => '');
  foreach ($elearningCourses as $elearningCourse) {
    $wId = $elearningCourse->getId();
    // Check that the course contains some exercises
    if ($elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($wId)) {
      $wName = $elearningCourse->getName();
      $elearningCourseList[$wId] = $wName;
    }
  }
  $strSelectCourse = LibHtml::getSelectList("elearningCourseId", $elearningCourseList, $elearningCourseId);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/session/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[15], $mlText[17], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[16], $mlText[18], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[12], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='openDate' id='openDate' value='$openDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[20], $mlText[21], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='closeDate' id='closeDate' value='$closeDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
if (!$elearningSessionUtils->sessionHasCourses($elearningSessionId)) {
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[5], $mlText[6], 300, 400);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectCourse);
}
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='closed' $checkedClosed value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#openDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#closeDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#openDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#closeDate").datepicker({ dateFormat:'dd-mm-yy' });
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
