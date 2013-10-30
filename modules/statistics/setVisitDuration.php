<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_STATISTICS);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $visitDuration = LibEnv::getEnvHttpPOST("visitDuration");

  $visitDuration = LibString::cleanString($visitDuration);

  $statisticsVisitUtils->setVisitDuration($visitDuration);

  $str = LibHtml::urlRedirect("$gStatisticsUrl/admin.php");
  printContent($str);
  return;

  } else {

  $visitDuration = $statisticsVisitUtils->getVisitDuration();

  $panelUtils->setHeader($mlText[0], "$gStatisticsUrl/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[1], 300, 500);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type='text' name='visitDuration' value='$visitDuration' size='10' maxlength='10'> $mlText[3]");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
