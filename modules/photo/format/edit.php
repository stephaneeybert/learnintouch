<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoFormatId = LibEnv::getEnvHttpPOST("photoFormatId");
  $name = LibEnv::getEnvHttpPOST("name");
  $currentName = LibEnv::getEnvHttpPOST("currentName");
  $description = LibEnv::getEnvHttpPOST("description");
  $price = LibEnv::getEnvHttpPOST("price");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $price = LibString::cleanString($price);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // The name must not be already used by another format
  if ($photoFormat = $photoFormatUtils->selectByName($name)) {
    $wPhotoFormatId = $photoFormat->getId();
    if ($wPhotoFormatId != $photoFormatId) {
      array_push($warnings, $mlText[2]);
    }
  }

  if (count($warnings) == 0) {

    if ($photoFormat = $photoFormatUtils->selectById($photoFormatId)) {
      $photoFormat->setName($name);
      $photoFormat->setDescription($description);
      $photoFormat->setPrice($price);
      $photoFormatUtils->update($photoFormat);
    } else {
      $photoFormat = new PhotoFormat();
      $photoFormat->setName($name);
      $photoFormat->setDescription($description);
      $photoFormat->setPrice($price);
      $photoFormatUtils->insert($photoFormat);
    }

    $str = LibHtml::urlRedirect("$gPhotoUrl/format/admin.php");
    printContent($str);
    return;

  }

} else {

  $photoFormatId = LibEnv::getEnvHttpGET("photoFormatId");

  $name = '';
  $description = '';
  $price = '';
  if ($photoFormatId) {
    if ($photoFormat = $photoFormatUtils->selectById($photoFormatId)) {
      $name = $photoFormat->getName();
      $description = $photoFormat->getDescription();
      $price = $photoFormat->getPrice();
    }
  }

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
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='price' value='$price' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('photoFormatId', $photoFormatId);
$panelUtils->addHiddenField('currentName', $name);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
