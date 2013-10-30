<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsEditorId = LibEnv::getEnvHttpPOST("newsEditorId");

  // Delete the editor only if it is not used
  if ($newsStory = $newsStoryUtils->selectByNewsEditor($newsEditorId)) {
    array_push($warnings, $mlText[5]);
  }

  if (count($warnings) == 0) {

    $newsEditorUtils->deleteEditor($newsEditorId);

    $str = LibHtml::urlRedirect("$gNewsUrl/newsEditor/admin.php");
    printContent($str);
    return;

  }

} else {

  $newsEditorId = LibEnv::getEnvHttpGET("newsEditorId");

}

if ($newsEditor = $newsEditorUtils->selectById($newsEditorId)) {
  $firstname = $newsEditorUtils->getFirstname($newsEditorId);
  $lastname = $newsEditorUtils->getLastname($newsEditorId);
  $email = $newsEditorUtils->getEmail($newsEditorId);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsEditor/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $firstname);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $lastname);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $email);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsEditorId', $newsEditorId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
