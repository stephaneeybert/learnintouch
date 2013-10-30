<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopItemImageId = LibEnv::getEnvHttpPOST("shopItemImageId");

  // Delete
  $shopItemImageUtils->delete($shopItemImageId);

  $str = LibHtml::urlRedirect("$gShopUrl/item/image/admin.php");
  printContent($str);
  return;

  } else {

  $shopItemImageId = LibEnv::getEnvHttpGET("shopItemImageId");

  if ($shopItemImage = $shopItemImageUtils->selectById($shopItemImageId)) {
    $image = $shopItemImage->getImage();
    $description = $shopItemImage->getDescription();
    }

  $panelUtils->setHeader($mlText[0], "$gShopUrl/item/image/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('shopItemImageId', $shopItemImageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
