<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->addLine();

$strCommand = " <a href='$gElearningUrl/exercise/emptyGarbage.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[7]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$elearningExercises = $elearningExerciseUtils->selectGarbage();
foreach ($elearningExercises as $elearningExercise) {
  $elearningExerciseId = $elearningExercise->getId();
  $name = $elearningExercise->getName();
  $description = $elearningExercise->getDescription();

  $strCommand = "<a href='$gElearningUrl/exercise/restore.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageRestore' title='$mlText[11]'></a>";

  $panelUtils->addLine($panelUtils->addCell($name, "n"), $description, $panelUtils->addCell("$strCommand", "nr"));
  }

$str = $panelUtils->render();

printAdminPage($str);

?>
