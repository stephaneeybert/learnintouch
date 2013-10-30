<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();

$websiteId = LibEnv::getEnvHttpGET("websiteId");

$domainName = '';
if ($website = $websiteUtils->selectById($websiteId)) {
  $domainName = $website->getDomainName();
}

$panelUtils->setHeader($mlText[0], "$gWebsiteUrl/admin.php");

$panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $domainName, '', '', '', '');
$panelUtils->addLine();

$strCommand = "<a href='$gWebsiteUrl/subscription/edit.php?websiteId=$websiteId' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[2]'></a>"
. " <a href='$gWebsiteUrl/subscription/renew.php?websiteId=$websiteId' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageRenew' title='$mlText[10]'></a>";


$panelUtils->addLine($panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[15], "nb"), $panelUtils->addCell($mlText[7], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$websiteSubscriptions = $websiteSubscriptionUtils->selectByWebsiteId($websiteId);

$panelUtils->openList();
foreach ($websiteSubscriptions as $websiteSubscription) {
  $websiteSubscriptionId = $websiteSubscription->getId();
  $openingDate = $websiteSubscription->getOpeningDate();
  $fee = $websiteSubscription->getFee();
  $duration = $websiteSubscription->getDuration();
  $autoRenewal = $websiteSubscription->getAutoRenewal();
  $terminationDate = $websiteSubscription->getTerminationDate();
  $websiteId = $websiteSubscription->getWebsiteId();

  $strCommand = "<a href='$gWebsiteUrl/subscription/edit.php?websiteSubscriptionId=$websiteSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[6]'></a>"
    . " <a href='$gWebsiteUrl/subscription/order.php?websiteSubscriptionId=$websiteSubscriptionId&websiteId=$websiteId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageShopOrder' title='$mlText[8]'></a>"
    . " <a href='$gWebsiteUrl/subscription/delete.php?websiteSubscriptionId=$websiteSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

  next($websiteSubscriptions);

  if ($autoRenewal) {
    $strAutoRenewal = "<img border='0' src='$gCommonImagesUrl/$gImageTrue' title=''>";
  } else {
    $strAutoRenewal = '';
  }

  if ($clockUtils->systemDateIsSet($openingDate)) {
    $openingDate = $clockUtils->systemToLocalNumericDate($openingDate);
  } else {
    $openingDate = '';
  }

  if ($clockUtils->systemDateIsSet($terminationDate)) {
    $terminationDate = $clockUtils->systemToLocalNumericDate($terminationDate);
  } else {
    $terminationDate = '';
  }

  $panelUtils->addLine($panelUtils->addCell($fee, "n"), $panelUtils->addCell("$openingDate", "n"), $panelUtils->addCell($duration, "n"), $panelUtils->addCell($strAutoRenewal, "n"), $panelUtils->addCell($terminationDate, "n"), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
