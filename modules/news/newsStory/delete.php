<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Get the form data
  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");

  $newsStoryUtils->deleteNewsStory($newsStoryId);

  $str = LibHtml::urlRedirect("$gNewsUrl/newsStory/admin.php");
  printContent($str);
  return;

} else {

  $newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");

  // Render the headline
  if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
    $headline = $newsStory->getHeadline();
    $releaseDate = $newsStory->getReleaseDate();
  }

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$headline");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "$releaseDate");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('newsStoryId', $newsStoryId);
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
