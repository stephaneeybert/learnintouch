<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $dynpageId = LibEnv::getEnvHttpPOST("dynpageId");
  $adminId = LibEnv::getEnvHttpPOST("adminId");

  if (count($warnings) == 0) {

    if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
      $dynpage->setAdminId($adminId);
      $dynpageUtils->update($dynpage);
    }

    $str = LibHtml::urlRedirect("$gDynpageUrl/admin.php");
    printMessage($str);
    return;

  }

} else {

  $dynpageId = LibEnv::getEnvHttpGET("dynpageId");

  $name = '';
  $adminId = '';
  if ($dynpageId) {
    if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
      $name = $dynpage->getName();
      $adminId = $dynpage->getAdminId();
    }
  }

}

$adminName = '';
if ($admin = $adminUtils->selectById($adminId)) {
  $adminName = $admin->getFirstname() . ' ' . $admin->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$strSelectAdmin = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[15]'> $mlText[25]", "$gAdminUrl/select.php", 600, 600);
$label = $popupUtils->getTipPopup($mlText[2], $mlText[3], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='adminName' value='$adminName' size='30' maxlength='255'> $strSelectAdmin", "n"));
$panelUtils->addHiddenField('adminId', $adminId);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('dynpageId', $dynpageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
