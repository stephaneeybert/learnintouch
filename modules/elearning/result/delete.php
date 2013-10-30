<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningResultId = LibEnv::getEnvHttpPOST("elearningResultId");

  $elearningResultUtils->deleteResult($elearningResultId);

  $str = LibHtml::urlRedirect("$gElearningUrl/result/admin.php");
  printContent($str);
  return;

} else {

  $elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

  $elearningExerciseId = '';
  $email = '';
  $firstname = '';
  $lastname = '';
  if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
    $elearningExerciseId = $elearningResult->getElearningExerciseId();
    $email = $elearningResult->getEmail();
    $firstname = $elearningResult->getFirstname();
    $lastname = $elearningResult->getLastname();
  }

  $name = '';
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/result/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$firstname $lastname");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningResultId', $elearningResultId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
