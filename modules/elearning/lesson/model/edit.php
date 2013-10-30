<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonModelId = LibEnv::getEnvHttpPOST("elearningLessonModelId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
      $elearningLessonModel->setName($name);
      $elearningLessonModel->setDescription($description);
      $elearningLessonModelUtils->update($elearningLessonModel);
    } else {
      $elearningLessonModel = new ElearningLessonModel();
      $elearningLessonModel->setName($name);
      $elearningLessonModel->setDescription($description);
      $elearningLessonModelUtils->insert($elearningLessonModel);
      $elearningLessonModelId = $elearningLessonModelUtils->getLastInsertId();

      // Add a heading to the lesson model
      $elearningLessonHeadingUtils->add($elearningLessonModelId);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/model/compose.php?elearningLessonModelId=$elearningLessonModelId");
    printContent($str);
    return;

  }

} else {

  $elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");

  $name = '';
  $description = '';
  if ($elearningLessonModelId) {
    if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
      $name = $elearningLessonModel->getName();
      $description = $elearningLessonModel->getDescription();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/model/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonModelId', $elearningLessonModelId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
