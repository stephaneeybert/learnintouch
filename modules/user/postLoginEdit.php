<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");
  $languageCode = LibEnv::getEnvHttpPOST("languageCode");
  $isPhone = LibEnv::getEnvHttpPOST("isPhone");

  $webpageId = LibString::cleanString($webpageId);
  $webpageName = LibString::cleanString($webpageName);
  $languageCode = LibString::cleanString($languageCode);
  $isPhone = LibString::cleanString($isPhone);

  // Clear the page if necessary
  if (!$webpageName) {
    $webpageId = '';
    }

  // If a web page or a system page has been selected then use it
  if ($webpageId) {
    $url = $webpageId;
    } else {
    $url = '';
    }

  // Set the post login page
  if ($isPhone) {
    $userUtils->setPhonePostLoginPage($languageCode, $url);
    } else {
    $userUtils->setComputerPostLoginPage($languageCode, $url);
    }

  $str = LibHtml::urlRedirect("$gUserUrl/login_pages_admin.php");
  printContent($str);
  return;

  } else {

  $languageCode = LibEnv::getEnvHttpGET("languageCode");
  $isPhone = LibEnv::getEnvHttpGET("isPhone");

  $language = $languageUtils->selectByCode($languageCode);
  $languageId = $language->getId();
  $strImage = $languageUtils->renderImage($languageId);
  if ($isPhone) {
    $url = $userUtils->getPhonePostLoginPage($languageCode);
    } else {
    $url = $userUtils->getComputerPostLoginPage($languageCode);
    }

  $webpageName = $templateUtils->getPageName($url);
  if ($webpageName) {
    $webpageId = $url;
    } else {
    $webpageId = '';
    }

  $panelUtils->setHeader($mlText[0], "$gUserUrl/login_pages_admin.php");
  $panelUtils->openForm($PHP_SELF, "edit");
  $strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[2]'> $mlText[4]", "$gTemplateUrl/select.php", 600, 600);
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strImage);
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[1], $mlText[7], 300, 200);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage", "n"));
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('webpageId', $webpageId);
  $panelUtils->addHiddenField('languageCode', $languageCode);
  $panelUtils->addHiddenField('isPhone', $isPhone);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
