<?php


error_log("calling back $PHP_SELF");
$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($shopItemUtils->preferences);

// Empty the shopping cart
$shopItemUtils->emptyCart();

$message = $preferenceUtils->getValue("SHOP_PAYMENT_COMPLETE");
if (!$message) {
  $message = $websiteText[0];
}

error_log("The user message after payment : $message");
$str = "\n<div class='system'>" . "\n<div class='system_comment'>$message</div>" . "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
