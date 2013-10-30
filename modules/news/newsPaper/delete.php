<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");

  $newsPaperUtils->deleteNewsPaper($newsPaperId);

  $str = LibHtml::urlRedirect("$gNewsUrl/newsPaper/admin.php");
  printContent($str);
  return;

} else {

  $newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");

  $title = '';
  $releaseDate = '';
  if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
    $title = $newsPaper->getTitle();
    $releaseDate = $newsPaper->getReleaseDate();
  }

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPaper/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $title);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $releaseDate);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('newsPaperId', $newsPaperId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
