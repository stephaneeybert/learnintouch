<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningScoringId = LibEnv::getEnvHttpPOST("elearningScoringId");

  if ($elearningExercises = $elearningExerciseUtils->selectByScoringId($elearningScoringId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $elearningScoringUtils->deleteScoring($elearningScoringId);

    $str = LibHtml::urlRedirect("$gElearningUrl/scoring/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningScoringId = LibEnv::getEnvHttpGET("elearningScoringId");

}

$name = '';
$description = '';
if ($scoring = $elearningScoringUtils->selectById($elearningScoringId)) {
  $name = $scoring->getName();
  $description = $scoring->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/scoring/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningScoringId', $elearningScoringId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
