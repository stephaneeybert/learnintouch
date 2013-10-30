<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // The name must not already exist
  if ($elearningCourse = $elearningCourseUtils->selectByName($name)) {
    array_push($warnings, $mlText[9]);
  }

  if (count($warnings) == 0) {

    $elearningCourseUtils->duplicate($elearningCourseId, $name, $description);

    $str = LibHtml::urlRedirect("$gElearningUrl/course/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $name = '';
  $description = '';
  if ($elearningCourseId) {
    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $randomNumber = LibUtils::generateUniqueId();
      $name = $elearningCourse->getName() . ELEARNING_DUPLICATA . '_' . $randomNumber;
      $description = $elearningCourse->getDescription();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/course/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
