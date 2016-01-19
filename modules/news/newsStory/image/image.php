<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$imageFilePath = $newsStoryImageUtils->imageFilePath;
$imageFileUrl = $newsStoryImageUtils->imageFileUrl;
$imageFileSize = $newsStoryImageUtils->imageFileSize;

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $newsStoryImageId = LibEnv::getEnvHttpPOST("newsStoryImageId");
  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
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
  } else if ($str = $fileUploadUtils->checkMediaFileType($userfile_name)) {
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
      if ($fileUploadUtils->isImageType($newsStoryImageUtils->imageFilePath . $image) && !$fileUploadUtils->isGifImage($newsStoryImageUtils->imageFilePath . $image)) {
        LibImage::resizeImageToWidth($newsStoryImageUtils->imageFilePath . $image, $imageWidth);
      }
    }

    if ($newsStoryImageId) {
      if ($newsStoryImage = $newsStoryImageUtils->selectById($newsStoryImageId)) {
        $newsStoryImage->setImage($image);
        $newsStoryImageUtils->update($newsStoryImage);
      }
    } else if ($newsStoryId && !$newsStoryImageId) {
      $newsStoryImage = new NewsStoryImage();
      $newsStoryImage->setImage($image);
      $newsStoryImage->setNewsStoryId($newsStoryId);

      // Get the next list order
      $listOrder = $newsStoryImageUtils->getNextListOrder($newsStoryId);
      $newsStoryImage->setListOrder($listOrder);

      $newsStoryImageUtils->insert($newsStoryImage);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

} else {

  $imageWidth = $newsStoryUtils->getImageWidth();

}

$newsStoryImageId = LibEnv::getEnvHttpGET("newsStoryImageId");
if (!$newsStoryImageId) {
  $newsStoryImageId = LibEnv::getEnvHttpPOST("newsStoryImageId");
}

$newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");
if (!$newsStoryId) {
  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
}

$image = '';
if ($newsStoryImage = $newsStoryImageUtils->selectById($newsStoryImageId)) {
  $image = $newsStoryImage->getImage();
}

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openMultipartForm($PHP_SELF);

if ($image) {
  $fileUrl = "$imageFileUrl/$image";
  $strImage = "<img src='$fileUrl' $gJSNoStatus title=''></img>";
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $strImage);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='checkbox' name='deleteImage' value='1'>");
}

$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[4], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='imageWidth' value='$imageWidth' size='5' maxlength='5'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageFileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());

$panelUtils->addHiddenField('max_file_size', $imageFileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsStoryId', $newsStoryId);
$panelUtils->addHiddenField('newsStoryImageId', $newsStoryImageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
