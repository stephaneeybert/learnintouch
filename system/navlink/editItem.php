<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navlinkItemId = LibEnv::getEnvHttpPOST("navlinkItemId");
  $navlinkId = LibEnv::getEnvHttpPOST("navlinkId");
  $text = LibEnv::getEnvHttpPOST("text");
  $description = LibEnv::getEnvHttpPOST("description");
  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");
  $externalUrl = LibEnv::getEnvHttpPOST("externalUrl");
  $blankTarget = LibEnv::getEnvHttpPOST("blankTarget");
  $language = LibEnv::getEnvHttpPOST("language");
  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");

  $text = LibString::cleanString($text);
  $description = LibString::cleanString($description);
  $webpageId = LibString::cleanString($webpageId);
  $webpageName = LibString::cleanString($webpageName);
  $externalUrl = LibString::cleanString($externalUrl);
  $blankTarget = LibString::cleanString($blankTarget);
  $language = LibString::cleanString($language);
  $templateModelId = LibString::cleanString($templateModelId);

  // Check that there is not already a language for the link
  if ($navlinkItem = $navlinkItemUtils->selectByLanguageAndNavlinkId($language, $navlinkId)) {
    $wNavlinkItemId = $navlinkItem->getId();
    if (!$navlinkItemId || ($navlinkItemId && $wNavlinkItemId && $wNavlinkItemId != $navlinkItemId)) {
      array_push($warnings, $mlText[20]);
    }
  }

  // Format the url
  // The url can be a web address or an email address
  if ($externalUrl && !LibEmail::validate($externalUrl)) {
    $externalUrl = LibUtils::formatUrl($externalUrl);
  }

  // Clear the page if necessary
  if (!$webpageName) {
    $webpageId = '';
  }

  // If a web page or a system page has been selected then use it
  if ($webpageId) {
    $url = $webpageId;
  } else if ($externalUrl) {
    $url = $externalUrl;
  } else {
    $url = '';
  }

  if (count($warnings) == 0) {

    if ($navlinkItem = $navlinkItemUtils->selectById($navlinkItemId)) {
      $navlinkItem->setText($text);
      $navlinkItem->setDescription($description);
      $navlinkItem->setUrl($url);
      $navlinkItem->setBlankTarget($blankTarget);
      $navlinkItem->setLanguage($language);
      $navlinkItem->setTemplateModelId($templateModelId);
      $navlinkItemUtils->update($navlinkItem);
    } else {
      $navlinkItem = new NavlinkItem();
      $navlinkItem->setNavlinkId($navlinkId);
      $navlinkItem->setText($text);
      $navlinkItem->setDescription($description);
      $navlinkItem->setUrl($url);
      $navlinkItem->setBlankTarget($blankTarget);
      $navlinkItem->setLanguage($language);
      $navlinkItem->setTemplateModelId($templateModelId);
      $navlinkItemUtils->insert($navlinkItem);
    }

    $str = LibHtml::urlRedirect("$gNavlinkUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $navlinkItemId = LibEnv::getEnvHttpGET("navlinkItemId");
  $navlinkId = LibEnv::getEnvHttpGET("navlinkId");

}

$text = '';
$description = '';
$image = '';
$imageOver = '';
$url = '';
$webpageId = '';
$webpageName = '';
$blankTarget = '';
$language = '';
$templateModelId = '';
if ($navlinkItem = $navlinkItemUtils->selectById($navlinkItemId)) {
  $text = $navlinkItem->getText();
  $description = $navlinkItem->getDescription();
  $image = $navlinkItem->getImage();
  $imageOver = $navlinkItem->getImageOver();
  $url = $navlinkItem->getUrl();
  $blankTarget = $navlinkItem->getBlankTarget();
  $language = $navlinkItem->getLanguage();
  $templateModelId = $navlinkItem->getTemplateModelId();
  $navlinkId = $navlinkItem->getNavlinkId();
}

$webpageName = $templateUtils->getPageName($url);
if ($webpageName) {
  $externalUrl = '';
  $webpageId = $url;
} else {
  $externalUrl = $url;
}

if ($blankTarget == '1') {
  $checkedBlankTarget = "CHECKED";
} else {
  $checkedBlankTarget = '';
}

if ($image) {
  $strImage = "<img src='$navlinkItemUtils->imageUrl/$image' border='0' title='$image'>";
} else {
  $strImage = '';
}

if ($imageOver) {
  $strImageOver = "<img src='$navlinkItemUtils->imageUrl/$imageOver' border='0' title='$imageOver'>";
} else {
  $strImageOver = '';
}

$languageNames = $navlinkUtils->getAvailableLanguages($navlinkId);
$strSelectLanguage = LibHtml::getSelectList("language", $languageNames, $language);

$modelList = $templateModelUtils->getAllModels();
$strSelectModel = LibHtml::getSelectList("templateModelId", $modelList, $templateModelId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNavlinkUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[1], $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='text' value='$text' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[4], 300, 400);
$strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[15]'> $mlText[25]", "$gTemplateUrl/select.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("$mlText[24] <input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage", "n"));
$panelUtils->addLine('', $panelUtils->addCell("$mlText[16] <input type='text' name='externalUrl' value='$externalUrl' size='30' maxlength='255'>", "n"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='blankTarget' $checkedBlankTarget value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[10]'>", "$gNavlinkUrl/image.php?navlinkItemId=$navlinkItemId", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strCommand);
if ($strImage) {
  $panelUtils->addLine('', $strImage);
}
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[23], $mlText[9], 300, 300);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[10]'>", "$gNavlinkUrl/imageOver.php?navlinkItemId=$navlinkItemId", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strCommand);
if ($strImageOver) {
  $panelUtils->addLine('', $strImageOver);
}
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[19], $mlText[21], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectLanguage);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[26], $mlText[27], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectModel);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('navlinkItemId', $navlinkItemId);
$panelUtils->addHiddenField('navlinkId', $navlinkId);
$panelUtils->addHiddenField('webpageId', $webpageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
