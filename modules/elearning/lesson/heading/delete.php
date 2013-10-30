<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonHeadingId = LibEnv::getEnvHttpPOST("elearningLessonHeadingId");

  $elearningLessonHeadingUtils->deleteHeading($elearningLessonHeadingId);

  $str = LibHtml::urlRedirect("$gElearningUrl/lesson/heading/admin.php");
  printContent($str);
  return;

} else {

  $elearningLessonHeadingId = LibEnv::getEnvHttpGET("elearningLessonHeadingId");

  if ($elearningLessonHeading = $elearningLessonHeadingUtils->selectById($elearningLessonHeadingId)) {
    $name = $elearningLessonHeading->getName();
    $content = $elearningLessonHeading->getContent();
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/heading/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $content);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningLessonHeadingId', $elearningLessonHeadingId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
