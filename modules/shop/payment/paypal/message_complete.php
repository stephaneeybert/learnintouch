<?php

require_once("website.php");

// Paypal sends a specific request on payment completion
$merchant_return_link = LibEnv::getEnvHttpGET("merchant_return_link");
error_log("The merchant_return_link $merchant_return_link returned by paypal in $PHP_SELF");

require_once($gShopPath . "payment/message_complete.php");

?>
