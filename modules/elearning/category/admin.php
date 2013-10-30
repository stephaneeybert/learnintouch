<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$strCommand = "<a href='$gElearningUrl/category/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$elearningCategories = $elearningCategoryUtils->selectAll();

$panelUtils->openList();
foreach ($elearningCategories as $category) {
  $elearningCategoryId = $category->getId();
  $name = $category->getName();
  $description = $category->getDescription();

  $strCommand = "<a href='$gElearningUrl/category/edit.php?elearningCategoryId=$elearningCategoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/category/delete.php?elearningCategoryId=$elearningCategoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
