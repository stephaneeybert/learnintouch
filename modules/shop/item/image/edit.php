<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopItemImageId = LibEnv::getEnvHttpPOST("shopItemImageId");
  $description = LibEnv::getEnvHttpPOST("description");

  $description = LibString::cleanString($description);

  if ($shopItemImage = $shopItemImageUtils->selectById($shopItemImageId)) {
    $shopItemImage->setDescription($description);
    $shopItemImageUtils->update($shopItemImage);
  }

  $str = LibHtml::urlRedirect("$gShopUrl/item/image/admin.php");
  printContent($str);
  return;

} else {

  $shopItemImageId = LibEnv::getEnvHttpGET("shopItemImageId");

  $description = '';
  if ($shopItemImageId) {
    if ($shopItemImage = $shopItemImageUtils->selectById($shopItemImageId)) {
      $description = $shopItemImage->getDescription();
    }
  }

  $panelUtils->setHeader($mlText[0], "$gShopUrl/item/image/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<textarea name='description' cols='30' rows='5'>$description</textarea>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('shopItemImageId', $shopItemImageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
