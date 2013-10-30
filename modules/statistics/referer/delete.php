<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_STATISTICS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $statisticsRefererId = LibEnv::getEnvHttpPOST("statisticsRefererId");

  // Delete
  $statisticsRefererUtils->delete($statisticsRefererId);

  $str = LibHtml::urlRedirect("$gStatisticsUrl/referer/admin.php");
  printContent($str);
  return;

  } else {

  $statisticsRefererId = LibEnv::getEnvHttpGET("statisticsRefererId");

  if ($statisticsReferer = $statisticsRefererUtils->selectById($statisticsRefererId)) {
    $name = $statisticsReferer->getName();
    $description = $statisticsReferer->getDescription();
    $url = $statisticsReferer->getUrl();
    }

  $panelUtils->setHeader($mlText[0], "$gStatisticsUrl/referer/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $url);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('statisticsRefererId', $statisticsRefererId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
