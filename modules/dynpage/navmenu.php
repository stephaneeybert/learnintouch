<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $dynpageNavmenuId = LibEnv::getEnvHttpPOST("dynpageNavmenuId");
  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");

  // Clear the page if necessary
  // This if statement precede the next if statement
  if (!$webpageName) {
    $webpageId = '';
  }

  if (count($warnings) == 0) {

    if ($dynpageNavmenu = $dynpageNavmenuUtils->selectById($dynpageNavmenuId)) {
      $dynpageNavmenu->setParentId($webpageId);
      $dynpageNavmenuUtils->update($dynpageNavmenu);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;

  }

} else {

  $dynpageNavmenuId = LibEnv::getEnvHttpGET("dynpageNavmenuId");
  if (!$dynpageNavmenuId) {
    $dynpageNavmenuId = LibEnv::getEnvHttpPOST("dynpageNavmenuId");
  }

  $webpageId = '';
  $webpageName = '';
  if ($dynpageNavmenuId) {
    if ($dynpageNavmenu = $dynpageNavmenuUtils->selectById($dynpageNavmenuId)) {
      $webpageId = $dynpageNavmenu->getParentId();

      $webpageName = $templateUtils->getPageName($webpageId);
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0]);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[15]'>", "$gDynpageUrl/select.php", 600, 600);
$strSelectPageBis = $popupUtils->getDialogPopup($mlText[25], "$gDynpageUrl/select.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($mlText[12], "nbr"), $panelUtils->addCell("<input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage $strSelectPageBis", "n"));
$panelUtils->addHiddenField('webpageId', $webpageId);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('dynpageNavmenuId', $dynpageNavmenuId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
