<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$preferenceUtils->init($photoUtils->preferences);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");

if (!$photoAlbumId) {
  $photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");
}

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(PHOTO_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(PHOTO_SESSION_SEARCH_PATTERN, $searchPattern);
}

if (!$photoAlbumId) {
  $photoAlbumId = LibSession::getSessionValue(PHOTO_SESSION_ALBUM);
} else {
  LibSession::putSessionValue(PHOTO_SESSION_ALBUM, $photoAlbumId);
}

if ($searchPattern) {
  $photoAlbumId = '';
  LibSession::putSessionValue(PHOTO_SESSION_ALBUM, '');
}

$searchPattern = LibString::cleanString($searchPattern);

$photoAlbums = $photoAlbumUtils->selectAll();
$photoAlbumList = Array('-1' => '');
foreach ($photoAlbums as $photoAlbum) {
  $wPhotoAlbumId = $photoAlbum->getId();
  $wName = $photoAlbum->getName();
  $photoAlbumList[$wPhotoAlbumId] = $wName;
}
$strSelect = LibHtml::getSelectList("photoAlbumId", $photoAlbumList, $photoAlbumId, true);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[13], 300, 200);

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$strCommand = "<a href='$gPhotoUrl/album/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAlbum' title='$mlText[6]'></a>"
  . " <a href='$gPhotoUrl/imageArchive.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageArchive' title='$mlText[4]'></a>"
  . " <a href='$gPhotoUrl/format/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageFormat' title='$mlText[5]'></a>"
  . " <a href='$gPhotoUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[20]'></a>";

$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '', $panelUtils->addCell($strCommand, "nbr"));

$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'>", "$gPhotoUrl/image.php", 600, 600);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $panelUtils->addCell($strSelect, "n"), '', $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->closeForm();

$jsColorbox = $colorboxUtils->renderJsColorbox() . $colorboxUtils->renderAdminColorbox();
$panelUtils->addContent($jsColorbox);

$albumName = '';
$folderName = '';
if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
  $albumName = $photoAlbum->getName();
  $folderName = $photoAlbum->getFolderName();
}

$listStep = $preferenceUtils->getValue("PHOTO_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $photos = $photoUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $photos = $photoUtils->selectByPhotoAlbum($photoAlbumId, $listIndex, $listStep);
}

$listNbItems = $photoUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$photoList = array();
for ($i = 0; $i < count($photos); $i++) {
  $photo = $photos[$i];
  $photoId = $photo->getId();
  $reference = $photo->getReference();
  $name = $photo->getName();
  $description = $photo->getDescription();
  $image = $photo->getImage();
  $photoAlbumId = $photo->getPhotoAlbum();

  if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
    $photoAlbumId = $photo->getPhotoAlbum();
    if (!$albumName) {
      $albumName = $photoAlbum->getName();
    }
    if (!$folderName) {
      $folderName = $photoAlbum->getFolderName();
    }
  }

  $strName = '';
  if ($reference) {
    $strName .= '<br><b>' . $mlText[12] . '</b> ' . $reference;
  }
  if ($name) {
    $strName .= '<br><b>' . $mlText[14] . '</b> ' . $name;
  }

  if ($folderName && $image && @file_exists($photoUtils->imagePath . $folderName . '/' . $image)) {

    $fileUploadUtils->loadLanguageTexts();
    if (!$fileUploadUtils->isGifImage($photoUtils->imagePath . $folderName . '/' . $image)) {
      // The image is created on the fly
      $filename = $photoUtils->imagePath . $folderName . '/' . $image;

      // Resize the image to the following width
      $width = $preferenceUtils->getValue("PHOTO_DEFAULT_MINI_WIDTH");
      $width = min($width, 200);

      $filename = urlencode($filename);
      $imageSrc = $gUtilsUrl . "/printImage.php?filename=" . $filename
        . "&width=" . $width . "&height=";
      $strImg = "<img src='$imageSrc' border='0' width='$width' href='' title='$image'>";
    } else {
      $imageSrc = "$photoUtils->imageUrl/$folderName/$image";
      $strImg = "<img src='$imageSrc' border='0' href='' title='$image'>";
    }
  } else {
    $strImg = "&nbsp;";
  }

  $strImg = "<a href='$photoUtils->imageUrl/$folderName/$image' rel='no_style_colorbox' title='$description'>$strImg</a>";

  $strSwap = " <br><a href='$gPhotoUrl/swapleft.php?photoId=$photoId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageLeft' title='$mlText[11]'></a> <a href='$gPhotoUrl/swapright.php?photoId=$photoId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageRight' title='$mlText[10]'></a>";

  $strCommand = " <a href='$gPhotoUrl/edit.php?photoId=$photoId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gPhotoUrl/image.php?photoId=$photoId", 600, 600)
    . " <a href='$gPhotoUrl/delete.php?photoId=$photoId' $gJSNoStatus>"
    . " <img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $photoList[$i] = $strImg . $strName . $strSwap . $strCommand . '<br/><br/>';
}

for ($i = 0; $i < count($photoList); $i = $i + 4) {
  $cell1 = LibUtils::getArrayValue($i, $photoList);
  $cell2 = LibUtils::getArrayValue($i+1, $photoList);
  $cell3 = LibUtils::getArrayValue($i+2, $photoList);
  $cell4 = LibUtils::getArrayValue($i+3, $photoList);

  $panelUtils->addLine($cell1, $cell2, $cell3, $cell4);
}

$str = $panelUtils->render();

printAdminPage($str);

?>
