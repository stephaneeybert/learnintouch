<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->addLine();

$strCommand = " <a href='$gElearningUrl/lesson/emptyGarbage.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[7]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$elearningLessons = $elearningLessonUtils->selectGarbage();
foreach ($elearningLessons as $elearningLesson) {
  $elearningLessonId = $elearningLesson->getId();
  $name = $elearningLesson->getName();
  $description = $elearningLesson->getDescription();

  $strCommand = "<a href='$gElearningUrl/lesson/restore.php?elearningLessonId=$elearningLessonId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageRestore' title='$mlText[11]'></a>";

  $panelUtils->addLine($panelUtils->addCell($name, "n"), $description, $panelUtils->addCell("$strCommand", "nr"));
}

$str = $panelUtils->render();

printAdminPage($str);

?>
