<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningResultRangeId = LibEnv::getEnvHttpPOST("elearningResultRangeId");

  $elearningResultRangeUtils->delete($elearningResultRangeId);

  $str = LibHtml::urlRedirect("$gElearningUrl/result/range/admin.php");
  printContent($str);
  return;

} else {

  $elearningResultRangeId = LibEnv::getEnvHttpGET("elearningResultRangeId");

  $upperRange = '';
  $grade = '';
  if ($elearningResultRange = $elearningResultRangeUtils->selectById($elearningResultRangeId)) {
    $upperRange = $elearningResultRange->getUpperRange();
    $grade = $elearningResultRange->getGrade();
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/result/range/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $upperRange);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $grade);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningResultRangeId', $elearningResultRangeId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
