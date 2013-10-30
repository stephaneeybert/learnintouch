<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_STATISTICS);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $statisticsVisitUtils->resetCounterTime();

  $str = LibHtml::urlRedirect("$gStatisticsUrl/admin.php");
  printContent($str);
  return;

  } else {

  $currentDate = $clockUtils->systemToLocalNumericDate($statisticsVisitUtils->getCounterDate());
  $todayDate = $clockUtils->getLocalNumericDate();

  $panelUtils->setHeader($mlText[0], "$gStatisticsUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $currentDate);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $todayDate);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
