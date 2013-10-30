<?php

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $websiteSubscriptionId = LibEnv::getEnvHttpPOST("websiteSubscriptionId");
  $websiteId = LibEnv::getEnvHttpPOST("websiteId");

  $websiteSubscriptionUtils->delete($websiteSubscriptionId);

  $str = LibHtml::urlRedirect("$gWebsiteUrl/subscription/admin.php?websiteId=$websiteId");
  printContent($str);
  return;

} else {

  $websiteSubscriptionId = LibEnv::getEnvHttpGET("websiteSubscriptionId");
  $websiteId = LibEnv::getEnvHttpGET("websiteId");

  // If a subscription exists then get current properties
  $openingDate = '';
  $fee = '';
  $duration = '';
  $terminationDate = '';
  if ($websiteSubscription = $websiteSubscriptionUtils->selectById($websiteSubscriptionId)) {
    $openingDate = $websiteSubscription->getOpeningDate();
    $fee = $websiteSubscription->getFee($fee);
    $duration = $websiteSubscription->getDuration($duration);
    $terminationDate = $websiteSubscription->getTerminationDate();
    $websiteId = $websiteSubscription->getWebsiteId();
  }

  if ($website = $websiteUtils->selectById($websiteId)) {
    $domainName = $website->getDomainName();
  }

  $openingDate = $clockUtils->systemToLocalNumericDate($openingDate);

  if ($clockUtils->systemDateIsSet($terminationDate)) {
    $terminationDate = $clockUtils->systemToLocalNumericDate($terminationDate);
  } else {
    $terminationDate = '';
  }

  $panelUtils->setHeader($mlText[0], "$gWebsiteUrl/subscription/admin.php?websiteId=$websiteId");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $domainName);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[13], "br"), $fee);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), $openingDate);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[15], "br"), $duration);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $terminationDate);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('websiteSubscriptionId', $websiteSubscriptionId);
  $panelUtils->addHiddenField('websiteId', $websiteId);
  $panelUtils->closeForm();

  $str = $panelUtils->render();

  printAdminPage($str);

}

?>
