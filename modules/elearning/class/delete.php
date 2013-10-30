<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");

  if (count($warnings) == 0) {

    $elearningClassUtils->deleteClass($elearningClassId);

    $str = LibHtml::urlRedirect("$gElearningUrl/class/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

}

if ($class = $elearningClassUtils->selectById($elearningClassId)) {
  $name = $class->getName();
  $description = $class->getDescription();
}

if ($elearningSubscriptions = $elearningSubscriptionUtils->selectByClassId($elearningClassId)) {
  array_push($warnings, $mlText[3]);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/class/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
