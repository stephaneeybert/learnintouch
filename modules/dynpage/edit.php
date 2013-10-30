<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $parentId = LibEnv::getEnvHttpPOST("parentId");
  $dynpageId = LibEnv::getEnvHttpPOST("dynpageId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $hide = LibEnv::getEnvHttpPOST("hide");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $hide = LibString::cleanString($hide);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // Check that the name is not already used
  if ($dynpage = $dynpageUtils->selectByParentIdAndNameAndNotGarbage($parentId, $name)) {
    $wDynpageId = $dynpage->getId();
    if ($wDynpageId != $dynpageId) {
      array_push($warnings, $mlText[7]);
    }
  }

  if (count($warnings) == 0) {

    if (!$dynpage = $dynpageUtils->selectById($dynpageId)) {
      $dynpageId = $dynpageUtils->addPage($name, $description, $parentId);

      $str = LibHtml::urlRedirect("$gDynpageUrl/edit_content.php?dynpageId=$dynpageId");
      printContent($str);
      return;
    } else {
      $dynpage->setName($name);
      $dynpage->setDescription($description);
      $dynpage->setHide($hide);
      $dynpageUtils->update($dynpage);

      $str = LibHtml::urlRedirect("$gDynpageUrl/admin.php");
      printContent($str);
      return;
    }

  }

} else {

  $dynpageId = LibEnv::getEnvHttpGET("dynpageId");
  $parentId = LibEnv::getEnvHttpGET("parentId");

  $name = '';
  $description = '';
  $hide = '';
  if ($dynpageId) {
    if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
      $name = $dynpage->getName();
      $description = $dynpage->getDescription();
      $hide = $dynpage->getHide();
    }
  }

}

if ($hide == '1') {
  $checkedHide = "CHECKED";
}  else {
  $checkedHide = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[3], 300, 160);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hide' $checkedHide value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('dynpageId', $dynpageId);
$panelUtils->addHiddenField('parentId', $parentId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
