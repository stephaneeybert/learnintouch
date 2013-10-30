<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$strCommand = "<a href='$gFormUrl/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
. " <a href='$gFormUrl/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[20]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$forms = $formUtils->selectAll();

$panelUtils->openList();
foreach ($forms as $form) {
  $formId = $form->getId();
  $name = $form->getName();
  $description = $form->getDescription();
  $email = $form->getEmail();

  $strPopupPreview = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[9]'>", "$gFormUrl/preview.php?formId=$formId", 600, 600);

  $strCommand = ''
    . " <a href='$gFormUrl/edit.php?formId=$formId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gFormUrl/item/admin.php?formId=$formId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageList' title='$mlText[7]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[10]'>", "$gFormUrl/image.php?formId=$formId", 600, 600)
    . ' ' . $strPopupPreview
    . " <a href='$gFormUrl/duplicate.php?formId=$formId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[8]'></a>"
    . " <a href='$gFormUrl/delete.php?formId=$formId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $email, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("form_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
