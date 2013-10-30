<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contactRefererId = LibEnv::getEnvHttpPOST("contactRefererId");

  $descriptions = '';
  $languages = $languageUtils->getActiveLanguages();
  foreach ($languages as $language) {
    $languageCode = $language->getCode();
    $description = LibEnv::getEnvHttpPOST("description_$languageCode");
    $description = LibString::cleanString($description);
    $descriptions = $languageUtils->setTextForLanguage($descriptions, $languageCode, $description);
  }

  if ($contactReferer = $contactRefererUtils->selectById($contactRefererId)) {
    $contactReferer->setDescription($descriptions);
    $contactRefererUtils->update($contactReferer);
  } else {
    // Get the next list order
    $listOrder = $contactRefererUtils->getNextListOrder();

    $contactReferer = new ContactReferer();
    $contactReferer->setDescription($descriptions);
    $contactReferer->setListOrder($listOrder);
    $contactRefererUtils->insert($contactReferer);
  }

  $str = LibHtml::urlRedirect("$gContactUrl/referer/admin.php");
  printContent($str);
  return;

} else {

  $contactRefererId = LibEnv::getEnvHttpGET("contactRefererId");

  if ($contactReferer = $contactRefererUtils->selectById($contactRefererId)) {
    $descriptions = $contactReferer->getDescription();
  } else {
    $descriptions = '';
  }

  $panelUtils->setHeader($mlText[0], "$gContactUrl/referer/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $i = 0;
  $languages = $languageUtils->getActiveLanguages();
  foreach ($languages as $language) {
    $languageCode = $language->getCode();
    $description = $languageUtils->getTextForLanguage($descriptions, $languageCode);
    $languageFlag = $languageUtils->renderLanguageFlag($languageCode);
    $strField = "<input type='text' name='description_$languageCode' value='$description' size='30' maxlength='255'> $languageFlag";
    if ($i == 0) {
      $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strField);
      $i++;
    } else {
      $panelUtils->addLine('', $strField);
    }
  }
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contactRefererId', $contactRefererId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
