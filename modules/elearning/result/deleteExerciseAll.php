<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");

  // Delete
  if ($elearningResults = $elearningResultUtils->selectByExerciseId($elearningExerciseId)) {
    foreach($elearningResults as $elearningResult) {
      $elearningResultId = $elearningResult->getId();
      $elearningResultUtils->deleteResult($elearningResultId);
      }
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/result/admin.php");
  printContent($str);
  return;

  } else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  // Delete all the results of an exercise

  // Get the exercise details
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
    }

  // Create the list of results
  $resultList = array();
  if ($elearningResults = $elearningResultUtils->selectByExerciseId($elearningExerciseId)) {
    foreach($elearningResults as $elearningResult) {
      $elearningResultId = $elearningResult->getId();
      $email = $elearningResult->getEmail();
      $firstname = $elearningResult->getFirstname();
      $lastname = $elearningResult->getLastname();
      array_push($resultList, array($elearningResultId, $email, $firstname, $lastname));
      }
    }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/result/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "$name - $description");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $panelUtils->addCell($mlText[3], "nb"));
  $panelUtils->addLine();
  foreach($resultList as $result) {
    list($elearningResultId, $email, $firstname, $lastname) = $result;
    $panelUtils->addLine($panelUtils->addCell("$firstname $lastname", "nr"), $email);
    }
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
