<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$shopItemId = LibEnv::getEnvHttpGET("shopItemId");

if (!$shopItemId) {
  $shopItemId = LibSession::getSessionValue(SHOP_SESSION_ITEM);
} else {
  LibSession::putSessionValue(SHOP_SESSION_ITEM, $shopItemId);
}

$shopItem = $shopItemUtils->selectById($shopItemId);
$name = $shopItem->getName();
$reference = $shopItem->getReference();

$panelUtils->setHeader($mlText[0], "$gShopUrl/item/admin.php");
$help = $popupUtils->getHelpPopup($mlText[13], 300, 300);
$panelUtils->setHelp($help);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[1]'>", "$gShopUrl/item/image/image.php?shopItemId=$shopItemId", 600, 600);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $name, '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $reference, '', '');
$panelUtils->addLine();

$imageList = array();

$shopItemImages = $shopItemImageUtils->selectByShopItemId($shopItemId);
for ($i = 0; $i < count($shopItemImages); $i++) {
  $shopItemImage = $shopItemImages[$i];
  $shopItemImageId = $shopItemImage->getId();
  $image = $shopItemImage->getImage();
  $description = $shopItemImage->getDescription();

  $strSwap = " <br><br><a href='$gShopUrl/item/image/swapup.php?shopItemImageId=$shopItemImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageLeft' title='$mlText[11]'></a>"
    . " <a href='$gShopUrl/item/image/swapdown.php?shopItemImageId=$shopItemImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageRight' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gShopUrl/item/image/edit.php?shopItemImageId=$shopItemImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[8]'>", "$gShopUrl/item/image/image.php?shopItemImageId=$shopItemImageId", 600, 600)
    . " <a href='$gShopUrl/item/image/delete.php?shopItemImageId=$shopItemImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  if ($image && file_exists($shopItemImageUtils->imageFilePath . $image)) {
    // Resize the image to the following width
    $preferenceUtils->init($shopItemUtils->preferences);
    $width = $preferenceUtils->getValue("SHOP_DEFAULT_MINI_WIDTH");

    $fileUploadUtils->loadLanguageTexts();
    $imageSrc = "$shopItemImageUtils->imageFileUrl/$image";
    $strImg = "<img src='$imageSrc' border='0' href='' title='$image'>";
  } else {
    $strImg = "&nbsp;";
  }

  $imageList[$i] = $strImg . $strSwap . $strCommand;
}

for ($i = 0; $i < count($imageList); $i = $i + 4) {
  $cell1 = LibUtils::getArrayValue($i, $imageList);
  $cell2 = LibUtils::getArrayValue($i+1, $imageList);
  $cell3 = LibUtils::getArrayValue($i+2, $imageList);
  $cell4 = LibUtils::getArrayValue($i+3, $imageList);
  $panelUtils->addLine($cell1, $cell2, $cell3, $cell4);
}

$str = $panelUtils->render();

printAdminPage($str);

?>
