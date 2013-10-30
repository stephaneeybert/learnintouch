<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopAffiliateId = LibEnv::getEnvHttpPOST("shopAffiliateId");

  if (count($warnings) == 0) {
    $shopAffiliateUtils->deleteAffiliate($shopAffiliateId);

    $str = LibHtml::urlRedirect("$gShopUrl/affiliate/admin.php");
    printContent($str);
    return;
  }

} else {

  $shopAffiliateId = LibEnv::getEnvHttpGET("shopAffiliateId");

}

$firstname = '';
$lastname = '';
$email = '';
if ($shopAffiliate = $shopAffiliateUtils->selectById($shopAffiliateId)) {
  $userId = $shopAffiliate->getUserId();
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $email = $user->getEmail();
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/affiliate/admin.php");
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "$firstname $lastname");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $email);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopAffiliateId', $shopAffiliateId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
