<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $contentImportId = LibEnv::getEnvHttpPOST("contentImportId");
  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");

  $xmlResponse = $elearningImportUtils->exposeExerciseAsXML($contentImportId, $elearningExerciseId);
  $lastInsertElearningExerciseId = $elearningImportUtils->importExerciseREST($xmlResponse);

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/import/admin.php");
  $panelUtils->openForm($PHP_SELF);
  if ($lastInsertElearningExerciseId) {
    if ($elearningExercise = $elearningExerciseUtils->selectById($lastInsertElearningExerciseId)) {
      $name = $elearningExercise->getName();
      $description = $elearningExercise->getDescription();
      $panelUtils->addLine($panelUtils->addCell($mlText[5], "ng"));
      $panelUtils->addLine();
      $strName = "<a href='$gElearningUrl/exercise/compose.php?elearningExerciseId=$lastInsertElearningExerciseId' title='$mlText[8]' $gJSNoStatus>" . $name . "</a>";
      $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $strName);
    }
  } else {
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "nw"));
  }
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 2);
  $panelUtils->closeForm();
  $str = $panelUtils->render();
  printAdminPage($str);

} else if ($formSubmitted == 2) {

  $str = LibHtml::urlRedirect("$gElearningUrl/import/admin.php");
  printContent($str);
  return;

} else {

  $contentImportId = LibEnv::getEnvHttpGET("contentImportId");
  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  // Get the exercise details and content
  $xmlResponse = $elearningImportUtils->exposeExerciseAsXML($contentImportId, $elearningExerciseId);
  $exerciseDetails = $elearningImportUtils->getExerciseDetailsREST($contentImportId, $xmlResponse);
  list($name, $description, $maxDuration, $image, $audio, $exercisePages) = $exerciseDetails;

  // The name of the exercise must be retrieved
  if (!$name) {
    array_push($warnings, $mlText[4]);
  }

  $strWarning = '';
  if (count($warnings) > 0) {
    foreach ($warnings as $warning) {
      $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/import/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contentImportId', $contentImportId);
  $panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
