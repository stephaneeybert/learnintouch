<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPublication/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[10], 300, 160);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), "<input type='checkbox' name='deleteNewsPapers' value='1'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 2);
  $panelUtils->addHiddenField('newsPublicationId', $newsPublicationId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

} else if ($formSubmitted == 2) {

  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");
  $deleteNewsPapers = LibEnv::getEnvHttpPOST("deleteNewsPapers");

  // Check if the newspapers must also be deleted
  if ($deleteNewsPapers == 1) {
    // Delete the newspapers
    if ($newsPapers = $newsPaperUtils->selectByNewsPublicationId($newsPublicationId)) {
      foreach ($newsPapers as $newsPaper) {
        $newsPaperUtils->delete($newsPaper->getId());
      }
    }
  } else {
    // Detach the newspapers
    if ($newsPapers = $newsPaperUtils->selectByNewsPublicationId($newsPublicationId)) {
      foreach ($newsPapers as $newsPaper) {
        $newsPaperId = $newsPaper->getId();
        $newsPaperUtils->detachFromNewsPublication($newsPaperId);
      }
    }
  }

  // Detach the headings
  if ($newsHeadings = $newsHeadingUtils->selectByNewsPublicationId($newsPublicationId)) {
    foreach ($newsHeadings as $newsHeading) {
      $newsHeadingId = $newsHeading->getId();
      $newsHeadingUtils->detachFromNewsPublication($newsHeadingId);
    }
  }

  $newsPublicationUtils->delete($newsPublicationId);

  $str = LibHtml::urlRedirect("$gNewsUrl/newsPublication/admin.php");
  printContent($str);
  return;

} else {

  $newsPublicationId = LibEnv::getEnvHttpGET("newsPublicationId");

  $name = '';
  $description = '';
  if ($newsPublication = $newsPublicationUtils->selectById($newsPublicationId)) {
    $name = $newsPublication->getName();
    $description = $newsPublication->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPublication/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('newsPublicationId', $newsPublicationId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
