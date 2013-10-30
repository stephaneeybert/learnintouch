<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");

  // Delete the news heading only if it is not used
  if ($newsStories = $newsStoryUtils->selectByNewsHeading($newsHeadingId)) {
    array_push($warnings, $mlText[5]);
  }

  if (count($warnings) == 0) {

    $newsHeadingUtils->delete($newsHeadingId);

    $str = LibHtml::urlRedirect("$gNewsUrl/newsHeading/admin.php");
    printContent($str);
    return;

  }

} else {

  $newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");

}

if ($newsHeading = $newsHeadingUtils->selectById($newsHeadingId)) {
  $name = $newsHeading->getName();
  $description = $newsHeading->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsHeading/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsHeadingId', $newsHeadingId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
