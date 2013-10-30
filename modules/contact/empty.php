<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $contactStatusId = LibEnv::getEnvHttpPOST("contactStatusId");

} else if ($formSubmitted == 2) {

  $contactStatusId = LibEnv::getEnvHttpPOST("contactStatusId");

  // A status is required
  if (!$contactStatusId) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    $contacts = $contactUtils->selectByStatus($contactStatusId);
    foreach ($contacts as $contact) {
      $contactId = $contact->getId();
      $contactUtils->putInGarbage($contactId);
    }

    $str = LibHtml::urlRedirect("$gContactUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $contactStatusId = LibEnv::getEnvHttpGET("contactStatusId");

}

$contactStatusList = array('' => '');
$contactStatuses = $contactStatusUtils->selectAll();
if (count($contactStatuses) > 0) {
  foreach ($contactStatuses as $contactStatus) {
    $wId = $contactStatus->getId();
    $wName = $contactStatus->getName();
    $contactStatusList[$wId] = $wName;
  }
}
$strSelect = LibHtml::getSelectList("contactStatusId", $contactStatusList, $contactStatusId, true);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gContactUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[5], 300, 400);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], 'nbr'), $panelUtils->addCell($strSelect, 'n'), '', '', '');
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell("$mlText[12]", "nb"), $panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[8]", "nb"), '');
$panelUtils->addLine();

$contacts = $contactUtils->selectByStatus($contactStatusId);
foreach ($contacts as $contact) {
  $firstname = $contact->getFirstname();
  $lastname = $contact->getLastname();
  $email = $contact->getEmail();
  $subject = $contact->getSubject();
  $contactDate = $clockUtils->systemToLocalNumericDate($contact->getContactDate());
  $contactTime = $clockUtils->dateTimeToSystemTime($contact->getContactDate());

  if ($email) {
    $strEmail = "<a href='mailto:$email'>$email</a>";
  } else {
    $strEmail = '';
  }

  $strDate = "$contactDate $contactTime";

  $panelUtils->addLine($panelUtils->addCell("$firstname $lastname", "n"), $panelUtils->addCell($strEmail, "n"), $panelUtils->addCell($subject, "n"), $panelUtils->addCell($strDate, "n"), '');
}

if (count($contacts) > 0) {
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine('', $panelUtils->addCell($mlText[2], "nbr"), $panelUtils->getOk(), '', '');
  $panelUtils->addHiddenField('formSubmitted', 2);
  $panelUtils->addHiddenField('contactStatusId', $contactStatusId);
  $panelUtils->closeForm();
}

$str = $panelUtils->render();

printAdminPage($str);

?>
