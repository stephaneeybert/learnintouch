<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gContactUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->addLine();

$strCommand = " <a href='$gContactUrl/emptyGarbage.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[7]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$contacts = $contactUtils->selectGarbage();

$panelUtils->openList();
foreach ($contacts as $contact) {
  $contactId = $contact->getId();

  $firstname = $contact->getFirstname();
  $lastname = $contact->getLastname();
  $email = $contact->getEmail();
  $subject = $contact->getSubject();

  $strCommand = "<a href='$gContactUrl/restore.php?contactId=$contactId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageRestore' title='$mlText[11]'></a>";

  $panelUtils->addLine($panelUtils->addCell($subject, "n"), "$firstname $lastname $email", $panelUtils->addCell("$strCommand", "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
