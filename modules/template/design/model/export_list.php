<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), '');
$panelUtils->addLine();

$names = LibDir::getFileNames($gTemplateDataPath . "export/xml/");

$panelUtils->openList();
foreach ($names as $name) {

  $strCommand = ''
    . "<a href='$gTemplateUrl/design/model/import.php?name=$name' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[1]'></a>"
    . " <a href='$gTemplateUrl/design/model/export_delete.php?name=$name' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
