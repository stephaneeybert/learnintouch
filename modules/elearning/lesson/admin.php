<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
$publicAccessStatus = LibEnv::getEnvHttpPOST("publicAccessStatus");
$duration = LibEnv::getEnvHttpPOST("duration");
$elearningLevelId = LibEnv::getEnvHttpPOST("elearningLevelId");
$elearningCategoryId = LibEnv::getEnvHttpPOST("elearningCategoryId");
$elearningSubjectId = LibEnv::getEnvHttpPOST("elearningSubjectId");

if (!$elearningCourseId) {
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
}

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(ELEARNING_SESSION_LESSON_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_LESSON_SEARCH_PATTERN, $searchPattern);
}

if (!$elearningCourseId) {
  $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
}

if (!$publicAccessStatus) {
  $publicAccessStatus = LibSession::getSessionValue(ELEARNING_SESSION_PUBLIC_ACCESS);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_PUBLIC_ACCESS, $publicAccessStatus);
}

if (!$duration) {
  $duration = LibSession::getSessionValue(ELEARNING_SESSION_DURATION);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, $duration);
}

if (!$elearningLevelId) {
  $elearningLevelId = LibSession::getSessionValue(ELEARNING_SESSION_LEVEL);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_LEVEL, $elearningLevelId);
}

if (!$elearningCategoryId) {
  $elearningCategoryId = LibSession::getSessionValue(ELEARNING_SESSION_CATEGORY);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_CATEGORY, $elearningCategoryId);
}

if (!$elearningSubjectId) {
  $elearningSubjectId = LibSession::getSessionValue(ELEARNING_SESSION_SUBJECT);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SUBJECT, $elearningSubjectId);
}

$searchPattern = LibString::cleanString($searchPattern);

if ($searchPattern) {
  $elearningCourseId = '';
  $publicAccessStatus = '';
  $duration = '';
  $elearningLevelId = '';
  $elearningCategoryId = '';
  $elearningSubjectId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_PUBLIC_ACCESS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_LEVEL, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CATEGORY, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SUBJECT, '');
} else if ($elearningCourseId > 0) {
  $publicAccessStatus = '';
  $duration = '';
  $elearningLevelId = '';
  $elearningCategoryId = '';
  $elearningSubjectId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_PUBLIC_ACCESS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_LEVEL, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CATEGORY, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SUBJECT, '');
}

$publicStatusList = array(
  '-1' => '',
  ELEARNING_PUBLIC => $mlText[50],
  ELEARNING_PROTECTED => $mlText[51],
);
$strSelectPublicAccess = LibHtml::getSelectList("publicAccessStatus", $publicStatusList, $publicAccessStatus, true);

$durationList = array(
  '-2' => '',
  '-1' => $mlText[56],
  7 => $mlText[43],
  30 => $mlText[44],
  90 => $mlText[45],
  180 => $mlText[46],
  365 => $mlText[47],
);
$strSelectRelease = LibHtml::getSelectList("duration", $durationList, $duration, true);

$elearningLevels = $elearningLevelUtils->selectAll();
$elearningLevelList = Array('-1' => '');
foreach ($elearningLevels as $elearningLevel) {
  $wLevelId = $elearningLevel->getId();
  $wName = $elearningLevel->getName();
  $elearningLevelList[$wLevelId] = $wName;
}
$strSelectLevel = LibHtml::getSelectList("elearningLevelId", $elearningLevelList, $elearningLevelId, true);

