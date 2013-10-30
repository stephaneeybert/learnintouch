<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
$strCommand = "<a href='$gElearningUrl/lesson/heading/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$lessonHeadings = $elearningLessonHeadingUtils->selectAll();

foreach ($lessonHeadings as $lessonHeading) {
  $elearningLessonHeadingId = $lessonHeading->getId();
  $name = $lessonHeading->getName();
  $content = $lessonHeading->getContent();

  $strCommand = "<a href='$gElearningUrl/lesson/heading/edit.php?elearningLessonHeadingId=$elearningLessonHeadingId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/lesson/heading/delete.php?elearningLessonHeadingId=$elearningLessonHeadingId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $content, $panelUtils->addCell($strCommand, "nbr"));
}

$str = $panelUtils->render();

printAdminPage($str);

?>
