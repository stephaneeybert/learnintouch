<?PHP

require_once("website.php");

if (!isset($shopCategoryId)) {
  $shopCategoryId = LibEnv::getEnvHttpPOST("shopCategoryId");
}

// Prevent sql injection attacks as the id is always numeric
$shopCategoryId = (int) $shopCategoryId;

$preferenceUtils->init($shopItemUtils->preferences);
$displayAll = $preferenceUtils->getValue("SHOP_DISPLAY_ALL");

if (!$displayAll) {
  if (!$shopCategoryId) {
    $shopCategoryId = LibSession::getSessionValue(SHOP_SESSION_CATEGORY);
  } else {
    LibSession::putSessionValue(SHOP_SESSION_CATEGORY, $shopCategoryId);
  }
}

$str = $shopItemUtils->renderList($shopCategoryId);

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
