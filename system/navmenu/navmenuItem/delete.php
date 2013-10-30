<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navmenuItemId = LibEnv::getEnvHttpPOST("navmenuItemId");

  // Delete
  $navmenuItemUtils->delete($navmenuItemId);

  $str = LibHtml::urlRedirect("$gNavmenuUrl/admin.php");
  printContent($str);
  return;

  } else {

  $navmenuItemId = LibEnv::getEnvHttpGET("navmenuItemId");

  if ($navmenuItem = $navmenuItemUtils->selectById($navmenuItemId)) {
    $name = $navmenuItem->getName();
    $description = $navmenuItem->getDescription();
    }

  $panelUtils->setHeader($mlText[0], "$gNavmenuUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('navmenuItemId', $navmenuItemId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
