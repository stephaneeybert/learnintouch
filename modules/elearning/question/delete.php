<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");

  if (count($warnings) == 0) {
    $elearningQuestionUtils->deleteQuestion($elearningQuestionId);

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
    printMessage($str);
    return;
  }

}

$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
if (!$elearningQuestionId) {
  $elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
}

$question = '';
if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
  $question = $elearningQuestion->getQuestion();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $question);
if ($elearningQuestionUtils->questionHasResults($elearningQuestionId)) {
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->addCell($mlText[3], "w"));
}
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningQuestionId', $elearningQuestionId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
