<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();


$websites = $websiteUtils->selectAll();

foreach ($websites as $website) {
  $websiteId = $website->getId();
  $websiteSubscriptionUtils->renew($websiteId);
}

$str = LibHtml::urlRedirect("$gWebsiteUrl/admin.php");
printContent($str);
return;

?>
