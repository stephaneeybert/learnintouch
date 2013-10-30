<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonModelId = LibEnv::getEnvHttpPOST("elearningLessonModelId");

  if ($elearningLessons = $elearningLessonUtils->selectByLessonModelId($elearningLessonModelId)) {
    foreach ($elearningLessons as $elearningLesson) {
      $elearningLesson->setLessonModelId('');
      $elearningLessonUtils->update($elearningLesson);
    }
  }

  $elearningLessonModelUtils->deleteModel($elearningLessonModelId);

  $str = LibHtml::urlRedirect("$gElearningUrl/lesson/model/admin.php");
  printContent($str);
  return;

} else {

  $elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");

  if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
    $name = $elearningLessonModel->getName();
    $description = $elearningLessonModel->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/model/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningLessonModelId', $elearningLessonModelId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
