<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), '');
$panelUtils->addLine();

$systemPages = $templateUtils->getSystemPages(true);
foreach ($systemPages as $pageId => $pageDescription) {

  // The user login is required on the user system pages
  if (strstr($pageId, 'SYSTEM_PAGE_USER')) {
    continue;
    }

  if ($userUtils->isSecuredPage($pageId)) {
    $mlTextSecure = $mlText[8];
    $imageSecure = $gImageUnlock;
    $secure = 0;
    } else {
    $mlTextSecure = $mlText[7];
    $imageSecure = $gImageLock;
    $secure = 1;
    }

  if ($userUtils->isSecuredPage($pageId)) {
    $strSecured = " <img border='0' src='$gCommonImagesUrl/$gImagePassword' title='$mlText[9]'>";
    } else {
    $strSecured = '';
    }

  $strCommand = "<a href='$gDynpageUrl/secureSystemPage.php?pageId=$pageId&secure=$secure' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$imageSecure' title='$mlTextSecure'></a>";

  $panelUtils->addLine($pageDescription, $strSecured, $panelUtils->addCell($strCommand, "nr"));
  }

$str = $panelUtils->render();

printAdminPage($str);

?>
