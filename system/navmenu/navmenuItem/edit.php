<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navmenuItemId = LibEnv::getEnvHttpPOST("navmenuItemId");
  $name = LibEnv::getEnvHttpPOST("name");
  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");
  $externalUrl = LibEnv::getEnvHttpPOST("externalUrl");
  $blankTarget = LibEnv::getEnvHttpPOST("blankTarget");
  $description = LibEnv::getEnvHttpPOST("description");
  $hide = LibEnv::getEnvHttpPOST("hide");
  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");
  $parentNavmenuItemId = LibEnv::getEnvHttpPOST("parentNavmenuItemId");

  $name = LibString::cleanString($name);
  $webpageId = LibString::cleanString($webpageId);
  $webpageName = LibString::cleanString($webpageName);
  $externalUrl = LibString::cleanString($externalUrl);
  $blankTarget = LibString::cleanString($blankTarget);
  $description = LibString::cleanString($description);
  $hide = LibString::cleanString($hide);
  $templateModelId = LibString::cleanString($templateModelId);

  // Format the url
  // The url can be a web address or an email address
  if ($externalUrl && !LibEmail::validate($externalUrl)) {
    $externalUrl = LibUtils::formatUrl($externalUrl);
  }

  // Clear the page if necessary
  // This if statement precede the next if statement
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

    if ($navmenuItem = $navmenuItemUtils->selectById($navmenuItemId)) {
      $navmenuItem->setName($name);
      $navmenuItem->setUrl($url);
      $navmenuItem->setBlankTarget($blankTarget);
      $navmenuItem->setDescription($description);
      $navmenuItem->setHide($hide);
      $navmenuItem->setTemplateModelId($templateModelId);
      $navmenuItemUtils->update($navmenuItem);
    } else {
      $navmenuItem = new NavmenuItem();
      $navmenuItem->setName($name);
      $navmenuItem->setUrl($url);
      $navmenuItem->setBlankTarget($blankTarget);
      $navmenuItem->setDescription($description);
      $navmenuItem->setHide($hide);
      $navmenuItem->setTemplateModelId($templateModelId);

      // Get the next list order
      $listOrder = $navmenuItemUtils->getNextListOrder($parentNavmenuItemId);
      $navmenuItem->setListOrder($listOrder);

      $navmenuItem->setParentNavmenuItemId($parentNavmenuItemId);
      $navmenuItemUtils->insert($navmenuItem);
    }

    $str = LibHtml::urlRedirect("$gNavmenuUrl/admin.php");
    printContent($str);
    return;
  }

}

$navmenuItemId = LibEnv::getEnvHttpGET("navmenuItemId");
if (!$navmenuItemId) {
  $navmenuItemId = LibEnv::getEnvHttpPOST("navmenuItemId");
}

$parentNavmenuItemId = LibEnv::getEnvHttpGET("parentNavmenuItemId");
if (!$parentNavmenuItemId) {
  $parentNavmenuItemId = LibEnv::getEnvHttpPOST("parentNavmenuItemId");
}

if (!$formSubmitted) {
  $name = '';
  $url = '';
  $blankTarget = '';
  $webpageId = '';
  $webpageName = '';
  $description = '';
  $hide = '';
  $templateModelId = '';
  if ($navmenuItemId) {
    if ($navmenuItem = $navmenuItemUtils->selectById($navmenuItemId)) {
      $name = $navmenuItem->getName();
      $url = $navmenuItem->getUrl();
      $blankTarget = $navmenuItem->getBlankTarget();
      $description = $navmenuItem->getDescription();
      $hide = $navmenuItem->getHide();
      $templateModelId = $navmenuItem->getTemplateModelId();
      $parentNavmenuItemId = $navmenuItem->getParentNavmenuItemId();
    }
  }
}

$webpageName = $templateUtils->getPageName($url);
if ($webpageName) {
  $externalUrl = '';
  $webpageId = $url;
} else {
  $externalUrl = $url;
}

$modelList = $templateModelUtils->getAllModels();
$strSelectModel = LibHtml::getSelectList("templateModelId", $modelList, $templateModelId);

if ($hide == '1') {
  $checkedHide = "CHECKED";
} else {
  $checkedHide = '';
}

if ($blankTarget == '1') {
  $checkedBlankTarget = "CHECKED";
} else {
  $checkedBlankTarget = '';
}

$panelUtils->setHeader($mlText[0], "$gNavmenuUrl/admin.php");

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[9], $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[13], 300, 400);
$strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[15]'>", "$gTemplateUrl/select.php", 600, 600);
$strSelectPageBis = $popupUtils->getDialogPopup($mlText[25], "$gTemplateUrl/select.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("$mlText[12] <input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage $strSelectPageBis", "n"));
$panelUtils->addLine('', $panelUtils->addCell("$mlText[16] <input type='text' name='externalUrl' value='$externalUrl' size='30' maxlength='255'>", "n"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='blankTarget' $checkedBlankTarget value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[8], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hide' $checkedHide value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[26], $mlText[27], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectModel);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('navmenuItemId', $navmenuItemId);
$panelUtils->addHiddenField('parentNavmenuItemId', $parentNavmenuItemId);
$panelUtils->addHiddenField('webpageId', $webpageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
