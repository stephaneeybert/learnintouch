<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $introduction = LibEnv::getEnvHttpPOST("introduction");
  $secured = LibEnv::getEnvHttpPOST("secured");
  $publicAccess = LibEnv::getEnvHttpPOST("publicAccess");
  $releaseDate = LibEnv::getEnvHttpPOST("releaseDate");
  $elearningLessonModelId = LibEnv::getEnvHttpPOST("elearningLessonModelId");
  $elearningCategoryId = LibEnv::getEnvHttpPOST("elearningCategoryId");
  $elearningLevelId = LibEnv::getEnvHttpPOST("elearningLevelId");
  $elearningSubjectId = LibEnv::getEnvHttpPOST("elearningSubjectId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $secured = LibString::cleanString($secured);
  $publicAccess = LibString::cleanString($publicAccess);
  $releaseDate = LibString::cleanString($releaseDate);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // The name must not already exist
  if ($elearningLesson = $elearningLessonUtils->selectByName($name)) {
    if ($elearningLessonId != $elearningLesson->getId()) {
      array_push($warnings, $mlText[9]);
    }
  }

  if ($elearningLessonUtils->isLockedForLoggedInAdmin($elearningLessonId)) {
    array_push($warnings, $mlText[16]);
  }

  if ($elearningCourseId) {
    if ($elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
      array_push($warnings, $mlText[17]);
    }
  }

  // Validate the release
  if ($releaseDate && !$clockUtils->isLocalNumericDateValid($releaseDate)) {
    array_push($warnings, $mlText[21] . " " . $clockUtils->getDateNumericFormatTip());
  }

  if ($releaseDate) {
    $releaseDate = $clockUtils->localToSystemDate($releaseDate);
  } else {
    $releaseDate = $clockUtils->getSystemDate();
  }

  if (count($warnings) == 0) {

    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      // If the lesson has no model then reset all the model
      // headings of the paragraphs of the lesson
      if (!$elearningLessonModelId) {
        if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
          foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
            $elearningLessonParagraph->setElearningLessonHeadingId('');
            $elearningLessonParagraphUtils->update($elearningLessonParagraph);
          }
        }
      }

      $elearningLesson->setName($name);
      $elearningLesson->setDescription($description);
      $elearningLesson->setSecured($secured);
      $elearningLesson->setPublicAccess($publicAccess);
      $elearningLesson->setReleaseDate($releaseDate);
      $elearningLesson->setLessonModelId($elearningLessonModelId);
      $elearningLesson->setCategoryId($elearningCategoryId);
      $elearningLesson->setLevelId($elearningLevelId);
      $elearningLesson->setSubjectId($elearningSubjectId);
      $elearningLessonUtils->update($elearningLesson);
    } else {
      $elearningLesson = new ElearningLesson();
      $elearningLesson->setName($name);
      $elearningLesson->setDescription($description);
      $elearningLesson->setSecured($secured);
      $elearningLesson->setPublicAccess($publicAccess);
      $elearningLesson->setReleaseDate($releaseDate);
      $elearningLesson->setLessonModelId($elearningLessonModelId);
      $elearningLesson->setCategoryId($elearningCategoryId);
      $elearningLesson->setLevelId($elearningLevelId);
      $elearningLesson->setSubjectId($elearningSubjectId);
      $elearningLessonUtils->insert($elearningLesson);
      $elearningLessonId = $elearningLessonUtils->getLastInsertId();
    }

    if ($elearningCourseId) {
      if (!$elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId)) {
        $elearningCourseItem = new ElearningCourseItem();
        $elearningCourseItem->setElearningCourseId($elearningCourseId);
        $elearningCourseItem->setElearningLessonId($elearningLessonId);
        $listOrder = $elearningCourseItemUtils->getNextListOrder($elearningCourseId);
        $elearningCourseItem->setListOrder($listOrder);
        $elearningCourseItemUtils->insert($elearningCourseItem);
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/compose.php?elearningLessonId=$elearningLessonId");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

  $name = '';
  $description = '';
  $secured = '';
  $publicAccess = '';
  $releaseDate = '';
  $elearningLessonModelId = '';
  $elearningCategoryId = '';
  $elearningLevelId = '';
  $elearningSubjectId = '';
  if ($elearningLessonId) {
    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $name = $elearningLesson->getName();
      $description = $elearningLesson->getDescription();
      $secured = $elearningLesson->getSecured();
      $publicAccess = $elearningLesson->getPublicAccess();
      $releaseDate = $elearningLesson->getReleaseDate();
      $elearningLessonModelId = $elearningLesson->getLessonModelId();
      $elearningCategoryId = $elearningLesson->getCategoryId();
      $elearningLevelId = $elearningLesson->getLevelId();
      $elearningSubjectId = $elearningLesson->getSubjectId();
    }
  }

}

