<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gTemplateDesignUrl/model/admin.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gImageSetUrl/reset.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[3]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nbc"), $panelUtils->addCell("$mlText[8]", "nbc"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$filenames = LibDir::getFileNames($gTemplateImagePath . "images/computer/");

sort($filenames);

$panelUtils->openList();
foreach($filenames as $filename) {
  $image = basename($filename);

  if (!file_exists($imageSetUtils->computerImagePath . $image)) {
    $imageSetUtils->copyComputerStandardImage($image);
  }

  if (!file_exists($imageSetUtils->phoneImagePath . $image)) {
    $imageSetUtils->copyPhoneStandardImage($image);
  }

  $strComputerStandardImage = "<img src='" . $imageSetUtils->computerImageUrl . "/$image' border='0' title='$image'>";
  $strPhoneStandardImage = "<img src='" . $imageSetUtils->phoneImageUrl . "/$image' border='0' title='$image'>";

  $strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[5]'>", "$gImageSetUrl/image_computer.php?standardImage=$image", 600, 600);
  $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[9]'>", "$gImageSetUrl/image_phone.php?standardImage=$image", 600, 600);

  if (is_file($imageSetUtils->computerCustomImagePath . $image)) {
    $strCommand .= " <a href='$gImageSetUrl/delete_computer.php?image=$image' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[6]'></a>";
  }

  if (is_file($imageSetUtils->phoneCustomImagePath . $image)) {
    $strCommand .= " <a href='$gImageSetUrl/delete_phone.php?image=$image' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[7]'></a>";
  }

  $panelUtils->addLine($panelUtils->addCell($strComputerStandardImage, "nbc"), $panelUtils->addCell($strPhoneStandardImage, "nbc"), $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