$elearningCategories = $elearningCategoryUtils->selectAll();
$elearningCategoryList = Array('-1' => '');
foreach ($elearningCategories as $elearningCategory) {
  $wCategoryId = $elearningCategory->getId();
  $wName = $elearningCategory->getName();
  $elearningCategoryList[$wCategoryId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("elearningCategoryId", $elearningCategoryList, $elearningCategoryId, true);

$elearningSubjects = $elearningSubjectUtils->selectAll();
$elearningSubjectList = Array('-1' => '');
foreach ($elearningSubjects as $elearningSubject) {
  $wSubjectId = $elearningSubject->getId();
  $wName = $elearningSubject->getName();
  $elearningSubjectList[$wSubjectId] = $wName;
}
$strSelectSubject = LibHtml::getSelectList("elearningSubjectId", $elearningSubjectList, $elearningSubjectId, true);

$courseName = '';
if ($course = $elearningCourseUtils->selectById($elearningCourseId)) {
  $courseName = $course->getName();
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[29], 300, 300);
$panelUtils->setHelp($help);

$strCommand = ''
  . " <a href='$gElearningUrl/lesson/model/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageModel' title='$mlText[6]'></a>"
  . " <a href='$gElearningUrl/exercise/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$mlText[52]'></a>"
  . " <a href='$gElearningUrl/course/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$mlText[22]'></a>"
  . " <a href='$gElearningUrl/category/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[23]'></a>"
  . " <a href='$gElearningUrl/subject/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSubject' title='$mlText[39]'></a>"
  . " <a href='$gElearningUrl/level/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageLevel' title='$mlText[14]'></a>";

$label = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strSearch, "n"), '', $panelUtils->addCell($strCommand, "nr"));

$panelUtils->openForm($PHP_SELF);
$courseLabel = $popupUtils->getTipPopup($mlText[33], $mlText[63], 300, 200);
$statusLabel = $popupUtils->getTipPopup($mlText[60], $mlText[64], 300, 200);
$sinceLabel = $popupUtils->getTipPopup($mlText[48], $mlText[65], 300, 200);
$panelUtils->addLine($panelUtils->addCell($courseLabel, "nb"), $panelUtils->addCell($statusLabel, "nb"), $panelUtils->addCell($sinceLabel, "nb"), '');
$strJsSuggestCourse = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/course/suggest.php", "courseName", "elearningCourseId");
$panelUtils->addContent($strJsSuggestCourse);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->addLine($panelUtils->addCell("<input type='text' id='courseName' name='$courseName' value='$courseName' /> " . $panelUtils->getTinyOk(), "n"), $panelUtils->addCell($strSelectPublicAccess, "n"), $panelUtils->addCell($strSelectRelease, "n"), '');
$levelLabel = $popupUtils->getTipPopup($mlText[13], $mlText[59], 300, 200);
$categoryLabel = $popupUtils->getTipPopup($mlText[9], $mlText[61], 300, 200);
$subjectLabel = $popupUtils->getTipPopup($mlText[55], $mlText[62], 300, 200);
$panelUtils->addLine($panelUtils->addCell($levelLabel, "nb"), $panelUtils->addCell($categoryLabel, "nb"), $panelUtils->addCell($subjectLabel, "nb"), '');
$panelUtils->addLine($panelUtils->addCell($strSelectLevel, "n"), $panelUtils->addCell($strSelectCategory, "n"), $panelUtils->addCell($strSelectSubject, "n"), '');
$panelUtils->closeForm();
$panelUtils->addLine();

$sinceDate = '';
$systemDate = $clockUtils->getSystemDate();
if ($duration == -1 || $duration > 0) {
  $sinceDate = $clockUtils->incrementDays($systemDate, -1 * $duration);
}

$strCommand = "<a href='$gElearningUrl/lesson/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$lastImportedElearningCourseId = $elearningImportUtils->retrieveLastImportedCourseId();

$strCommand .= " <a href='$gElearningUrl/import/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[53]'></a>";
if ($lastImportedElearningCourseId) {
  $strCommand .= " <a href='$gElearningUrl/course/delete.php?elearningCourseId=$lastImportedElearningCourseId&deleteContent=1' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[31]'></a>";
}
$strCommand .= " <a href='$gElearningUrl/lesson/garbage.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageGarbage' title='$mlText[34]'></a>";

