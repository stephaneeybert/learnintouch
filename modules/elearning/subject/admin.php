<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$strCommand = "<a href='$gElearningUrl/subject/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$elearningCategories = $elearningSubjectUtils->selectAll();

$panelUtils->openList();
foreach ($elearningCategories as $subject) {
  $elearningSubjectId = $subject->getId();
  $name = $subject->getName();
  $description = $subject->getDescription();

  $strCommand = "<a href='$gElearningUrl/subject/edit.php?elearningSubjectId=$elearningSubjectId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/subject/delete.php?elearningSubjectId=$elearningSubjectId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
