<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  require_once($gTemplateDesignPath . "element/edit_controller.php");

}

$templateElementLanguageId = LibEnv::getEnvHttpGET("templateElementLanguageId");
$photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

$name = '';
if ($photoAlbumId) {
  if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
    $name = $photoAlbum->getName();
  }
}

$photoAlbums = $photoAlbumUtils->selectAll();
$photoAlbumList = Array('' => '');
foreach ($photoAlbums as $photoAlbum) {
  $wPhotoAlbumId = $photoAlbum->getId();
  $wName = $photoAlbum->getName();
  $photoAlbumList[$wPhotoAlbumId] = $wName;
}
$strSelect = LibHtml::getSelectList("objectId", $photoAlbumList, $photoAlbumId);

$panelUtils->setHeader($mlText[0]);
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $strSelect);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('templateElementLanguageId', $templateElementLanguageId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
