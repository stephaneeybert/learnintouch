<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
$help = $popupUtils->getHelpPopup($mlText[7], 300, 500);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gElearningUrl/lesson/model/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$lessonModels = $elearningLessonModelUtils->selectAll();

$panelUtils->openList();
foreach ($lessonModels as $lessonModel) {
  $elearningLessonModelId = $lessonModel->getId();
  $name = $lessonModel->getName();
  $description = $lessonModel->getDescription();
  $locked = $lessonModel->getLocked();

  if (!$elearningLessonModelUtils->isLockedForLoggedInAdmin($elearningLessonModelId)) {
    $strCommand = "<a href='$gElearningUrl/lesson/model/edit.php?elearningLessonModelId=$elearningLessonModelId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
      . " <a href='$gElearningUrl/lesson/model/compose.php?elearningLessonModelId=$elearningLessonModelId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[4]'></a>"
      . " <a href='$gElearningUrl/lesson/model/delete.php?elearningLessonModelId=$elearningLessonModelId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";
  } else {
    $strCommand = '';
  }


  $adminLogin = $adminUtils->checkAdminLogin();
  if ($adminUtils->isSuperAdmin($adminLogin)) {
    if ($locked) {
      $strCommand .= " <a href='$gElearningUrl/lesson/model/lock.php?elearningLessonModelId=$elearningLessonModelId&locked=0' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageUnlock' title='$mlText[9]'></a>";
    } else {
      $strCommand .= " <a href='$gElearningUrl/lesson/model/lock.php?elearningLessonModelId=$elearningLessonModelId&locked=1' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageLock' title='$mlText[8]'></a>";
    }
  }

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
