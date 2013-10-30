<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $name = LibEnv::getEnvHttpPOST("name");

  $name = LibString::cleanString($name);

  // Duplicate the exercise page
  $elearningExercisePageUtils->duplicate($elearningExercisePageId, '', $name);

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
  printContent($str);
  return;

  } else {

  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

  $name = '';
  if ($elearningExercisePageId) {
    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $name = $elearningExercisePage->getName();
      }
    }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='name' value='$name' size='30'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningExercisePageId', $elearningExercisePageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
