<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsStoryImageId = LibEnv::getEnvHttpPOST("newsStoryImageId");

  // Delete
  $newsStoryImageUtils->delete($newsStoryImageId);

  $str = LibHtml::urlRedirect("$gNewsUrl/newsStory/image/admin.php");
  printContent($str);
  return;

} else {

  $newsStoryImageId = LibEnv::getEnvHttpGET("newsStoryImageId");

  if ($newsStoryImage = $newsStoryImageUtils->selectById($newsStoryImageId)) {
    $image = $newsStoryImage->getImage();
    $description = $newsStoryImage->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/image/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('newsStoryImageId', $newsStoryImageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
