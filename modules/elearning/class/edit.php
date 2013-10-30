<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($class = $elearningClassUtils->selectById($elearningClassId)) {
      $class->setName($name);
      $class->setDescription($description);
      $elearningClassUtils->update($class);
    } else {
      $class = new ElearningClass();
      $class->setName($name);
      $class->setDescription($description);
      $elearningClassUtils->insert($class);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/class/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

  $name = '';
  $description = '';
  if ($elearningClassId) {
    if ($class = $elearningClassUtils->selectById($elearningClassId)) {
      $name = $class->getName();
      $description = $class->getDescription();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/class/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
