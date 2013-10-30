<?php

require_once("website.php");

// Paypal sends a specific request on payment cancel
$merchant_return_link = LibEnv::getEnvHttpGET("merchant_return_link");

require_once($gShopPath . "payment/message_cancel.php");

?>
