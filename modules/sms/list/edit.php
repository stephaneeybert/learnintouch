<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $autoSubscribe = LibEnv::getEnvHttpPOST("autoSubscribe");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $autoSubscribe = LibString::cleanString($autoSubscribe);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[8]);
  }

  if (count($warnings) == 0) {

    if ($smsList = $smsListUtils->selectById($smsListId)) {
      $smsList->setName($name);
      $smsList->setDescription($description);
      $smsList->setAutoSubscribe($autoSubscribe);
      $smsListUtils->update($smsList);
    } else {
      $smsList = new SmsList();
      $smsList->setName($name);
      $smsList->setDescription($description);
      $smsList->setAutoSubscribe($autoSubscribe);
      $smsListUtils->insert($smsList);
      $smsListId = $smsListUtils->getLastInsertId();
    }

    $str = LibHtml::urlRedirect("$gSmsUrl/list/admin.php");
    printContent($str);
    return;

  }

} else {

  $smsListId = LibEnv::getEnvHttpGET("smsListId");

  if (!$smsListId) {
    $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  }

  $name = '';
  $description = '';
  $autoSubscribe = '';
  if ($smsListId) {
    if ($smsList = $smsListUtils->selectById($smsListId)) {
      $name = $smsList->getName();
      $description = $smsList->getDescription();
      $autoSubscribe = $smsList->getAutoSubscribe();
    }
  }

}

if ($autoSubscribe == '1') {
  $checkedAutoSubscribe = "CHECKED";
} else {
  $checkedAutoSubscribe = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gSmsUrl/list/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='autoSubscribe' $checkedAutoSubscribe value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('smsListId', $smsListId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
