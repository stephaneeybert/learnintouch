<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$contactStatusId = LibEnv::getEnvHttpPOST("contactStatusId");

if (!$contactStatusId) {
  $contactStatusId = LibSession::getSessionValue(CONTACT_SESSION_STATUS);
} else {
  LibSession::putSessionValue(CONTACT_SESSION_STATUS, $contactStatusId);
}

if (!$contactStatusId) {
  if ($status = $contactStatusUtils->selectFirst()) {
    $contactStatusId = $status->getId();
  }
}

$contactStatusList = array('-1' => '');
$contactStatuses = $contactStatusUtils->selectAll();
if (count($contactStatuses) > 0) {
  foreach ($contactStatuses as $contactStatus) {
    $wId = $contactStatus->getId();
    $wName = $contactStatus->getName();
    $contactStatusList[$wId] = $wName;
  }

  $strSelect = LibHtml::getSelectList("contactStatusId", $contactStatusList, $contactStatusId, true);
} else {
  $strSelect = '';
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 500);
$panelUtils->setHelp($help);

$strCommand = "<a href='$gContactUrl/status/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageStatus' title='$mlText[6]'></a>"
  . " <a href='$gContactUrl/referer/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[16]'></a>"
  . " <a href='$gUtilsUrl/download.php?filename=$gContactPath" . "formfile.zip' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDownload' title='$mlText[15]'></a>"
  . " <a href='$gContactUrl/empty.php?contactStatusId=$contactStatusId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[11]'></a>"
  . " <a href='$gContactUrl/garbage.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageGarbage' title='$mlText[32]'></a>"
  . " <a href='$gContactUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[7]'></a>";

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $panelUtils->addCell($strSelect, "n"), '', '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->closeForm();
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell("$mlText[12]", "nb"), $panelUtils->addCell("$mlText[1]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[8]", "nb"), '');

$preferenceUtils->init($contactUtils->preferences);
$listStep = $preferenceUtils->getValue("CONTACT_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($contactStatusId > 0) {
  $contacts = $contactUtils->selectByStatus($contactStatusId, $listIndex, $listStep);
} else {
  $contacts = $contactUtils->selectNonGarbage($listIndex, $listStep);
}

$listNbItems = $contactUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($contacts as $contact) {
  $contactId = $contact->getId();
  $firstname = $contact->getFirstname();
  $lastname = $contact->getLastname();
  $email = $contact->getEmail();
  $organisation = $contact->getOrganisation();
  $telephone = $contact->getTelephone();
  $subject = $contact->getSubject();
  $message = $contact->getMessage();
  $contactDate = $contact->getContactDate();
  $contactTime = $clockUtils->dateTimeToSystemTime($contact->getContactDate());

  // Transform the date into a local numeric date
  $contactDate = $clockUtils->systemToLocalNumericDate($contactDate);

  $strEmail = "<a href='$gContactUrl/read.php?contactId=$contactId' $gJSNoStatus title='$mlText[14]' href=''>"
    . $email . "</a>";

  $strSubject = "<a href='$gContactUrl/read.php?contactId=$contactId' $gJSNoStatus title='$mlText[14]' href=''>$subject</a>";

  $strDate = "$contactDate $contactTime";

  $strCommand = "<a href='$gContactUrl/read.php?contactId=$contactId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[2]'></a>"
    . " <a href='$gContactUrl/delete.php?contactId=$contactId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$firstname $lastname", $strEmail, $strSubject, $strDate, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("contact_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
