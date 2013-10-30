<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(ADMIN_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(ADMIN_SESSION_SEARCH_PATTERN, $searchPattern);
}

$searchPattern = LibString::cleanString($searchPattern);

$loginSession = $adminUtils->checkAdminLogin();

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 400);
$panelUtils->setHelp($help);

$strCommand = " <a href='$gAdminUrl/password.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePassword' title='$mlText[11]'></a>"
  . " <a href='$gAdminUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[12]'></a>";

if ($adminUtils->isSuperAdmin($loginSession)) {
  $labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
  $strSearch = "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
    . $panelUtils->getTinyOk()
    . "<input type='hidden' name='searchSubmitted' value='1'> ";

  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '', $panelUtils->addCell($strCommand, "nr"));
  $panelUtils->closeForm();
} else {
  $panelUtils->addLine('', '', '', $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->addLine();

$strCommand = '';
// Only a super admin can create an adinistrator
if ($adminUtils->isSuperAdmin($loginSession)) {
  $strCommand .= "<a href='$gAdminUrl/register.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[2]'></a>";
}

$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[7], "nb"), $panelUtils->addCell($strCommand, "nbr"));

$websiteEmail = $profileUtils->getProfileValue("website.email");
if (!$websiteEmail || !$adminUtils->selectByEmail($websiteEmail)) {
  $label = $popupUtils->getTipPopup($mlText[13], $mlText[14], 300, 300);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($label, "w"));
}

$preferenceUtils->init($adminUtils->preferences);
$listStep = $preferenceUtils->getValue("ADMIN_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $admins = $adminUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($adminUtils->isStaffLogin($loginSession)) {
  $admins = $adminUtils->selectAll($listIndex, $listStep);
} else {
  $admins = $adminUtils->selectAllNonSuperAdminAndLoggedOne($loginSession, $listIndex, $listStep);
}

$listNbItems = $adminUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($admins as $admin) {
  $adminId = $admin->getId();
  $firstname = $admin->getFirstname();
  $lastname = $admin->getLastname();
  $login = $admin->getLogin();
  $superAdmin = $admin->getSuperAdmin();
  $email = $admin->getEmail();

  $strCommand = "<a href='$gAdminUrl/edit.php?adminId=$adminId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[6]'></a>";

  // A non super admin cannot see any other admin
  if (!$adminUtils->isSuperAdmin($loginSession)) {
    if ($loginSession != $login) {
      continue;
    }
  }

  // Only a super admin can change the password of another admin
  if ($adminUtils->isSuperAdmin($loginSession)) {
    $strCommand .= " <a href='$gAdminUrl/setPassword.php?adminId=$adminId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImagePassword' title='$mlText[10]'></a>";
  }

  // Only a super admin can edit the module permissions
  if ($adminUtils->isSuperAdmin($loginSession)) {
    $strCommand .= " <a href='$gAdminUrl/module/edit.php?adminId=$adminId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImagePermission' title='$mlText[8]'></a>";
  }

  // Only a super admin can delete another admin
  if ($adminUtils->isSuperAdmin($loginSession)) {
    // A staff admin cannot be deleted
    if (!$adminUtils->isStaffLogin($login)) {
      // An admin cannot delete oneself
      if ($login != $loginSession) {
        $strCommand .= " <a href='$gAdminUrl/delete.php?adminId=$adminId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";
      }
    }
  }

  $strName = "$firstname $lastname";
  if ($email) {
    $strName = "<a href='mailto:$email'>$strName</a>";
  }

  if ($superAdmin) {
    $strSuperAdmin = "<img border='0' src='$gCommonImagesUrl/$gImageTrue' title='$mlText[3]'>";
  } else {
    $strSuperAdmin = '';
  }

  $panelUtils->addLine($panelUtils->addCell($strName, "n"), $panelUtils->addCell($login, "n"), $strSuperAdmin, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("admin_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
