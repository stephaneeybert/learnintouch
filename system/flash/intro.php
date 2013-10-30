<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FLASH);

$mlText = $languageUtils->getMlText(__FILE__);

$introFlashId = $flashUtils->getIntroFlashId();
if ($flash = $flashUtils->selectById($introFlashId)) {
  $flashId = $flash->getId();
  } else {
  $flash = new Flash();
  $flashUtils->insert($flash);
  $flashId = $flashUtils->getLastInsertId();
  $flashUtils->setIntroFlashId($flashId);
  }

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'>", "$gFlashUrl/edit.php?flashId=$flashId", 700, 600)
  . " <a href='$gFlashUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[3]'></a>";

$strFlash = "<a href='http://www.macromedia.com/go/getflashplayer/' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageGetFlash' title='$mlText[4]'></a>";
$panelUtils->addLine($strFlash, $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$flashObject = $flashUtils->renderFlashIntroObject();

$panelUtils->addLine($panelUtils->addCell($flashObject, "c"));
$panelUtils->addLine();

$str = $panelUtils->render();

printAdminPage($str);

?>
