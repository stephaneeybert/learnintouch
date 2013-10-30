<?PHP

require_once("website.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormat.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDao.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDB.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatUtils.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

if (!$photoAlbumId) {
  $photoAlbumId = LibSession::getSessionValue(PHOTO_SESSION_ALBUM);
} else {
  LibSession::putSessionValue(PHOTO_SESSION_ALBUM, $photoAlbumId);
}

$name = '';
if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
  $name = $photoAlbum->getName();
}

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/album/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 300);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gPhotoUrl/format/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageFormat' title='$mlText[4]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $panelUtils->addCell($name, "n"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();
$strCommand = "<a href='$gPhotoUrl/album/format/edit.php?photoAlbumId=$photoAlbumId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[7]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$photoAlbumFormats = $photoAlbumFormatUtils->selectByPhotoAlbumId($photoAlbumId);
if (count($photoAlbumFormats) > 0) {
  foreach ($photoAlbumFormats as $photoAlbumFormat) {
    $photoAlbumFormatId = $photoAlbumFormat->getId();
    $photoFormatId = $photoAlbumFormat->getPhotoFormatId();
    $price = $photoAlbumFormat->getPrice();

    $formatName = '';
    if ($photoFormat = $photoFormatUtils->selectById($photoFormatId)) {
      $formatName = $photoFormat->getName();
    }

    $strCommand = "<a href='$gPhotoUrl/album/format/edit.php?photoAlbumFormatId=$photoAlbumFormatId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
      . " <a href='$gPhotoUrl/album/format/delete.php?photoAlbumFormatId=$photoAlbumFormatId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

    $panelUtils->addLine($formatName, $price, $panelUtils->addCell($strCommand, "nbr"));
  }
}

$str = $panelUtils->render();

printAdminPage($str);

?>
