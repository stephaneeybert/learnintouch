<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navlinkItemId = LibEnv::getEnvHttpPOST("navlinkItemId");

  // Delete
  $navlinkItemUtils->delete($navlinkItemId);

  $str = LibHtml::urlRedirect("$gNavlinkUrl/admin.php");
  printContent($str);
  return;

  } else {

  $navlinkItemId = LibEnv::getEnvHttpGET("navlinkItemId");

  if ($navlinkItem = $navlinkItemUtils->selectById($navlinkItemId)) {
    $text = $navlinkItem->getText();
    $description = $navlinkItem->getDescription();
    }

  $panelUtils->setHeader($mlText[0], "$gNavlinkUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $text);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('navlinkItemId', $navlinkItemId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
