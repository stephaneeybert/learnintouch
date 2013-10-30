<?php

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($shopItemUtils->preferences);

$shopItemUtils->emptyCart();

$message = $preferenceUtils->getValue("SHOP_PAYMENT_INCOMPLETE");

if (!$message) {
  $message = $websiteText[1];
}

$str = "\n<div class='system'>"
  . "<div class='system_comment'>$message</div>";

$shopOrderId = LibSession::getSessionValue(SHOP_SESSION_ORDER);
if ($shopOrderId) {
  if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
    $str .= "\n<div class='system_comment'>"
     . "<a href='$gShopUrl/order/confirm.php?shopOrderId=$shopOrderId' $gJSNoStatus>"
      . $websiteText[0]
      . "</a>"
      . "</div>";
  }
}

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
