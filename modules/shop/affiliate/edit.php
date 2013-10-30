<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $userId = LibEnv::getEnvHttpPOST("userId");

  // Check that the user is specified
  if (!$user = $userUtils->selectById($userId)) {
    array_push($warnings, $mlText[3]);
  }

  // Check that the user is not already assigned to another affiliate
  if ($shopAffiliate = $shopAffiliateUtils->selectByUserId($userId)) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    $shopAffiliate = new ShopAffiliate();
    $shopAffiliate->setUserId($userId);
    $shopAffiliateUtils->insert($shopAffiliate);

    $str = LibHtml::urlRedirect("$gShopUrl/affiliate/admin.php");
    printContent($str);
    return;

  }

} else {

  $userId = '';

}

$userName = '';
if ($user = $userUtils->selectById($userId)) {
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/affiliate/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 200);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gUserUrl/suggestUsers.php", "userName", "userId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('userId', $userId);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='userName' value='$userName' />");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
