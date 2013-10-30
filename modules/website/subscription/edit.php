<?php

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $websiteSubscriptionId = LibEnv::getEnvHttpPOST("websiteSubscriptionId");
  $openingDate = LibEnv::getEnvHttpPOST("openingDate");
  $autoRenewal = LibEnv::getEnvHttpPOST("autoRenewal");
  $fee = LibEnv::getEnvHttpPOST("fee");
  $duration = LibEnv::getEnvHttpPOST("duration");
  $terminationDate = LibEnv::getEnvHttpPOST("terminationDate");
  $websiteId = LibEnv::getEnvHttpPOST("websiteId");

  $openingDate = LibString::cleanString($openingDate);
  $autoRenewal = LibString::cleanString($autoRenewal);
  $fee = LibString::cleanString($fee);
  $duration = LibString::cleanString($duration);
  $terminationDate = LibString::cleanString($terminationDate);

  if ($openingDate) {
    $openingDate = $clockUtils->localToSystemDate($openingDate);
  } else {
    $openingDate = $clockUtils->getSystemDate();
  }

  if ($terminationDate) {
    $terminationDate = $clockUtils->localToSystemDate($terminationDate);
  }

  // The duration is a number of months
  if (!is_numeric($duration) || $duration <= 0 || $duration > 12) {
    array_push($warnings, $mlText[17]);
  }

  if (count($warnings) == 0) {

    if ($websiteSubscription = $websiteSubscriptionUtils->selectById($websiteSubscriptionId)) {
      $websiteSubscription->setOpeningDate($openingDate);
      $websiteSubscription->setTerminationDate($terminationDate);
      $websiteSubscription->setFee($fee);
      $websiteSubscription->setDuration($duration);
      $websiteSubscription->setAutoRenewal($autoRenewal);
      $websiteSubscriptionUtils->update($websiteSubscription);
    } else {
      $websiteSubscription = new WebsiteSubscription();
      $websiteSubscription->setOpeningDate($openingDate);
      $websiteSubscription->setTerminationDate($terminationDate);
      $websiteSubscription->setFee($fee);
      $websiteSubscription->setDuration($duration);
      $websiteSubscription->setAutoRenewal($autoRenewal);
      $websiteSubscription->setWebsiteId($websiteId);
      $websiteSubscriptionUtils->insert($websiteSubscription);
      $websiteSubscriptionId = $websiteSubscriptionUtils->getLastInsertId();
    }

    $str = LibHtml::urlRedirect("$gWebsiteUrl/subscription/admin.php?websiteId=$websiteId");
    printContent($str);
    return;

  }

} else {

  $websiteSubscriptionId = LibEnv::getEnvHttpGET("websiteSubscriptionId");
  $websiteId = LibEnv::getEnvHttpGET("websiteId");

  // If a subscription exists then get current properties
  $openingDate = '';
  $fee = '';
  $duration = '';
  $autoRenewal = "1";
  $terminationDate = '';
  if ($websiteSubscription = $websiteSubscriptionUtils->selectById($websiteSubscriptionId)) {
    $openingDate = $websiteSubscription->getOpeningDate();
    $fee = $websiteSubscription->getFee($fee);
    $duration = $websiteSubscription->getDuration($duration);
    $autoRenewal = $websiteSubscription->getAutoRenewal();
    $terminationDate = $websiteSubscription->getTerminationDate();
    $websiteId = $websiteSubscription->getWebsiteId();
  }

}

if ($website = $websiteUtils->selectById($websiteId)) {
  $domainName = $website->getDomainName();
}

// Set a default duration value
if ($duration == 0) {
  $duration = 12;
}

if (!$clockUtils->systemDateIsSet($openingDate)) {
  $openingDate = $clockUtils->getSystemDate();
}

$openingDate = $clockUtils->systemToLocalNumericDate($openingDate);

if ($clockUtils->systemDateIsSet($terminationDate)) {
  $terminationDate = $clockUtils->systemToLocalNumericDate($terminationDate);
} else {
  $terminationDate = '';
}

if ($autoRenewal == '1') {
  $checkedAutoRenewal = "CHECKED";
} else {
  $checkedAutoRenewal = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gWebsiteUrl/subscription/admin.php?websiteId=$websiteId");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $domainName);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[13], "br"), "<input type='text' name='fee' value='$fee' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), "<input type='text' name='openingDate' id='openingDate' value='$openingDate' size='12' maxlength='10' class='date_field'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[15], "br"), "<input type='text' name='duration' value='$duration' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "br"), "<input type='checkbox' name='autoRenewal' $checkedAutoRenewal value='1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), "<input type='text' name='terminationDate' id='terminationDate' value='$terminationDate' size='12' maxlength='10' class='date_field'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('websiteSubscriptionId', $websiteSubscriptionId);
$panelUtils->addHiddenField('websiteId', $websiteId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $(".date_field").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $(".date_field").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
