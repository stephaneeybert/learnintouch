<?php

require_once("website.php");

$adminUtils->checkForStaffLogin();

$websiteSubscriptionId = LibEnv::getEnvHttpGET("websiteSubscriptionId");
$websiteId = LibEnv::getEnvHttpGET("websiteId");

if ($websiteAddress = $websiteAddressUtils->selectByWebsite($websiteId)) {
  $websiteSubscriptionUtils->createOrder($websiteSubscriptionId);

  $str = LibHtml::urlRedirect("$gWebsiteUrl/subscription/admin.php?websiteId=$websiteId");
} else {
  $str = "The website needs an address, so as to create an order.";
}

printContent($str);
return;

?>
