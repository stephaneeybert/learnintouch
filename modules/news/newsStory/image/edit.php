<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsStoryImageId = LibEnv::getEnvHttpPOST("newsStoryImageId");
  $description = LibEnv::getEnvHttpPOST("description");

  $description = LibString::cleanString($description);

  if ($newsStoryImage = $newsStoryImageUtils->selectById($newsStoryImageId)) {
    $newsStoryId = $newsStoryImage->getNewsStoryId();
    $newsStoryImage->setDescription($description);
    $newsStoryImageUtils->update($newsStoryImage);
  }

  $str = LibHtml::urlRedirect("$gNewsUrl/newsStory/image/admin.php?newsStoryId=$newsStoryId");
  printContent($str);
  return;

} else {

  $newsStoryImageId = LibEnv::getEnvHttpGET("newsStoryImageId");

  $description = '';
  if ($newsStoryImageId) {
    if ($newsStoryImage = $newsStoryImageUtils->selectById($newsStoryImageId)) {
      $newsStoryId = $newsStoryImage->getNewsStoryId();
      $description = $newsStoryImage->getDescription();
    }
  }

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/image/admin.php?newsStoryId=$newsStoryId");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<textarea name='description' cols='50' rows='6'>$description</textarea>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('newsStoryImageId', $newsStoryImageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
