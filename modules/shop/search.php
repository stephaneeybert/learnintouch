<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $currentCategoryId = LibEnv::getEnvHttpPOST("currentCategoryId");
  $priceMin = LibEnv::getEnvHttpPOST("priceMin");
  $priceMax = LibEnv::getEnvHttpPOST("priceMax");
  $reference = LibEnv::getEnvHttpPOST("reference");
  $pattern = LibEnv::getEnvHttpPOST("pattern");
  $available = LibEnv::getEnvHttpPOST("available");

  $currentCategoryId = LibString::cleanString($currentCategoryId);
  $priceMin = LibString::cleanString($priceMin);
  $priceMax = LibString::cleanString($priceMax);
  $reference = LibString::cleanString($reference);
  $pattern = LibString::cleanString($pattern);
  $available = LibString::cleanString($available);

  LibSession::putSessionValue(SHOP_SESSION_REFERENCE, $reference);
  LibSession::putSessionValue(SHOP_SESSION_SEARCH_PATTERN, $pattern);
  LibSession::putSessionValue(SHOP_SESSION_AVAILABLE, $available);
  LibSession::putSessionValue(SHOP_SESSION_CATEGORY, $currentCategoryId);
  LibSession::putSessionValue(SHOP_SESSION_PRICE_MIN, $priceMin);
  LibSession::putSessionValue(SHOP_SESSION_PRICE_MAX, $priceMax);

  if (count($warnings) == 0) {
    $str = $shopItemUtils->renderSearchList($reference, $pattern, $priceMin, $priceMax, $available, $currentCategoryId);

    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");
    exit;
  }

} else {

  $reference = LibSession::getSessionValue(SHOP_SESSION_REFERENCE);
  $pattern = LibSession::getSessionValue(SHOP_SESSION_SEARCH_PATTERN);
  $available = LibSession::getSessionValue(SHOP_SESSION_AVAILABLE);
  $currentCategoryId = LibSession::getSessionValue(SHOP_SESSION_CATEGORY);
  $priceMin = LibSession::getSessionValue(SHOP_SESSION_PRICE_MIN);
  $priceMax = LibSession::getSessionValue(SHOP_SESSION_PRICE_MAX);

}

$categoryList = array(' ' => '');
if ($categories = $shopCategoryUtils->getCategoryNames()) {
  foreach ($categories as $wCategoryId => $wName) {
    $categoryList[$wCategoryId] = $wName;
  }
}
$strSelectCategory = LibHtml::getSelectList("currentCategoryId", $categoryList, $currentCategoryId);

$availableList = array(
  '',
  7 => $websiteText[43],
  30 => $websiteText[44],
  90 => $websiteText[45],
  180 => $websiteText[46],
  365 => $websiteText[47],
);
$strSelectAvailable = LibHtml::getSelectList("available", $availableList, $available);

$preferenceUtils->init($shopItemUtils->preferences);

$str = '';

$str .= "\n<div class='shop_item'>";

$str .= $commonUtils->renderWarningMessages($warnings);

$strViewList = "\n<a href='$gShopUrl/display_list.php' $gJSNoStatus title='" .  $websiteText[29] . "'>"
  . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_ITEM_LIST . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

$strViewSelection = "\n<a href='$gShopUrl/item/selection.php' $gJSNoStatus title='" .  $websiteText[28]
  . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_SELECTION . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

$url = "$gContactUrl/post.php";
$strContact = "\n<a href='$url' $gJSNoStatus title='" . $websiteText[27] . "'>"
  . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL . "' class='no_style_image_icon' title='' alt='' /></a>";

$str .= "\n<div class='shop_item_icon'>"
  . "\n $strViewList"
  . "\n $strViewSelection"
  . "\n $strContact"
  . "\n</div>";

$str .= "\n<form action='$gShopUrl/search.php' method='post' name='edit' id='edit'>";

if (count($categories) > 1) {
  $str .= "\n<div class='shop_item_label'>$websiteText[6]</div>";
  $str .= "\n<div class='shop_item_field'>$strSelectCategory</div>";
}

$hidePrice = $preferenceUtils->getValue("SHOP_HIDE_PRICE");
if (!$hidePrice) {
  $label = $popupUtils->getUserTipPopup($websiteText[8], $websiteText[1], 300, 200);
  $str .= "\n<div class='shop_item_label'>$label</div>";
  $str .= "\n<div class='shop_item_field'>"
    . "<input class='system_input' type='text' name='priceMin' value='$priceMin' size='25' maxlength='50' /></div>";
  $label = $popupUtils->getUserTipPopup($websiteText[49], $websiteText[2], 300, 200);
  $str .= "\n<div class='shop_item_label'>$label</div>";
  $str .= "\n<div class='shop_item_field'>"
    . "<input class='system_input' type='text' name='priceMax' value='$priceMax' size='25' maxlength='50' /></div>";
}

$hideText = $preferenceUtils->getValue("SHOP_SEARCH_HIDE_TEXT");
if (!$hideText) {
  $label = $popupUtils->getUserTipPopup($websiteText[13], $websiteText[14], 300, 200);
  $str .= "\n<div class='shop_item_label'>$label</div>";
  $str .= "\n<div class='shop_item_field'><input class='system_input' type='text' name='pattern' size='25' maxlength='50' value='$pattern' /></div>";
}

$hideReference = $preferenceUtils->getValue("SHOP_SEARCH_HIDE_REFERENCE");
if (!$hideReference) {
  $label = $popupUtils->getUserTipPopup($websiteText[5], $websiteText[15], 300, 200);
  $str .= "\n<div class='shop_item_label'>$label</div>";
  $str .= "\n<div class='shop_item_field'><input class='system_input' type='text' name='reference' size='25' maxlength='50' value='$reference' /></div>";
}

$hidePeriod = $preferenceUtils->getValue("SHOP_SEARCH_HIDE_PERIOD");
if (!$hidePeriod) {
  $label = $popupUtils->getUserTipPopup($websiteText[48], $websiteText[16], 300, 200);
  $str .= "\n<div class='shop_item_label'>$label</div>";
  $str .= "\n<div class='shop_item_field'>$strSelectAvailable</div>";
}

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[3]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
