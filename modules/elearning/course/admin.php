<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$elearningMatterId = LibEnv::getEnvHttpPOST("elearningMatterId");
$userId = LibEnv::getEnvHttpPOST("userId");
$userName = LibEnv::getEnvHttpPOST("userName");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(ELEARNING_SESSION_COURSE_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE_SEARCH_PATTERN, $searchPattern);
}

if (!$elearningMatterId) {
  $elearningMatterId = LibSession::getSessionValue(ELEARNING_SESSION_MATTER);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_MATTER, $elearningMatterId);
}

$searchPattern = LibString::cleanString($searchPattern);

if ($searchPattern) {
  $elearningMatterId = '';
  $userId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_MATTER, '');
} else if ($elearningMatterId > 0) {
  $userId = '';
}

$elearningMatters = $elearningMatterUtils->selectAll();
$elearningMatterList = Array('-1' => '');
foreach ($elearningMatters as $elearningMatter) {
  $wMatterId = $elearningMatter->getId();
  $wName = $elearningMatter->getName();
  $elearningMatterList[$wMatterId] = $wName;
}
$strSelectMatter = LibHtml::getSelectList("elearningMatterId", $elearningMatterList, $elearningMatterId, true);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[29], 300, 300);
$panelUtils->setHelp($help);

$strCommand = ''
  . " <a href='$gElearningUrl/lesson/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageLesson' title='$mlText[8]'></a>"
  . " <a href='$gElearningUrl/exercise/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$mlText[9]'></a>"
  . " <a href='$gElearningUrl/session/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSession' title='$mlText[21]'></a>"
  . " <a href='$gElearningUrl/matter/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[34]'></a>";

$label = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> " . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strSearch, "n"), '', '', $panelUtils->addCell($strCommand, "nr"));

$strMatter = "<form action='$PHP_SELF' method='post'>"
  . "$mlText[33] " . $strSelectMatter
  . "</form>";

if ($websiteUtils->isCurrentWebsiteOption('OPTION_ELEARNING_STORE')) {
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $userName = $firstname . ' ' . $lastname;
  } else {
    $userName = '';
  }
  $label = $popupUtils->getTipPopup($mlText[14], $mlText[15], 300, 200);
  $strJsSuggest = $commonUtils->ajaxAutocomplete("$gUserUrl/suggestUsers.php", "userName", "userId");
  $panelUtils->addContent($strJsSuggest);
  $strUser = $strJsSuggest 
    . "<form action='$PHP_SELF' method='post'>"
    . "$label <input type='text' name='userName' id='userName' value='$userName' size='20' /> "
    . $panelUtils->getTinyOk()
    . "<input type='hidden' name='userId' id='userId' value='$userId'>"
    . "</form>";
} else {
  $strUser = '';
}
$panelUtils->addLine($panelUtils->addCell($strMatter, "nb"), $panelUtils->addCell($strUser, "nb"), '', '', '');

$strCommand = "<a href='$gElearningUrl/course/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
  . " <a href='$gElearningUrl/import/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[53]'></a>";

$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[17], "nb"), $panelUtils->addCell($mlText[5], "nbc"), $panelUtils->addCell($mlText[16], "nbc"), $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($elearningExerciseUtils->preferences);
$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $elearningCourses = $elearningCourseUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($elearningMatterId > 0) {
  $elearningCourses = $elearningCourseUtils->selectByMatterId($elearningMatterId, $listIndex, $listStep);
} else if ($userId > 0) {
  $elearningCourses = $elearningCourseUtils->selectByUserId($userId, $listIndex, $listStep);
} else {
  $elearningCourses = $elearningCourseUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $elearningCourseUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($elearningCourses as $elearningCourse) {
  $elearningCourseId = $elearningCourse->getId();
  $name = $elearningCourse->getName();
  $description = $elearningCourse->getDescription();
  $matterId = $elearningCourse->getMatterId();
  $importable = $elearningCourse->getImportable();
  $locked = $elearningCourse->getLocked();
  $autoSubscription = $elearningCourse->getAutoSubscription();

  if ($importable) {
    $strImportable = "<img border='0' src='$gCommonImagesUrl/$gImageCurrent' title='$mlText[6]'>";
  } else {
    $strImportable = '';
  }

  if ($autoSubscription) {
    $strAutoSubscription = "<img border='0' src='$gCommonImagesUrl/$gImageCurrent' title='$mlText[18]'>";
  } else {
    $strAutoSubscription = '';
  }

  $strCommand = '';

  $strCommand .= " <a href='$gElearningUrl/subscription/course.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$mlText[19]'></a>";

  $strCommand .= " <a href='$gElearningUrl/lesson/admin.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageLesson' title='$mlText[12]'></a>"
    . " <a href='$gElearningUrl/exercise/admin.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$mlText[13]'></a>"
    . " <a href='$gElearningUrl/course/send.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[28]'></a>";;

  if (!$elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
    $strCommand .= " <a href='$gElearningUrl/course/edit.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
      . " <a href='$gElearningUrl/course/info/admin.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageInfo' title='$mlText[20]'></a>";
  }

  $adminLogin = $adminUtils->checkAdminLogin();
  if ($adminUtils->isSuperAdmin($adminLogin)) {
    if ($locked) {
      $strCommand .= " <a href='$gElearningUrl/course/lock.php?elearningCourseId=$elearningCourseId&locked=0' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageUnlock' title='$mlText[11]'></a>";
    } else {
      $strCommand .= " <a href='$gElearningUrl/course/lock.php?elearningCourseId=$elearningCourseId&locked=1' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageLock' title='$mlText[10]'></a>";
    }
  }

  $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gElearningUrl/course/image.php?elearningCourseId=$elearningCourseId", 600, 600);

  if (!$elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
    $strCommand .= " <a href='$gElearningUrl/course/duplicate.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[22]'></a>";
    $strCommand .= " <a href='$gElearningUrl/course/delete.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";
  } else {
    $strCommand .= " <img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''>";
  }

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strImportable, "c"), $panelUtils->addCell($strAutoSubscription, "c"), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
