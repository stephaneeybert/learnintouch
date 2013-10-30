<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$adminUtils->checkSuperAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Delete all lessons from the garbage
  if ($elearningLessons = $elearningLessonUtils->selectGarbage()) {
    foreach ($elearningLessons as $elearningLesson) {
      $elearningLessonId = $elearningLesson->getId();
      $elearningLessonUtils->deleteLesson($elearningLessonId);
    }
  }

  $str = LibHtml::urlRedirect("$gElearningUrl/lesson/garbage.php");
  printMessage($str);
  return;

} else {

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/garbage.php");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