$exercisesLabel = $popupUtils->getTipPopup($mlText[18], $mlText[20], 300, 200);
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[17]", "nb"), $panelUtils->addCell($exercisesLabel, "nb"), $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($elearningExerciseUtils->preferences);
$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $elearningLessons = $elearningLessonUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($elearningCourseId > 0) {
  $elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($elearningCourseId, $listIndex, $listStep);
} else if ($elearningCategoryId > 0 && $elearningLevelId > 0 && $elearningSubjectId > 0 && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndLevelIdAndSubjectIdAndReleaseDate($elearningCategoryId, $elearningLevelId, $elearningSubjectId, $sinceDate, $systemDate, $listIndex, $listStep);
} else if ($elearningCategoryId > 0 && $elearningLevelId > 0 && $elearningSubjectId > 0) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndLevelIdAndSubjectId($elearningCategoryId, $elearningLevelId, $elearningSubjectId, $listIndex, $listStep);
} else if ($elearningCategoryId > 0 && $elearningLevelId > 0 && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndLevelIdAndReleaseDate($elearningCategoryId, $elearningLevelId, $sinceDate, $systemDate, $listIndex, $listStep);
} else if ($elearningCategoryId > 0 && $elearningLevelId > 0) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndLevelId($elearningCategoryId, $elearningLevelId, $listIndex, $listStep);
} else if ($elearningCategoryId > 0 && $elearningSubjectId > 0 && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndSubjectIdAndReleaseDate($elearningCategoryId, $elearningSubjectId, $sinceDate, $systemDate, $listIndex, $listStep);
} else if ($elearningCategoryId > 0 && $elearningSubjectId > 0) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndSubjectId($elearningCategoryId, $elearningSubjectId, $listIndex, $listStep);
} else if ($elearningLevelId > 0 && $elearningSubjectId > 0 && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectByLevelIdAndSubjectIdAndReleaseDate($elearningLevelId, $elearningSubjectId, $sinceDate, $systemDate, $listIndex, $listStep);
} else if ($elearningLevelId > 0 && $elearningSubjectId > 0) {
  $elearningLessons = $elearningLessonUtils->selectByLevelIdAndSubjectId($elearningLevelId, $elearningSubjectId, $listIndex, $listStep);
} else if ($elearningCategoryId > 0 && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndReleaseDate($elearningCategoryId, $sinceDate, $systemDate, $listIndex, $listStep);
} else if ($elearningCategoryId > 0) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryId($elearningCategoryId, $listIndex, $listStep);
} else if ($elearningLevelId > 0 && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectByLevelIdAndReleaseDate($elearningLevelId, $sinceDate, $systemDate, $listIndex, $listStep);
} else if ($elearningLevelId > 0) {
  $elearningLessons = $elearningLessonUtils->selectByLevelId($elearningLevelId, $listIndex, $listStep);
} else if ($elearningSubjectId > 0 && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectBySubjectIdAndReleaseDate($elearningSubjectId, $sinceDate, $systemDate, $listIndex, $listStep);
} else if ($elearningSubjectId > 0) {
  $elearningLessons = $elearningLessonUtils->selectBySubjectId($elearningSubjectId, $listIndex, $listStep);
} else if ($publicAccessStatus == ELEARNING_PUBLIC && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectPublicAccessAndReleaseDate($sinceDate, $systemDate, $listIndex, $listStep);
} else if ($publicAccessStatus == ELEARNING_PROTECTED && $sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectProtectedAndReleaseDate($sinceDate, $systemDate, $listIndex, $listStep);
} else if ($publicAccessStatus == ELEARNING_PUBLIC) {
  $elearningLessons = $elearningLessonUtils->selectPublicAccess($listIndex, $listStep);
} else if ($publicAccessStatus == ELEARNING_PROTECTED) {
  $elearningLessons = $elearningLessonUtils->selectProtected($listIndex, $listStep);
} else if ($sinceDate) {
  $elearningLessons = $elearningLessonUtils->selectByReleaseDate($sinceDate, $systemDate, $listIndex, $listStep);
} else {
  $defaultListEmpty = $preferenceUtils->getValue("ELEARNING_LIST_DEFAULT_EMPTY");
  if (!$defaultListEmpty) {
    $elearningLessons = $elearningLessonUtils->selectNonGarbage($listIndex, $listStep);
  } else {
    $elearningLessons = array();
  }
}

