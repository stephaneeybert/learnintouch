<?PHP

require_once("website.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormat.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDao.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDB.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatUtils.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoAlbumFormatId = LibEnv::getEnvHttpPOST("photoAlbumFormatId");
  $photoFormatId = LibEnv::getEnvHttpPOST("photoFormatId");
  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");
  $price = LibEnv::getEnvHttpPOST("price");

  $price = LibString::cleanString($price);

  // The format is required
  if (!$photoFormatId) {
    array_push($warnings, $mlText[7]);
  }

  // The price is required
  if (!$price) {
    array_push($warnings, $mlText[6]);
  }

  // The price must be numerical
  if (!is_numeric($price)) {
    array_push($warnings, $mlText[2]);
  }

  // The format must not be already used by another format
  if ($photoAlbumFormat = $photoAlbumFormatUtils->selectByPhotoFormatIdAndPhotoAlbumId($photoFormatId, $photoAlbumId)) {
    $wPhotoAlbumFormatId = $photoAlbumFormat->getId();
    if ($wPhotoAlbumFormatId != $photoAlbumFormatId) {
      array_push($warnings, $mlText[8]);
    }
  }

  if (count($warnings) == 0) {

    if ($photoAlbumFormat = $photoAlbumFormatUtils->selectById($photoAlbumFormatId)) {
      $photoAlbumFormat->setPhotoFormatId($photoFormatId);
      $photoAlbumFormat->setPrice($price);
      $photoAlbumFormatUtils->update($photoAlbumFormat);
    } else {
      $photoAlbumFormat = new PhotoAlbumFormat();
      $photoAlbumFormat->setPhotoAlbumId($photoAlbumId);
      $photoAlbumFormat->setPhotoFormatId($photoFormatId);
      $photoAlbumFormat->setPrice($price);
      $photoAlbumFormatUtils->insert($photoAlbumFormat);
    }

    $str = LibHtml::urlRedirect("$gPhotoUrl/album/format/admin.php");
    printContent($str);
    return;

  }

} else {

  $photoAlbumFormatId = LibEnv::getEnvHttpGET("photoAlbumFormatId");
  $photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

  $photoFormatId = '';
  $price = '';
  if ($photoAlbumFormatId) {
    if ($photoAlbumFormat = $photoAlbumFormatUtils->selectById($photoAlbumFormatId)) {
      $photoAlbumId = $photoAlbumFormat->getPhotoAlbumId();
      $photoFormatId = $photoAlbumFormat->getPhotoFormatId();
      $price = $photoAlbumFormat->getPrice();
    }
  }

}

$albumName = '';
if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
  $albumName = $photoAlbum->getName();
}

$listFormats = array('' => '');
$photoFormats = $photoFormatUtils->selectAll();
foreach ($photoFormats as $photoFormat) {
  $wPhotoFormatId = $photoFormat->getId();
  $name = $photoFormat->getName();
  $listFormats[$wPhotoFormatId] = $name;
}
$strSelectFormat = LibHtml::getSelectList("photoFormatId", $listFormats, $photoFormatId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/album/format/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $albumName);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelectFormat);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='price' value='$price' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('photoAlbumFormatId', $photoAlbumFormatId);
$panelUtils->addHiddenField('photoAlbumId', $photoAlbumId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
