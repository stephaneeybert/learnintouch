<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");

$strCommand = " <a href='$gWebsiteUrl/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[2]'></a>"
. " <a href='$gWebsiteUrl/usage.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageDisk' title='$mlText[12]'></a>"
. " <a href='$gWebsiteUrl/subscription/renew_all.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageRenew' title='$mlText[16]'></a>"
. " <a href='$gWebsiteUrl/subscription/exportInvoice.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageExport' title='$mlText[17]'></a>";

$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[15], "nb"), $panelUtils->addCell($mlText[7], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$websites = $websiteUtils->selectAll();

$panelUtils->openList();
foreach ($websites as $website) {
  $websiteId = $website->getId();
  $name = $website->getName();
  $domainName = $website->getDomainName();
  $firstname = $website->getFirstname();
  $lastname = $website->getLastname();
  $email = $website->getEmail();
  $diskSpace = $website->getDiskSpace();
  $package = $website->getPackage();

  // Get the package name
  $packages = $websiteUtils->getPackageNames();
  if (array_key_exists($package, $packages)) {
    $packageName = $packages[$package];
  } else {
    $packageName = '';
  }

  // Get the opening and termination dates
  $openingDate = '';
  $terminationDate = '';
  $autoRenewal = '';
  $duration = '';
  if ($websiteSubscriptions = $websiteSubscriptionUtils->selectByWebsiteId($websiteId)) {
    if (count($websiteSubscriptions) > 0) {
      $firstWebsiteSubscription = $websiteSubscriptions[0];
      $lastWebsiteSubscription = $websiteSubscriptions[count($websiteSubscriptions) - 1];
      $openingDate = $firstWebsiteSubscription->getOpeningDate();
      $autoRenewal = $lastWebsiteSubscription->getAutoRenewal();
      $duration = $lastWebsiteSubscription->getDuration();
      $terminationDate = $lastWebsiteSubscription->getTerminationDate();
    }
  }

  // Format the possible null value dates
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

  $strCommand = "<a href='$gWebsiteUrl/edit.php?websiteId=$websiteId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[6]'></a>"
    . " <a href='$gWebsiteUrl/subscription/admin.php?websiteId=$websiteId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[11]'></a>"
    . " <a href='$gWebsiteUrl/editAddress.php?websiteId=$websiteId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[10]'></a>"
    . "<a href='$gWebsiteUrl/option/edit.php?websiteId=$websiteId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[18]'></a>"
    . " <a href='$gWebsiteUrl/delete.php?websiteId=$websiteId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

  $strDomainName = "<a href='$domainName'>$name</a>";

  $panelUtils->addLine($panelUtils->addCell($strDomainName, "n"), $panelUtils->addCell($packageName, "n"), $panelUtils->addCell($openingDate, "n"), $panelUtils->addCell($duration, "n"), $panelUtils->addCell($terminationDate, "n"), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
