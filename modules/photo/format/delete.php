<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoFormatId = LibEnv::getEnvHttpPOST("photoFormatId");

  // Delete the photo photo format only if it is not used
  if ($photos = $photoUtils->selectByFormat($photoFormatId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $photoFormatUtils->delete($photoFormatId);

    $str = LibHtml::urlRedirect("$gPhotoUrl/format/admin.php");
    printContent($str);
    return;

  }

} else {

  $photoFormatId = LibEnv::getEnvHttpGET("photoFormatId");

}

if ($photoFormat = $photoFormatUtils->selectById($photoFormatId)) {
  $name = $photoFormat->getName();
  $description = $photoFormat->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/format/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('photoFormatId', $photoFormatId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
