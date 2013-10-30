<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$email = LibEnv::getEnvHttpPOST("email");

$email = LibString::cleanString($email);

if ($email) {

  // Validate the email
  if (!LibEmail::validate($email)) {
    array_push($warnings, $mlText[21]);
  }

  if (count($warnings) == 0) {

    if (!$mailAddress = $mailAddressUtils->selectByEmail($email)) {
      $mailAddress = new MailAddress();
      $mailAddress->setEmail($email);
      $mailAddress->setSubscribe(true);
      $mailAddressUtils->insert($mailAddress);
    }

  }

}

$currentSubscribe = LibEnv::getEnvHttpPOST("currentSubscribe");

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(MAIL_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(MAIL_SESSION_SEARCH_PATTERN, $searchPattern);
}
$searchPattern = LibString::cleanString($searchPattern);

$searchCountry = LibEnv::getEnvHttpPOST("searchCountry");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
if (!$searchCountry && !$searchSubmitted) {
  $searchCountry = LibSession::getSessionValue(MAIL_SESSION_SEARCH_COUNTRY);
} else {
  LibSession::putSessionValue(MAIL_SESSION_SEARCH_COUNTRY, $searchCountry);
}
$searchCountry = LibString::cleanString($searchCountry);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 400);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gMailUrl/list/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEmailList' title='$mlText[22]'></a>"
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[5]'>", "$gMailUrl/address/import.php", 600, 600)
  . " <a href='$gMailUrl/address/deleteImport.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[7]'></a>"
  . " <a href='$gMailUrl/address/export.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageExport' title='$mlText[11]'></a>";

// Get the number of email addresses
$nbAddress = $mailAddressUtils->countAll();

$subscribeList = array(
  ' ' => '',
  1 => $mlText[13],
  -1 => $mlText[14],
);
$strSelectSubscribe = "<form action='$PHP_SELF' method='post'>"
  . LibHtml::getSelectList("currentSubscribe", $subscribeList, $currentSubscribe, true)
  . "</form>";

$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$strCountrySearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchCountry' size='20' maxlength='50' value='$searchCountry'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$strAddEmail = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='email' size='20' maxlength='255' value=''> "
  .  $panelUtils->getTinyOk()
  . "</form>";

$labelStatus = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 300);
$labelSearch = $popupUtils->getTipPopup($mlText[9], $mlText[10], 300, 300);
$labelEmail = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 300);
$labelCountry = $popupUtils->getTipPopup($mlText[24], $mlText[25], 300, 300);
$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), $panelUtils->addCell($labelCountry, "nbr"), $panelUtils->addCell($strCountrySearch, "n"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine($panelUtils->addCell($labelEmail, "nbr"), $panelUtils->addCell($strAddEmail, "n"), $panelUtils->addCell($labelStatus, "nbr"), $strSelectSubscribe, '', '');

$strCommand = "<a href='$gMailUrl/address/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $panelUtils->addCell($nbAddress, "n"), '', '', '', '');
$panelUtils->addLine($panelUtils->addCell($mlText[12], "nb"), $panelUtils->addCell($mlText[16], "nb"), $panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($mlText[15], "nb"), $panelUtils->addCell($mlText[23], "nb"), $panelUtils->addCell($strCommand, "nr"));

$preferenceUtils->init($mailUtils->preferences);
$listStep = $preferenceUtils->getValue("MAIL_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $mailAddresses = $mailAddressUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($searchCountry) {
  $mailAddresses = $mailAddressUtils->selectLikeCountry($searchCountry, $listIndex, $listStep);
} else if ($currentSubscribe == 1) {
  $mailAddresses = $mailAddressUtils->selectSubscribers($listIndex, $listStep);
} else if ($currentSubscribe == -1) {
  $mailAddresses = $mailAddressUtils->selectNonSubscribers($listIndex, $listStep);
} else {
  $mailAddresses = $mailAddressUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $mailAddressUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($mailAddresses as $mailAddress) {
  $mailAddressId = $mailAddress->getId();
  $email = $mailAddress->getEmail();
  $firstname = $mailAddress->getFirstname();
  $lastname = $mailAddress->getLastname();
  $comment = $mailAddress->getComment();
  $country = $mailAddress->getCountry();

  $strCommand = ''
    . " <a href='$gMailUrl/address/edit.php?mailAddressId=$mailAddressId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gMailUrl/address/delete.php?mailAddressId=$mailAddressId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($firstname, $lastname, $email, $comment, $country, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("mail_address_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
