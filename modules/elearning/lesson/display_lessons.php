<?PHP

require_once("website.php");

$elearningExerciseUtils->checkUserLogin();

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
$elearningLevelId = LibEnv::getEnvHttpPOST("elearningLevelId");
$elearningCategoryId = LibEnv::getEnvHttpPOST("elearningCategoryId");
$elearningSubjectId = LibEnv::getEnvHttpPOST("elearningSubjectId");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE_SEARCH_PATTERN, $searchPattern);
}
if (!$elearningCourseId) {
  $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
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
  $elearningLevelId = '';
  $elearningCategoryId = '';
  $elearningSubjectId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_LEVEL, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CATEGORY, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SUBJECT, '');
} else if ($elearningCourseId > 0) {
  $elearningLevelId = '';
  $elearningCategoryId = '';
  $elearningSubjectId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_LEVEL, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CATEGORY, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SUBJECT, '');
}

$preferenceUtils->init($elearningExerciseUtils->preferences);
$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $elearningLessons = $elearningLessonUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($elearningCategoryId && $elearningLevelId && $elearningSubjectId) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndLevelIdAndSubjectId($elearningCategoryId, $elearningLevelId, $elearningSubjectId, $listIndex, $listStep);
} else if ($elearningCategoryId && $elearningLevelId) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndLevelId($elearningCategoryId, $elearningLevelId, $listIndex, $listStep);
} else if ($elearningCategoryId && $elearningSubjectId) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryIdAndSubjectId($elearningCategoryId, $elearningSubjectId, $listIndex, $listStep);
} else if ($elearningLevelId && $elearningSubjectId) {
  $elearningLessons = $elearningLessonUtils->selectByLevelIdAndSubjectId($elearningLevelId, $elearningSubjectId, $listIndex, $listStep);
} else if ($elearningCategoryId) {
  $elearningLessons = $elearningLessonUtils->selectByCategoryId($elearningCategoryId, $listIndex, $listStep);
} else if ($elearningLevelId) {
  $elearningLessons = $elearningLessonUtils->selectByLevelId($elearningLevelId, $listIndex, $listStep);
} else if ($elearningSubjectId) {
  $elearningLessons = $elearningLessonUtils->selectBySubjectId($elearningSubjectId, $listIndex, $listStep);
} else if ($elearningCourseId) {
  $elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($elearningCourseId, $listIndex, $listStep);
} else {
  if (!$preferenceUtils->getValue("ELEARNING_LIST_DEFAULT_EMPTY")) {
    $elearningLessons = $elearningLessonUtils->selectNonGarbage($listIndex, $listStep);
  } else {
    $elearningLessons = array();
  }
}

$listNbItems = $elearningExerciseUtils->countFoundRows();

$elearningCourses = $elearningCourseUtils->selectAll();
$elearningCourseList = Array('' => '');
foreach ($elearningCourses as $elearningCourse) {
  $wCourseId = $elearningCourse->getId();
  $wName = $elearningCourse->getName();
  $elearningCourseList[$wCourseId] = $wName;
}
$strSelectCourse = LibHtml::getSelectList("elearningCourseId", $elearningCourseList, $elearningCourseId);

$elearningLevels = $elearningLevelUtils->selectAll();
$elearningLevelList = Array('-1' => '');
foreach ($elearningLevels as $elearningLevel) {
  $wLevelId = $elearningLevel->getId();
  $wName = $elearningLevel->getName();
  $elearningLevelList[$wLevelId] = $wName;
}
$strSelectLevel = LibHtml::getSelectList("elearningLevelId", $elearningLevelList, $elearningLevelId);

