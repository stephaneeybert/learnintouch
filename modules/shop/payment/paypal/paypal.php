<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$str = $shopOrderUtils->renderPaypalPayment(0);

printAdminPage($str);

?>
