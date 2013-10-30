<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
  $question = LibEnv::getEnvHttpPOST("question");
  $points = LibEnv::getEnvHttpPOST("points");

  $question = LibString::cleanString($question);
  $points = LibString::cleanString($points);

  // Duplicate the question
  $elearningQuestionUtils->duplicate($elearningQuestionId, '', $question, $points);

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
  printContent($str);
  return;

  } else {

  $elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");

  $question = '';
  $points = '';
  if ($elearningQuestionId) {
    if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
      $question = $elearningQuestion->getQuestion();
      $points = $elearningQuestion->getPoints();
      }
    }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='question' value='$question' size='30'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='points' value='$points' size='3'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningQuestionId', $elearningQuestionId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