if (!$clockUtils->systemDateIsSet($releaseDate)) {
  $releaseDate = $clockUtils->getSystemDate();
}

$releaseDate = $clockUtils->systemToLocalNumericDate($releaseDate);

$elearningLessonModels = $elearningLessonModelUtils->selectAll();
$elearningLessonModelList = Array('' => '');
foreach ($elearningLessonModels as $elearningLessonModel) {
  $wId = $elearningLessonModel->getId();
  $wName = $elearningLessonModel->getName();
  $elearningLessonModelList[$wId] = $wName;
}
$strSelectLessonModel = LibHtml::getSelectList("elearningLessonModelId", $elearningLessonModelList, $elearningLessonModelId);

$elearningCategories = $elearningCategoryUtils->selectAll();
$elearningCategoryList = Array('' => '');
foreach ($elearningCategories as $elearningCategory) {
  $wCatId = $elearningCategory->getId();
  $wName = $elearningCategory->getName();
  $elearningCategoryList[$wCatId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("elearningCategoryId", $elearningCategoryList, $elearningCategoryId);

$elearningLevels = $elearningLevelUtils->selectAll();
$elearningLevelList = Array('' => '');
foreach ($elearningLevels as $elearningLevel) {
  $wLevelId = $elearningLevel->getId();
  $wName = $elearningLevel->getName();
  $elearningLevelList[$wLevelId] = $wName;
}
$strSelectLevel = LibHtml::getSelectList("elearningLevelId", $elearningLevelList, $elearningLevelId);

$elearningSubjects = $elearningSubjectUtils->selectAll();
$elearningSubjectList = Array('' => '');
foreach ($elearningSubjects as $elearningSubject) {
  $wId = $elearningSubject->getId();
  $wName = $elearningSubject->getName();
  $elearningSubjectList[$wId] = $wName;
}
$strSelectSubject = LibHtml::getSelectList("elearningSubjectId", $elearningSubjectList, $elearningSubjectId);

if ($publicAccess == '1') {
  $checkedPublicAccess = "CHECKED";
} else {
  $checkedPublicAccess = '';
}

if ($secured == '1') {
  $checkedSecured = "CHECKED";
} else {
  $checkedSecured = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[29], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<textarea name='description' cols='30' rows='5'>$description</textarea>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[18], $mlText[19], 300, 300);
$strJsSuggestCourse = $commonUtils->ajaxAutocomplete("$gElearningUrl/course/suggest.php", "courseName", "elearningCourseId");
$panelUtils->addContent($strJsSuggestCourse);
$panelUtils->addHiddenField('elearningCourseId', '');
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' id='courseName' name='' value='' />", "n"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[26], $mlText[27], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='secured' $checkedSecured value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[7], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='publicAccess' $checkedPublicAccess value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='releaseDate' id='releaseDate' value='$releaseDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[13], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectLessonModel);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[15], "nbr"), $strSelectCategory);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[12], "nbr"), $strSelectSubject);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $strSelectLevel);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#releaseDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#releaseDate").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
