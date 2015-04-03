<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningScoringRangeId = LibEnv::getEnvHttpPOST("elearningScoringRangeId");

  $elearningScoringRangeUtils->deleteScoringRange($elearningScoringRangeId);

  $str = LibHtml::urlRedirect("$gElearningUrl/scoring/range/admin.php");
  printContent($str);
  return;

} else {

  $elearningScoringRangeId = LibEnv::getEnvHttpGET("elearningScoringRangeId");

  $currentLanguageCode = $languageUtils->getCurrentAdminLanguageCode();

  $upperRange = '';
  $score = '';
  if ($elearningScoringRange = $elearningScoringRangeUtils->selectById($elearningScoringRangeId)) {
    $upperRange = $elearningScoringRange->getUpperRange();
    $score = $languageUtils->getTextForLanguage($elearningScoringRange->getScore(), $currentLanguageCode);
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/scoring/range/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $upperRange);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $score);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningScoringRangeId', $elearningScoringRangeId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
