<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningAnswerId = LibEnv::getEnvHttpPOST("elearningAnswerId");

  if (count($warnings) == 0) {
    $elearningAnswerUtils->deleteAnswer($elearningAnswerId);

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
    printMessage($str);
    return;
  }

}

$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
if (!$elearningAnswerId) {
  $elearningAnswerId = LibEnv::getEnvHttpPOST("elearningAnswerId");
}

$answer = '';
if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
  $answer = $elearningAnswer->getAnswer();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $answer);
if ($elearningAnswerUtils->answerHasResults($elearningAnswerId)) {
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->addCell($mlText[3], "w"));
}
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningAnswerId', $elearningAnswerId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