$elearningCategories = $elearningCategoryUtils->selectAll();
$elearningCategoryList = Array('-1' => '');
foreach ($elearningCategories as $elearningCategory) {
  $wCatId = $elearningCategory->getId();
  $wName = $elearningCategory->getName();
  $elearningCategoryList[$wCatId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("elearningCategoryId", $elearningCategoryList, $elearningCategoryId);

$elearningSubjects = $elearningSubjectUtils->selectAll();
$elearningSubjectList = Array('-1' => '');
foreach ($elearningSubjects as $elearningSubject) {
  $wSubjectId = $elearningSubject->getId();
  $wName = $elearningSubject->getName();
  $elearningSubjectList[$wSubjectId] = $wName;
}
$strSelectSubject = LibHtml::getSelectList("elearningSubjectId", $elearningSubjectList, $elearningSubjectId);

$str = '';

$str .= "\n<div class='system'>";

$str .= "<div class='system_title'>$websiteText[0]</div>";

$str .= "<div class='system_comment'><a href='javascript:void(0);' onclick=\"toggleSearch(); return false;\" class='no_style_image_icon'>$websiteText[11]</a></div>";

$str .= "<div id='search' style='display:none;'>";

$str .= "<form action='$gElearningUrl/lesson/display_lessons.php' method='post'>";

$label = $userUtils->getTipPopup($websiteText[1], $websiteText[2], 300, 400);
$strSearch = "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' />"
  . "<input type='hidden' name='searchSubmitted' value='1'> ";

$str .= "<div class='system_label''>$label</div>"
  . "<div class='system_field'>$strSearch</div>";

$label = $userUtils->getTipPopup($websiteText[5], $websiteText[6], 300, 400);
$str .= "<div class='system_label''>$label</div>"
  . "<div class='system_field'>$strSelectCourse <input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /></div>";

$label = $userUtils->getTipPopup($websiteText[3], $websiteText[4], 300, 400);
$str .= "<div class='system_label''>$label</div>"
  . "<div class='system_field'>$strSelectLevel <input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /></div>";

$label = $userUtils->getTipPopup($websiteText[7], $websiteText[8], 300, 400);
$str .= "<div class='system_label''>$label</div>"
  . "<div class='system_field'>$strSelectCategory <input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /></div>";

$label = $userUtils->getTipPopup($websiteText[9], $websiteText[10], 300, 400);
$str .= "<div class='system_label''>$label</div>"
  . "<div class='system_field'>$strSelectSubject <input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /></div>";

$str .= "</form>";

$str .= "</div>";

$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $str .= "<div>$paginationLinks</div>";
}

if ($elearningCourseId > 0) {
  if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
    $name = $elearningCourse->getName();
    $str .= "<div><img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_COURSE . "' title='$websiteText[16]' style='vertical-align:middle;'> $name</div><br />";
  }
}

if ($elearningCourseId && isset($elearningCourseItems)) {

  foreach ($elearningCourseItems as $elearningCourseItem) {
    $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
    $elearningLessonId = $elearningCourseItem->getElearningLessonId();
    if ($elearningExerciseId) {
      if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
        $strName = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus title='$websiteText[13]'><img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' title='$websiteText[13]' style='vertical-align:middle;'> " . $elearningExercise->getName() . "</a>";
        $description = $elearningExercise->getDescription();
        $str .= "<div>$strName</div>";
      }
    } else if ($elearningLessonId) {
      if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
        $strName = "<a href='$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId' $gJSNoStatus title='$websiteText[12]'><img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_LESSON . "' title='$websiteText[12]' style='vertical-align:middle;'> " . $elearningLesson->getName() . "</a>";
        $description = $elearningLesson->getDescription();
        $str .= "<div>$strName</div>";
        $lessonExerciseLinks = $elearningLessonUtils->renderLessonExerciseDisplayLinks($elearningLessonId, $websiteText[15]);
        if ($lessonExerciseLinks) {
          $str .= "<div>&nbsp;&nbsp;&nbsp;&nbsp;$lessonExerciseLinks</div>";
        }
      }
    }

  }

} else if (isset($elearningLessons)) {

  foreach ($elearningLessons as $elearningLesson) {
    $elearningLessonId = $elearningLesson->getId();
    $strName = "<a href='$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId' $gJSNoStatus title='$websiteText[14]'><img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_LESSON . "' title='$websiteText[13]' style='vertical-align:middle;'> " . $elearningLesson->getName() . "</a>";
    $description = $elearningLesson->getDescription();
    $str .= "<div>$strName</div>";
    $lessonExerciseLinks = $elearningLessonUtils->renderLessonExerciseDisplayLinks($elearningLessonId, $websiteText[15]);
    if ($lessonExerciseLinks) {
      $str .= "<div>&nbsp;&nbsp;&nbsp;&nbsp;$lessonExerciseLinks</div>";
    }
  }

}

$str .= "</div>";

$str .= <<<HEREDOC
<script type="text/javascript">
function toggleSearch() {
  var id = 'search';
  $("#"+id).slideToggle('fast', function() {
    // Animation complete
  });
  void(0);
}
</script>
HEREDOC;

$gTemplate->setPageContent($str);

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
