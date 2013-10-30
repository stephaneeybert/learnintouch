<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");
  $emptyAlbum = LibEnv::getEnvHttpPOST("emptyAlbum");

  $emptyAlbum = LibString::cleanString($emptyAlbum);

  // Check if the album must be emptied
  if ($emptyAlbum) {
    $photoUtils->emptyAlbum($photoAlbumId);
  }

  // Delete the photo photoAlbum only if it is not used
  if ($photos = $photoUtils->selectByPhotoAlbum($photoAlbumId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $photoAlbumUtils->deleteAlbum($photoAlbumId);

    $str = LibHtml::urlRedirect("$gPhotoUrl/album/admin.php");
    printContent($str);
    return;

  }

} else {

  $photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

}

if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
  $name = $photoAlbum->getName();
  $event = $photoAlbum->getEvent();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/album/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $event);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type=checkbox name='emptyAlbum' value='1'> $mlText[4]");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('photoAlbumId', $photoAlbumId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
