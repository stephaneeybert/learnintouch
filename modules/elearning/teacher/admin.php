<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$adminLogin = $adminUtils->checkAdminLogin();

if (!$adminUtils->isStaffLogin($adminLogin) && !$adminUtils->isSuperAdmin($adminLogin)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/subscription/admin.php", $gRedirectDelay);
  printMessage($str);
  return;
}

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(ELEARNING_SESSION_TEACHER_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER_SEARCH_PATTERN, $searchPattern);
}
$searchPattern = LibString::cleanString($searchPattern);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");

$help = $popupUtils->getHelpPopup($mlText[10], 300, 200);
$panelUtils->setHelp($help);

$label = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<b>$label</b> <input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";
$panelUtils->addLine($panelUtils->addCell($strSearch, "n"), '', '', '');

$strCommand = "<a href='$gElearningUrl/teacher/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[8]", "nb"), $panelUtils->addCell("$mlText[9]", "nb"), "", $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($elearningExerciseUtils->preferences);
$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $elearningTeachers = $elearningTeacherUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $elearningTeachers = $elearningTeacherUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $elearningTeacherUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($elearningTeachers as $elearningTeacher) {
  $elearningTeacherId = $elearningTeacher->getId();
  $firstname = $elearningTeacherUtils->getFirstname($elearningTeacherId);
  $lastname = $elearningTeacherUtils->getLastname($elearningTeacherId);
  $email = $elearningTeacherUtils->getEmail($elearningTeacherId);
  $email = $elearningTeacherUtils->renderEmail($email);

  $strCommand = " <a href='$gElearningUrl/teacher/delete.php?elearningTeacherId=$elearningTeacherId' teacher='$firstname $lastname' $gJSNoStatus>"
    .  "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($panelUtils->addCell("$firstname $lastname", "n"), $panelUtils->addCell($email, "n"), '', $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("elearning_teacher_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
