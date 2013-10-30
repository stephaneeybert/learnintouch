<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PROFILE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

$profileUtils->loadProfileNames();

if ($formSubmitted) {

  foreach ($profileUtils->profileNames as $profileName) {
    $name = $profileName[0];
    if ($profile = $profileUtils->selectByName($name)) {
      $profileId = $profile->getId();

      $value = LibEnv::getEnvHttpPOST("value_$profileId");

      $value = LibString::cleanString($value);

      $profile->setValue($value);
      $profileUtils->update($profile);
    }
  }

  $str = LibHtml::urlRedirect("$gAdminUrl/menu.php");
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[2]'>", "$gProfileUrl/logo.php", 600, 600)
  . " " . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[3]'>", "$gProfileUrl/favicon.php", 600, 600)
  . " " . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gProfileUrl/iphoneicon.php", 600, 600)
  . " " . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[6]'>", "$gProfileUrl/map.php", 600, 600)
  . " <a href='$gProfileUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[10]'></a>";
$panelUtils->addLine('', $panelUtils->addCell($strCommand, "nr"));

$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nbr"), $panelUtils->addCell("$mlText[5]", "nb"));
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);

$profileNames = $profileUtils->getProfileNames();
foreach ($profileNames as $profileName) {
  $name = $profileName[0];
  if ($profile = $profileUtils->selectByName($name)) {
    $profileId = $profile->getId();
    $name = $profile->getName();
    $value = $profile->getValue();

    if ($profileName[1]) {
      $name = $profileName[1];
    }

    if ($profileName[2]) {
      $label = $popupUtils->getTipPopup($name, $profileName[2], 300, 400);
    } else {
      $label = $name;
    }

    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='value_$profileId' value='$value' size='30'>");
  }
}

$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
