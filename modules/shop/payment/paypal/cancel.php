<?php

require_once("website.php");

// This page is called by paypal after a cancelled payment
// The order id is passed back by paypal
$shopOrderId = LibEnv::getEnvHttpPOST("shopOrderId");

require_once($gShopPath . "payment/cancel.php");

?>
