<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");

  if ($elearningExerciseId) {
    $str = $templateUtils->renderJsUpdate($elearningExerciseId);
    printMessage($str);

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  } else if ($elearningLessonId) {
    $str = $templateUtils->renderJsUpdate($elearningLessonId);
    printMessage($str);

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  } else {
    array_push($warnings, $mlText[1]);
  }

} else {

  $elearningExerciseId = '';
  $elearningLessonId = '';

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExerciseInternalLinks.php", "elearningExerciseName", "elearningExerciseId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' id='elearningExerciseName' value='' size='40' />");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/lesson/suggestLessonInternalLinks.php", "elearningLessonName", "elearningLessonId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' id='elearningLessonName' value='' size='40' />");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
