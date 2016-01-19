<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();
$imageFileSize = $shopItemImageUtils->imageFileSize;
$imageFilePath = $shopItemImageUtils->imageFilePath;
$imageFileUrl = $shopItemImageUtils->imageFileUrl;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $shopItemImageId = LibEnv::getEnvHttpPOST("shopItemImageId");
  $shopItemId = LibEnv::getEnvHttpPOST("shopItemId");
  $imageWidth = LibEnv::getEnvHttpPOST("imageWidth");

  // Get the file characteristics
  // Note how the form parameter "userfile" creates several variables
  $uploaded_file = LibEnv::getEnvHttpFILE("userfile");
  $userfile = $uploaded_file['tmp_name'];
  $userfile_name = $uploaded_file['name'];
  $userfile_type = $uploaded_file['type'];
  $userfile_size = $uploaded_file['size'];

  // Clean up the filename
  $userfile_name = LibString::stripNonFilenameChar($userfile_name);

  // Check if a file has been specified...
  if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->checkImageFileType($userfile_name)) {
    // Check if the image file name has a correct file type
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $imageFileSize)) {
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imageFilePath)) {
    // Check if the file has been copied to the directory
    array_push($warnings, $str);
  }

  // Update the image
  $image = $userfile_name;

  if (count($warnings) == 0) {

    if ($imageWidth) {
      if ($fileUploadUtils->isImageType($shopItemImageUtils->imageFilePath . $image) && !$fileUploadUtils->isGifImage($shopItemImageUtils->imageFilePath . $image)) {
        LibImage::resizeImageToWidth($shopItemImageUtils->imageFilePath . $image, $imageWidth);
      }
    }

    if ($shopItemImageId) {
      if ($shopItemImage = $shopItemImageUtils->selectById($shopItemImageId)) {
        $shopItemImage->setImage($image);
        $shopItemImageUtils->update($shopItemImage);
      }
    } else if ($shopItemId && !$shopItemImageId) {
      $shopItemImage = new ShopItemImage();
      $shopItemImage->setImage($image);
      $shopItemImage->setShopItemId($shopItemId);

      // Get the next list order
      $listOrder = $shopItemImageUtils->getNextListOrder($shopItemId);
      $shopItemImage->setListOrder($listOrder);

      $shopItemImageUtils->insert($shopItemImage);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

} else {

  $imageWidth = $shopItemUtils->getImageWidth();

}

$shopItemImageId = LibEnv::getEnvHttpGET("shopItemImageId");
if (!$shopItemImageId) {
  $shopItemImageId = LibEnv::getEnvHttpPOST("shopItemImageId");
}

$shopItemId = LibEnv::getEnvHttpGET("shopItemId");
if (!$shopItemId) {
  $shopItemId = LibEnv::getEnvHttpPOST("shopItemId");
}

$image = '';
if ($shopItemImage = $shopItemImageUtils->selectById($shopItemImageId)) {
  $image = $shopItemImage->getImage();
}

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openMultipartForm($PHP_SELF);

if ($image) {
  $fileUrl = $imageFileUrl . "/" . $image;
  $strImage = "<img src='$fileUrl' $gJSNoStatus title=''></img>";
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), $strImage);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $image);
}

$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[4], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='imageWidth' value='$imageWidth' size='5' maxlength='5'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageFileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $imageFileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopItemImageId', $shopItemImageId);
$panelUtils->addHiddenField('shopItemId', $shopItemId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
