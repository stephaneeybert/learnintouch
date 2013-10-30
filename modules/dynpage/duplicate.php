<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $dynpageId = LibEnv::getEnvHttpPOST("dynpageId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // Check that the name is not already used
  if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
    if ($wDynpage = $dynpageUtils->selectByParentIdAndName($dynpage->getParentId(), $name)) {
      $wDynpageId = $wDynpage->getId();
      $garbage = $wDynpage->getGarbage();
      if ($wDynpageId != $dynpageId && !$garbage) {
        array_push($warnings, $mlText[7]);
      }
    }
  }

  if (count($warnings) == 0) {

    $dynpageUtils->duplicate($dynpageId, $name, $description);

    $str = LibHtml::urlRedirect("$gDynpageUrl/admin.php");
    printMessage($str);
    return;

  }

} else {

  $dynpageId = LibEnv::getEnvHttpGET("dynpageId");

  $name = '';
  $description = '';
  if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
    $randomNumber = LibUtils::generateUniqueId();
    $name = $dynpage->getName() . DYNPAGE_DUPLICATA . '_' . $randomNumber;
    $description = $dynpage->getDescription();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[3], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('dynpageId', $dynpageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
