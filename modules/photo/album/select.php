<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);


$listPhotoAlbums = $photoAlbumUtils->getPhotoAlbumListIds();
array_unshift($listPhotoAlbums, '');
$strSelectPhotoAlbum = LibHtml::getSelectList("photoAlbumId", $listPhotoAlbums);

$cyclePhotoAlbums = $photoAlbumUtils->getPhotoAlbumCycleIds();
array_unshift($cyclePhotoAlbums, '');
$strSelectPhotoCycle = LibHtml::getSelectList("photoCycleId", $cyclePhotoAlbums);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");
  $photoCycleId = LibEnv::getEnvHttpPOST("photoCycleId");

  if ($photoAlbumId) {
    $str = $templateUtils->renderJsUpdate($photoAlbumId);
    printMessage($str);
  } else if ($photoCycleId) {
    $str = $templateUtils->renderJsUpdate($photoCycleId);
    printMessage($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectPhotoAlbum);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $strSelectPhotoCycle);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
