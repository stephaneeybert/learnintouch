<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $smsId = LibEnv::getEnvHttpPOST("smsId");
  $body = LibEnv::getEnvHttpPOST("body");
  $description = LibEnv::getEnvHttpPOST("description");
  $categoryId = LibEnv::getEnvHttpPOST("categoryId");

  // Keep only the first 160 characters
  // And SMS message size is limited
  if (strlen($body) > SMS_MESSAGE_LENGTH) {
    $body = substr($body, 0, SMS_MESSAGE_LENGTH);
  }

  // The body is required
  if (!$body) {
    array_push($warnings, $mlText[9]);
  }

  $body = LibString::lineBreakToSpace($body);

  $adminId = $adminUtils->getLoggedAdminId();

  if (count($warnings) == 0) {

    if ($sms = $smsUtils->selectById($smsId)) {
      $sms->setBody($body);
      $sms->setDescription($description);
      $sms->setCategoryId($categoryId);
      $smsUtils->update($sms);
    } else {
      $sms = new Sms();
      $sms->setBody($body);
      $sms->setDescription($description);
      $sms->setAdminId($adminId);
      $sms->setCategoryId($categoryId);
      $smsUtils->insert($sms);
    }

    $str = LibHtml::urlRedirect("$gSmsUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $smsId = LibEnv::getEnvHttpGET("smsId");

  $body = '';
  $description = '';
  $categoryId = '';
  if ($sms = $smsUtils->selectById($smsId)) {
    $body = $sms->getBody();
    $description = $sms->getDescription();
    $categoryId = $sms->getCategoryId();
  }

}

$smsCategories = $smsCategoryUtils->selectAll();
$listCategories = Array('' => '');
foreach ($smsCategories as $smsCategory) {
  $wId = $smsCategory->getId();
  $wName = $smsCategory->getName();
  $listCategories[$wId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("categoryId", $listCategories, $categoryId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gSmsUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $strSelectCategory);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<textarea name='body' cols='40' rows='6'>$body</textarea>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('smsId', $smsId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