$listNbItems = $elearningLessonUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

if (isset($elearningLessons)) {
  $panelUtils->openList();
  foreach ($elearningLessons as $elearningLesson) {
    $elearningLessonId = $elearningLesson->getId();
    $name = $elearningLesson->getName();
    $description = $elearningLesson->getDescription();
    $locked = $elearningLesson->getLocked();

    $lessonExerciseLinks = $elearningLessonUtils->renderLessonExerciseComposeLinks($elearningLessonId, $mlText[16]);

    $strPageUrl = "$mlText[36] <br><br>$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId<br><br>$mlText[37]";

    $strCommand = '';

    $strCommand .= " <a href='$gElearningUrl/lesson/send_by_admin.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[28]'></a>";

    if (!$elearningLessonUtils->isLockedForLoggedInAdmin($elearningLessonId)) {
      $strCommand .= " <a href='$gElearningUrl/lesson/edit.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
        . " <a href='$gElearningUrl/lesson/compose.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[42]'></a>";
    }

    if (!$elearningLessonUtils->isLockedForLoggedInAdmin($elearningLessonId)) {
      $strCommand .= " <a href='$gElearningUrl/course/addLesson.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageAddTo' title='$mlText[8]'></a>";
    }

    $adminLogin = $adminUtils->checkAdminLogin();
    if ($adminUtils->isSuperAdmin($adminLogin)) {
      if ($locked) {
        $strCommand .= " <a href='$gElearningUrl/lesson/lock.php?elearningLessonId=$elearningLessonId&locked=0' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageUnlock' title='$mlText[25]'></a>";
      } else {
        $strCommand .= " <a href='$gElearningUrl/lesson/lock.php?elearningLessonId=$elearningLessonId&locked=1' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageLock' title='$mlText[24]'></a>";
      }
    }

    $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[26]'>", "$gElearningUrl/lesson/preview.php?elearningLessonId=$elearningLessonId", 600, 600)
      . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[40]'>", "$gElearningUrl/lesson/print_lesson.php?elearningLessonId=$elearningLessonId", 600, 600)
      . " <a href='$gElearningUrl/lesson/pdf.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$mlText[40]'></a>"
      . ' ' . $popupUtils->getPopup("<img border='0' src='$gCommonImagesUrl/$gImageWeb' title='$mlText[35]'> ", $strPageUrl, 800, 160)
      . " <a href='$gElearningUrl/lesson/duplicate.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[32]'></a>";

    if (!$elearningLessonUtils->isLockedForLoggedInAdmin($elearningLessonId)) {
      $strCommand .= " <a href='$gElearningUrl/lesson/delete.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";
    } else {
      $strCommand .= " <img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''>";
    }

    $panelUtils->addLine($name, $description, $lessonExerciseLinks, $panelUtils->addCell($strCommand, "nr"));
  }
  $panelUtils->closeList();
}

