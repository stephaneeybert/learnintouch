<?PHP

require_once("website.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormat.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDao.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDB.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatUtils.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoAlbumFormatId = LibEnv::getEnvHttpPOST("photoAlbumFormatId");

  $photoAlbumFormatUtils->delete($photoAlbumFormatId);

  $str = LibHtml::urlRedirect("$gPhotoUrl/album/format/admin.php");
  printContent($str);
  return;

  } else {

  $photoAlbumFormatId = LibEnv::getEnvHttpGET("photoAlbumFormatId");

  $photoAlbumId = '';
  $photoFormatId = '';
  $price = '';
  if ($photoAlbumFormatId) {
    if ($photoAlbumFormat = $photoAlbumFormatUtils->selectById($photoAlbumFormatId)) {
      $photoAlbumId = $photoAlbumFormat->getPhotoAlbumId();
      $photoFormatId = $photoAlbumFormat->getPhotoFormatId();
      $price = $photoAlbumFormat->getPrice();
      }
    }

  $albumName = '';
  if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
    $albumName = $photoAlbum->getName();
    }

  $formatName = '';
  if ($photoFormat = $photoFormatUtils->selectById($photoFormatId)) {
    $formatName = $photoFormat->getName();
    }

  $panelUtils->setHeader($mlText[0], "$gPhotoUrl/album/format/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $albumName);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $formatName);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $price);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('photoAlbumFormatId', $photoAlbumFormatId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
