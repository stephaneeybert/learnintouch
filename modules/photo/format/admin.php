<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/admin.php");
$strCommand = "<a href='$gPhotoUrl/format/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$photoFormats = $photoFormatUtils->selectAll();
foreach ($photoFormats as $photoFormat) {
  $photoFormatId = $photoFormat->getId();
  $name = $photoFormat->getName();
  $description = $photoFormat->getDescription();

  $strCommand = "<a href='$gPhotoUrl/format/edit.php?photoFormatId=$photoFormatId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gPhotoUrl/format/delete.php?photoFormatId=$photoFormatId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
  }

$str = $panelUtils->render();

printAdminPage($str);

?>