if ($elearningCourseId > 0 && isset($elearningCourseItems)) {
  $panelUtils->openList();
  foreach ($elearningCourseItems as $elearningCourseItem) {
    $elearningCourseItemId = $elearningCourseItem->getId();
    $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
    $elearningLessonId = $elearningCourseItem->getElearningLessonId();
    if ($elearningExerciseId) {
      if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
        $description = $elearningExercise->getDescription();

        $strSwap = "<a href='$gElearningUrl/course/swapup.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId&redirectToExercise=1' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a> <a href='$gElearningUrl/course/swapdown.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId&redirectToExercise=1' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

        $strCommand = " <a href='$gElearningUrl/assignment/add.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageAssignment' title='$mlText[38]'></a>"
          . " <a href='$gElearningUrl/exercise/send_by_admin.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[28]'></a>";
        $strCommand .= " <a href='$gElearningUrl/exercise/edit.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[5]'></a>"
          . " <a href='$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[12]'></a>"
          . " <a href='$gElearningUrl/course/removeExercise.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageRemoveFrom' title='$mlText[15]'></a>"
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[27]'>", "$gElearningUrl/exercise/preview.php?elearningExerciseId=$elearningExerciseId", 600, 600)
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[41]'>", "$gElearningUrl/exercise/print_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600)
          . " <a href='$gElearningUrl/exercise/pdf.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$mlText[41]'></a>";

        $strName = "<span class='drag_and_drop' elearningCourseItemId='$elearningCourseItemId'>" . $elearningExerciseUtils->renderExerciseComposeLink($elearningExerciseId, $mlText[16]) . "</span>";

        $panelUtils->addLine($strSwap . ' ' . $strName, $description, '', $panelUtils->addCell($strCommand, "nr"));
      }
    } else if ($elearningLessonId) {
      if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
        $description = $elearningLesson->getDescription();

        $lessonExerciseLinks = $elearningLessonUtils->renderLessonExerciseComposeLinks($elearningLessonId, $mlText[16]);

        $strSwap = "<a href='$gElearningUrl/course/swapup.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId&redirectToExercise=0' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a> <a href='$gElearningUrl/course/swapdown.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId&redirectToExercise=1' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

        $strCommand = " <a href='$gElearningUrl/lesson/send_by_admin.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[28]'></a>";
        $strCommand .= " <a href='$gElearningUrl/lesson/edit.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
          . " <a href='$gElearningUrl/lesson/compose.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[42]'></a>"
          . " <a href='$gElearningUrl/course/removeLesson.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageRemoveFrom' title='$mlText[15]'></a>"
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[26]'>", "$gElearningUrl/lesson/preview.php?elearningLessonId=$elearningLessonId", 600, 600)
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[40]'>", "$gElearningUrl/lesson/print_lesson.php?elearningLessonId=$elearningLessonId", 600, 600)
          . " <a href='$gElearningUrl/lesson/pdf.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$mlText[40]'></a>"
          . " <a href='$gElearningUrl/lesson/duplicate.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[32]'></a>";

        $strName = "<span class='drag_and_drop' elearningCourseItemId='$elearningCourseItemId'>" . $elearningLessonUtils->renderLessonComposeLink($elearningLessonId, $mlText[21]) . "</span>";

        $panelUtils->addLine($strSwap . ' ' . $strName, $description, $lessonExerciseLinks, $panelUtils->addCell($strCommand, "nr"));
      }
    }
  }
  $panelUtils->closeList();
}

$strRememberScroll = LibJavaScript::rememberScroll("elearning_lesson_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$strListOrderDragAndDrop = <<<HEREDOC
<style type="text/css">
.drag_and_drop {
  cursor:pointer;
}
.droppable-hover {
  outline:2px solid #ABABAB;
}
#droppableTooltip {
  position:absolute;
  z-index:9999;
  background-color:#fff;
  font-weight:normal;
  border:1px solid #ABABAB;
  padding:4px;
}
</style>

<script type="text/javascript">

$(document).ready(function() {

  $(".drag_and_drop").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".drag_and_drop").droppable({
    accept: '.drag_and_drop', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      $(this).append('<div id="droppableTooltip">$mlText[66]</div>');
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
      moveAfter(ui.draggable.attr("elearningCourseItemId"), $(this).attr("elearningCourseItemId"));
    }
  });

  function moveAfter(elearningCourseItemId, targetId) {
    var url = '$gElearningUrl/course/move_after.php?elearningCourseItemId='+elearningCourseItemId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function renderPage(responseText) {
    var response = eval('(' + responseText + ')');
    var moved = response.moved;
    if (moved) {
      window.location = window.location.href;
    }
  }

});

</script>
HEREDOC;
$panelUtils->addContent($strListOrderDragAndDrop);

$str = $panelUtils->render();

printAdminPage($str);

?>
