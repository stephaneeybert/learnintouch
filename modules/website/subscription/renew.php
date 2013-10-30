<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();


$websiteId = LibEnv::getEnvHttpGET("websiteId");

$websiteSubscriptionUtils->renew($websiteId);

$str = LibHtml::urlRedirect("$gWebsiteUrl/subscription/admin.php?websiteId=$websiteId");
printContent($str);
return;

?>
