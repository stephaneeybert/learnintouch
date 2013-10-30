<?php

require_once("website.php");

// This page is called by paypal after a completed payment
// The payment details are passed by paypal

/*
   $paymentShopOrderId = "26";
   $paymentFirstname = "Stephane";
   $paymentLastname = "Eybert";
   $paymentEmail = "mittiprovence@yahoo.se";
   $paymentDate = "10:25:44 Nov 03, 2009 PST";
   $paymentAmount = "1.00";
   $paymentShippingFee = "0.00";
   $paymentHandlingFee = "0.00";
   $paymentPaymentFee = "0.00";
   $paymentCurrency = "EUR";
   $paymentStatus = "Completed";
   $paymentTransactionID = "8U447285GR733601W";
 */
/*
   $postedData = '';
   foreach ($_POST as $key => $value) {
   $postedData .= $key . "==" . $value . "::";
   }
   error_log($postedData);
 */

$paymentShopOrderId = LibEnv::getEnvHttpPOST("invoice");
$paymentFirstname = LibEnv::getEnvHttpPOST("first_name");
$paymentLastname = LibEnv::getEnvHttpPOST("last_name");
$paymentEmail = LibEnv::getEnvHttpPOST("business");
$paymentDate = LibEnv::getEnvHttpPOST("payment_date");
$paymentAmount = LibEnv::getEnvHttpPOST("mc_gross");
$paymentShippingFee = LibEnv::getEnvHttpPOST("mc_shipping");
$paymentHandlingFee = LibEnv::getEnvHttpPOST("mc_handling");
$paymentPaymentFee = LibEnv::getEnvHttpPOST("payment_fee");
$paymentCurrency = LibEnv::getEnvHttpPOST("mc_currency");
$paymentStatus = LibEnv::getEnvHttpPOST("payment_status");
$paymentTransactionID = LibEnv::getEnvHttpPOST("txn_id");

error_log("");
error_log("Paypal payment notification");
error_log("paymentShopOrderId $paymentShopOrderId");
error_log("paymentFirstname $paymentFirstname");
error_log("paymentLastname $paymentLastname");
error_log("paymentEmail $paymentEmail");
error_log("paymentDate $paymentDate");
error_log("paymentAmount $paymentAmount");
error_log("paymentShippingFee $paymentShippingFee");
error_log("paymentHandlingFee $paymentHandlingFee");
error_log("paymentPaymentFee $paymentPaymentFee");
error_log("paymentCurrency $paymentCurrency");
error_log("paymentStatus $paymentStatus");
error_log("paymentTransactionID $paymentTransactionID");

if ($paymentStatus == 'Completed') {
  error_log("Setting paymentStatusCompleted to true");
  $paymentStatusCompleted = true;
} else {
  $paymentStatusCompleted = false;
}

require_once($gShopPath . "payment/notify.php");

?>
