<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningMatterId = LibEnv::getEnvHttpPOST("elearningMatterId");

  // Check that there are no courses using the matter
  if ($elearningCourses = $elearningCourseUtils->selectByMatterId($elearningMatterId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $elearningMatterUtils->deleteMatter($elearningMatterId);

    $str = LibHtml::urlRedirect("$gElearningUrl/matter/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningMatterId = LibEnv::getEnvHttpGET("elearningMatterId");

}

if ($matter = $elearningMatterUtils->selectById($elearningMatterId)) {
  $name = $matter->getName();
  $description = $matter->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/matter/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningMatterId', $elearningMatterId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
