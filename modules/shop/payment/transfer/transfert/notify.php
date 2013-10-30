<?php

require_once("website.php");

$paymentShopOrderId = LibEnv::getEnvHttpPOST("shopOrderId");
error_log("paymentShopOrderId $paymentShopOrderId");

// The payment status shall be manually updated to the "paid status",
// by the operator once he has actually received the payment
$paymentStatusCompleted = false;

require_once($gShopPath . "payment/notify.php");

$bankDetailsPage = $shopOrderUtils->getBankDetailsPage();
$url = $templateUtils->renderPageUrl($bankDetailsPage);
error_log("url $url");

$str = LibHtml::urlRedirect($url);
printContent($str);
exit;

?>
