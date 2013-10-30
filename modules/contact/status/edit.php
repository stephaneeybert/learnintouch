<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contactStatusId = LibEnv::getEnvHttpPOST("contactStatusId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name cannot be empty
  if (!$name) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    if ($contactStatus = $contactStatusUtils->selectById($contactStatusId)) {
      $contactStatus->setName($name);
      $contactStatus->setDescription($description);
      $contactStatusUtils->update($contactStatus);
    } else {
      // Get the next list order
      $listOrder = $contactStatusUtils->getNextListOrder();

      $contactStatus = new ContactStatus();
      $contactStatus->setName($name);
      $contactStatus->setDescription($description);
      $contactStatus->setListOrder($listOrder);
      $contactStatusUtils->insert($contactStatus);
    }

    $str = LibHtml::urlRedirect("$gContactUrl/status/admin.php");
    printContent($str);
    return;

  }

} else {

  $contactStatusId = LibEnv::getEnvHttpGET("contactStatusId");

  if ($contactStatus = $contactStatusUtils->selectById($contactStatusId)) {
    $name = $contactStatus->getName();
    $description = $contactStatus->getDescription();
  } else {
    $name = '';
    $description = '';
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gContactUrl/status/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('contactStatusId', $contactStatusId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
