<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $languageId = LibEnv::getEnvHttpPOST("languageId");
  $code = LibEnv::getEnvHttpPOST("code");
  $name = LibEnv::getEnvHttpPOST("name");
  $locale = LibEnv::getEnvHttpPOST("locale");

  $code = LibString::cleanString($code);
  $name = LibString::cleanString($name);
  $locale = LibString::cleanString($locale);

  // The code is required
  if (!$code) {
    array_push($warnings, $mlText[39]);
  }

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[40]);
  }

  // The code must not already exist
  // If creating a new one...
  if (!$languageId) {
    if ($language = $languageUtils->selectByCode($code)) {
      array_push($warnings, $mlText[9]);
    }
  } else {
    // If editing an existing one...
    if ($language = $languageUtils->selectByCode($code)) {
      $wlanguageId = $language->getId();
      // Except for the current one...
      if ($wlanguageId != $languageId) {
        array_push($warnings, $mlText[9]);
      }
    }
  }

  if (count($warnings) == 0) {

    if ($language = $languageUtils->selectById($languageId)) {
      $language->setCode($code);
      $language->setName($name);
      $language->setLocale($locale);
      $languageUtils->update($language);
    } else {
      $language = new Language();
      $language->setCode($code);
      $language->setName($name);
      $language->setLocale($locale);
      $languageUtils->insert($language);
    }

    $str = LibHtml::urlRedirect("$gLanguageUrl/admin.php");
    printMessage($str);
    return;

  }

} else {

  $languageId = LibEnv::getEnvHttpGET("languageId");

  $code = '';
  $name = '';
  $locale = '';
  if ($languageId) {
    if ($language = $languageUtils->selectById($languageId)) {
      $code = $language->getCode();
      $name = $language->getName();
      $locale = $language->getLocale();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gLanguageUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='code'  value='$code' size='4' maxlength='2'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='locale'  value='$locale' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('languageId', $languageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
