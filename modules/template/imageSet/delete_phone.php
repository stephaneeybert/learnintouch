<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$imagePath = $imageSetUtils->phoneCustomImagePath;
$imageUrl = $imageSetUtils->phoneCustomImageUrl;

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $image = LibEnv::getEnvHttpPOST("image");

  $imageSetUtils->deletePhoneCustomImage($image);

  $str = LibHtml::urlRedirect("$gImageSetUrl/admin.php");
  printContent($str);
  return;
}

$image = LibEnv::getEnvHttpGET("image");

$panelUtils->setHeader($mlText[0], "$gImageSetUrl/admin.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<img src='$imageUrl/$image' border='0' title='' href=''>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('image', $image);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
