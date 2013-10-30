<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gShopUrl/order/admin.php");

$panelUtils->openList();
$strCommand = "<a href='$gShopUrl/payment/paypal/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[1]'></a>";
$panelUtils->addLine("<a href='$gShopUrl/payment/paypal/edit.php' title='" . $mlText[1] . "' $gJSNoStatus>$mlText[2]</a>", $panelUtils->addCell($strCommand, "nr"));
$strCommand = "<a href='$gShopUrl/payment/transfer/bank.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[4]'></a>";
$panelUtils->addLine("<a href='$gShopUrl/payment/transfer/bank.php' title='" . $mlText[4] . "' $gJSNoStatus>$mlText[3]</a>", $panelUtils->addCell($strCommand, "nr"));
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
