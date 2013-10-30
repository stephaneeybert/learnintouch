<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navbarItemId = LibEnv::getEnvHttpPOST("navbarItemId");

  $navbarItemUtils->deleteItem($navbarItemId);

  $str = LibHtml::urlRedirect("$gNavbarUrl/admin.php");
  printContent($str);
  return;

} else {

  $navbarItemId = LibEnv::getEnvHttpGET("navbarItemId");

  if ($navbarItem = $navbarItemUtils->selectById($navbarItemId)) {
    $name = $navbarItem->getName();
    $image = $navbarItem->getImage();
    $url = $navbarItem->getUrl();
    $description = $navbarItem->getDescription();
    $navbarLanguageId = $navbarItem->getNavbarLanguageId();
  }

  $panelUtils->setHeader($mlText[0], "$gNavbarUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('navbarItemId', $navbarItemId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
