<?php


$websiteText = $languageUtils->getWebsiteText(__FILE__);

if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
  $status = $shopOrder->getStatus();
  if ($status == SHOP_ORDER_STATUS_PENDING) {
    $shopOrderUtils->deleteOrder($shopOrderId);
    }
  }

$str = "\n<div class='system'>"
  . "\n<div class='system_comment'>$websiteText[0]</div>"
  . "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
