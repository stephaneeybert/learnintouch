<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");

if (!$newsStoryId) {
  $newsStoryId = LibSession::getSessionValue(NEWS_SESSION_NEWSSTORY);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSSTORY, $newsStoryId);
}

$newsStory = $newsStoryUtils->selectById($newsStoryId);
$headline = $newsStory->getHeadline();
$releaseDate = $newsStory->getReleaseDate();

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php");
$help = $popupUtils->getHelpPopup($mlText[13], 300, 300);
$panelUtils->setHelp($help);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[1]'>", "$gNewsUrl/newsStory/image/image.php?newsStoryId=$newsStoryId", 600, 600);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $headline, '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $releaseDate, '', '');
$panelUtils->addLine();

$imageList = array();

$newsStoryImages = $newsStoryImageUtils->selectByNewsStoryId($newsStoryId);
for ($i = 0; $i < count($newsStoryImages); $i++) {
  $newsStoryImage = $newsStoryImages[$i];
  $newsStoryImageId = $newsStoryImage->getId();
  $image = $newsStoryImage->getImage();
  $description = $newsStoryImage->getDescription();

  $strSwap = " <br><br><a href='$gNewsUrl/newsStory/image/swapup.php?newsStoryImageId=$newsStoryImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageLeft' title='$mlText[11]'></a>"
    . " <a href='$gNewsUrl/newsStory/image/swapdown.php?newsStoryImageId=$newsStoryImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageRight' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gNewsUrl/newsStory/image/edit.php?newsStoryImageId=$newsStoryImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[8]'>", "$gNewsUrl/newsStory/image/image.php?newsStoryImageId=$newsStoryImageId", 600, 600)
    . " <a href='$gNewsUrl/newsStory/image/delete.php?newsStoryImageId=$newsStoryImageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  if ($image && @file_exists($newsStoryImageUtils->imageFilePath . $image)) {
    $fileUploadUtils->loadLanguageTexts();
    if (!$fileUploadUtils->isGifImage($newsStoryImageUtils->imageFilePath . $image)) {
      // The image is created on the fly
      $filename = $newsStoryImageUtils->imageFilePath . $image;

      // Resize the image to the following width
      $preferenceUtils->init($newsStoryUtils->preferences);
      $width = $preferenceUtils->getValue("NEWS_STORY_IMAGE_SMALL_WIDTH");

      $filename = urlencode($filename);

      $imageSrc = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&width=" . $width . "&height=";
    } else {
      $imageSrc = "$newsStoryImageUtils->imageFileUrl/$image";
    }
    $strImg = "<img src='$imageSrc' border='0' width='$width' href='' title='$image'>";
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
