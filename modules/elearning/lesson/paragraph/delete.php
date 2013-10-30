<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");

  if (count($warnings) == 0) {
    $elearningLessonParagraphUtils->deleteParagraph($elearningLessonParagraphId);

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/compose.php");
    printMessage($str);
    return;
  }

}

$elearningLessonParagraphId = LibEnv::getEnvHttpGET("elearningLessonParagraphId");
if (!$elearningLessonParagraphId) {
  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");
}

if ($elearningQuestion = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
  $name = $elearningQuestion->getName();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonParagraphId', $elearningLessonParagraphId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
