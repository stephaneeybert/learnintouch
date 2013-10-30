<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  require_once($gTemplateDesignPath . "element/edit_controller.php");

}

$templateElementLanguageId = LibEnv::getEnvHttpGET("templateElementLanguageId");
$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");

$title = '';
if ($newsPaperId) {
  if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
    $title = $newsPaper->getTitle();
  }
}

$photoAlbums = $newsPaperUtils->selectAll();
$photoAlbumList = Array('' => '');
foreach ($photoAlbums as $photoAlbum) {
  $wPhotoAlbumId = $photoAlbum->getId();
  $wName = $photoAlbum->getName();
  $photoAlbumList[$wPhotoAlbumId] = $wName;
}
$strSelect = LibHtml::getSelectList("objectId", $photoAlbumList, $newsPaperId);

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
