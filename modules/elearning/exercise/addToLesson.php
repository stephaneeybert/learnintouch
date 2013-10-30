<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");

  if (!$elearningLessonParagraphId) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $elearningLessonParagraph->setElearningExerciseId($elearningExerciseId);
      $elearningLessonParagraphUtils->update($elearningLessonParagraph);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

}

$name = '';
$description = '';
if ($elearningExerciseId) {
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
  }
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/lesson/suggestLessonParagraphs.php", "elearningLessonParagraphHeadline", "elearningLessonParagraphId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningLessonParagraphId', '');
$label = $popupUtils->getTipPopup($mlText[2], $mlText[1], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='elearningLessonParagraphHeadline' value='' size='30'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
