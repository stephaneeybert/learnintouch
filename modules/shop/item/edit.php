<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopItemId = LibEnv::getEnvHttpPOST("shopItemId");
  $name = LibEnv::getEnvHttpPOST("name");
  $shortDescription = LibEnv::getEnvHttpPOST("shortDescription");
  $longDescription = LibEnv::getEnvHttpPOST("longDescription");
  $reference = LibEnv::getEnvHttpPOST("reference");
  $price = LibEnv::getEnvHttpPOST("price");
  $vatRate = LibEnv::getEnvHttpPOST("vatRate");
  $shippingFee = LibEnv::getEnvHttpPOST("shippingFee");
  $categoryId = LibEnv::getEnvHttpPOST("categoryId");
  $url = LibEnv::getEnvHttpPOST("url");
  $hide = LibEnv::getEnvHttpPOST("hide");
  $available = LibEnv::getEnvHttpPOST("available");

  $name = LibString::cleanString($name);
  $shortDescription = LibString::cleanString($shortDescription);
  $longDescription = LibString::cleanString($longDescription);
  $reference = LibString::cleanString($reference);
  $price = LibString::cleanString($price);
  $vatRate = LibString::cleanString($vatRate);
  $shippingFee = LibString::cleanString($shippingFee);
  $url = LibString::cleanString($url);
  $hide = LibString::cleanString($hide);
  $available = LibString::cleanString($available);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[10]);
  }

  // Validate the url
  if ($url && LibUtils::isInvalidUrl($url)) {
    array_push($warnings, $mlText[21]);
  }

  // Format the url
  $url = LibUtils::formatUrl($url);

  // Validate the available
  if ($available && !$clockUtils->isLocalNumericDateValid($available)) {
    array_push($warnings, $mlText[41] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  if ($available) {
    $available = $clockUtils->localToSystemDate($available);
  } else {
    $available = $clockUtils->getSystemDateTime();
  }

  // Get the system date for the added and last modified dates
  $systemDate = $clockUtils->getSystemDateTime();

  // Format the amount
  $price = LibString::formatAmount($price);

  // Format the amount
  $vatRate = LibString::formatAmount($vatRate);

  // Format the amount
  $shippingFee = LibString::formatAmount($shippingFee);

  // If the item is assigned to another category then the item list order must be set according to the
  // category number of items
  // Otherwise the item list order is not changed

  if ($shopItem = $shopItemUtils->selectById($shopItemId)) {
    $currentCategoryId = $shopItem->getCategoryId();
  } else {
    $currentCategoryId = '';
  }

  // It must be a zero and not an empty value otherwise the list order will be reassigned every time
  if (!$currentCategoryId) {
    $currentCategoryId = '0';
  }

  if (count($warnings) == 0) {

    if ($shopItem = $shopItemUtils->selectById($shopItemId)) {
      $shopItem->setName($name);
      $shopItem->setShortDescription($shortDescription);
      $shopItem->setLongDescription($longDescription);
      $shopItem->setReference($reference);
      $shopItem->setPrice($price);
      $shopItem->setVatRate($vatRate);
      $shopItem->setShippingFee($shippingFee);
      $shopItem->setCategoryId($categoryId);
      $shopItem->setUrl($url);
      // Check if the category is changed
      if ($currentCategoryId != $categoryId) {
        // Get the next list order
        $listOrder = $shopItemUtils->getNextListOrder($categoryId);
        $shopItem->setListOrder($listOrder);
      }
      $shopItem->setHide($hide);
      $shopItem->setLastModified($systemDate);
      $shopItem->setAvailable($available);
      $shopItemUtils->update($shopItem);
    } else {
      $shopItem = new ShopItem();
      $shopItem->setName($name);
      $shopItem->setShortDescription($shortDescription);
      $shopItem->setLongDescription($longDescription);
      $shopItem->setReference($reference);
      $shopItem->setPrice($price);
      $shopItem->setVatRate($vatRate);
      $shopItem->setShippingFee($shippingFee);
      $shopItem->setCategoryId($categoryId);
      $shopItem->setUrl($url);
      $listOrder = $shopItemUtils->getNextListOrder($categoryId);
      $shopItem->setListOrder($listOrder);
      $shopItem->setHide($hide);
      $shopItem->setAdded($systemDate);
      $shopItem->setLastModified($systemDate);
      $shopItem->setAvailable($available);
      $shopItemUtils->insert($shopItem);
      $shopItemId = $shopItemUtils->getLastInsertId();
    }

    $str = LibHtml::urlRedirect("$gShopUrl/item/admin.php");
    printContent($str);
    return;

  }

} else {

  $shopItemId = LibEnv::getEnvHttpGET("shopItemId");

  $name = '';
  $shortDescription = '';
  $longDescription = '';
  $reference = '';
  $price = '';
  $vatRate = '';
  $shippingFee = '';
  $categoryId = '';
  $url = '';
  $hide = '';
  $available = '';
  if ($shopItemId) {
    if ($shopItem = $shopItemUtils->selectById($shopItemId)) {
      $name = $shopItem->getName();
      $shortDescription = $shopItem->getShortDescription();
      $longDescription = $shopItem->getLongDescription();
      $reference = $shopItem->getReference();
      $price = $shopItem->getPrice();
      $vatRate = $shopItem->getVatRate();
      $shippingFee = $shopItem->getShippingFee();
      $categoryId = $shopItem->getCategoryId();
      $url = $shopItem->getUrl();
      $hide = $shopItem->getHide();
      $available = $shopItem->getAvailable();
    }
  } else {
    $categoryId = LibSession::getSessionValue(SHOP_SESSION_CATEGORY);
  }

}

if (!$vatRate) {
  $vatRate = $shopItemUtils->getVatRate();
}

$price = $shopItemUtils->decimalFormat($price);
$vatRate = $shopItemUtils->decimalFormat($vatRate);
$shippingFee = $shopItemUtils->decimalFormat($shippingFee);

if (!$clockUtils->systemDateIsSet($available)) {
  $available = $clockUtils->getSystemDateTime();
}

$available = $clockUtils->systemToLocalNumericDate($available);

$categories = $shopCategoryUtils->getCategoryNames();
$categoryList = Array('' => '');
foreach ($categories as $wCategoryId => $wName) {
  $categoryList[$wCategoryId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("categoryId", $categoryList, $categoryId);

if ($hide == '1') {
  $checkedHide = "CHECKED";
} else {
  $checkedHide = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/item/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='shortDescription' value='$shortDescription' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<textarea name='longDescription' cols='28' rows='6'>$longDescription</textarea>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='reference' value='$reference' size='30' maxlength='30'>");
$panelUtils->addLine();
$preferenceUtils->init($shopItemUtils->preferences);
$currency = $preferenceUtils->getValue("SHOP_CURRENCY");
if ($vatRate > 0) {
  $label = $mlText[11];
} else {
  $label = $mlText[3];
}
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='price' value='$price' size='10' maxlength='30'> $currency");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[12], "nbr"), "<input type='text' name='vatRate' value='$vatRate' size='6' maxlength='6'> %");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shippingFee' value='$shippingFee' size='10' maxlength='30'> $currency");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[27], $mlText[28], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hide' $checkedHide value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[43], $mlText[44], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='available' id='available' value='$available' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelectCategory);
$panelUtils->addLine();

$label = $popupUtils->getTipPopup($mlText[8], $mlText[30], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='url' value='$url' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopItemId', $shopItemId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#available").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#available").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
