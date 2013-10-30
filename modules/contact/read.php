<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contactId = LibEnv::getEnvHttpPOST("contactId");
  $status = LibEnv::getEnvHttpPOST("status");

  $status = LibString::cleanString($status);

  if ($link = $contactUtils->selectById($contactId)) {
    $link->setStatus($status);
    $contactUtils->update($link);
  }

  $str = LibHtml::urlRedirect("$gContactUrl/admin.php");
  printContent($str);
  return;

} else {

  $contactId = LibEnv::getEnvHttpGET("contactId");

  // The administrator may access this page without being logged in if a unique token is used
  // This allows a administrator to access this page by clicking on a link in an email
  $tokenName = LibEnv::getEnvHttpGET("tokenName");
  $tokenValue = LibEnv::getEnvHttpGET("tokenValue");
  if ($uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
    // In case the website email is also the one of a registered admin then log in the admin
    $siteEmail = LibEnv::getEnvHttpGET("siteEmail");
    if ($admin = $adminUtils->selectByEmail($siteEmail)) {
      $login = $admin->getLogin();
      $adminUtils->logIn($login);
    }
  } else {
    // If no token is used, then
    // check that the administrator is allowed to use the module
    $adminModuleUtils->checkAdminModule(MODULE_CONTACT);
  }

  if (!$contact = $contactUtils->selectById($contactId)) {
    $str = LibHtml::urlRedirect("$gContactUrl/admin.php", $gRedirectDelay);
    printMessage($str);
    return;
  }

  $contactId = $contact->getId();
  $firstname = $contact->getFirstname();
  $lastname = $contact->getLastname();
  $email = $contact->getEmail();
  $organisation = $contact->getOrganisation();
  $telephone = $contact->getTelephone();
  $subject = $contact->getSubject();
  $message = $contact->getMessage();
  $status = $contact->getStatus();
  $contactRefererId = $contact->getContactRefererId();

  if ($contactReferer = $contactRefererUtils->selectById($contactRefererId)) {
    $refererDescription = $contactReferer->getDescription();
    $languageCode = $languageUtils->getCurrentAdminLanguageCode();
    $refererDescription = $languageUtils->getTextForLanguage($refererDescription, $languageCode);
  } else {
    $refererDescription = '';
  }

  $contactStatuses = $contactStatusUtils->selectAll();
  $contactStatusList = Array('' => '');
  foreach ($contactStatuses as $contactStatus) {
    $wContactStatusId = $contactStatus->getId();
    $wName = $contactStatus->getName();
    $contactStatusList[$wContactStatusId] = $wName;
  }
  $strSelect = LibHtml::getSelectList("status", $contactStatusList, $status);

  $strCommand = " <a href='$gContactUrl/delete.php?contactId=$contactId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[7]'></a>";

  $panelUtils->setHeader($mlText[0], "$gContactUrl/admin.php");
  $panelUtils->addLine('', $panelUtils->addCell($strCommand, "nbr"));
  $panelUtils->openForm($PHP_SELF);
  if ($firstname || $lastname) {
    $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$firstname $lastname");
    $panelUtils->addLine();
  }
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<a href='mailto:$email'>$email</a>");
  $panelUtils->addLine();
  if ($subject) {
    $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $subject);
    $panelUtils->addLine();
  }
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $message);
  $panelUtils->addLine();
  if ($organisation) {
    $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $organisation);
    $panelUtils->addLine();
  }
  if ($telephone) {
    $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $telephone);
    $panelUtils->addLine();
  }
  if ($refererDescription) {
    $panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $refererDescription);
    $panelUtils->addLine();
  }
  $label = $popupUtils->getTipPopup($mlText[10], $mlText[9], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelect);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contactId', $contactId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
