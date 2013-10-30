<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");

  if (count($warnings) == 0) {

    $elearningExercisePageUtils->deleteExercisePage($elearningExercisePageId);

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
    printMessage($str);
    return;

  }

}

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
if (!$elearningExercisePageId) {
  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
}

if ($elearningQuestion = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $name = $elearningQuestion->getName();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
if ($elearningExercisePageUtils->exercisePageHasResults($elearningExercisePageId)) {
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->addCell($mlText[3], "w"));
}
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExercisePageId', $elearningExercisePageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
