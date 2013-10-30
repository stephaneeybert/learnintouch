<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$strCommand = "<a href='$gMailUrl/category/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$mailCategories = $mailCategoryUtils->selectAll();

$panelUtils->openList();
foreach ($mailCategories as $mailCategory) {
  $categoryId = $mailCategory->getId();
  $name = $mailCategory->getName();
  $description = $mailCategory->getDescription();

  $strCommand = "<a href='$gMailUrl/category/edit.php?categoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gMailUrl/category/delete.php?categoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
