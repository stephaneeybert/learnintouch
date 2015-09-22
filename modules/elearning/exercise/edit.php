<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $releaseDate = LibEnv::getEnvHttpPOST("releaseDate");
  $publicAccess = LibEnv::getEnvHttpPOST("publicAccess");
  $secured = LibEnv::getEnvHttpPOST("secured");
  $hideIntroduction = LibEnv::getEnvHttpPOST("hideIntroduction");
  $skipExerciseIntroduction = LibEnv::getEnvHttpPOST("skipExerciseIntroduction");
  $socialConnect = LibEnv::getEnvHttpPOST("socialConnect");
  $hideSolutions = LibEnv::getEnvHttpPOST("hideSolutions");
  $hideProgressionBar = LibEnv::getEnvHttpPOST("hideProgressionBar");
  $hidePageTabs = LibEnv::getEnvHttpPOST("hidePageTabs");
  $disableNextPageTabs = LibEnv::getEnvHttpPOST("disableNextPageTabs");
  $numberPageTabs = LibEnv::getEnvHttpPOST("numberPageTabs");
  $hideKeyboard = LibEnv::getEnvHttpPOST("hideKeyboard");
  $contactPage = LibEnv::getEnvHttpPOST("contactPage");
  $maxDuration = LibEnv::getEnvHttpPOST("maxDuration");
  $elearningCategoryId = LibEnv::getEnvHttpPOST("elearningCategoryId");
  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");
  $elearningLevelId = LibEnv::getEnvHttpPOST("elearningLevelId");
  $elearningSubjectId = LibEnv::getEnvHttpPOST("elearningSubjectId");
  $scoringId = LibEnv::getEnvHttpPOST("scoringId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);
  $releaseDate = LibString::cleanString($releaseDate);
  $publicAccess = LibString::cleanString($publicAccess);
  $secured = LibString::cleanString($secured);
  $hideIntroduction = LibString::cleanString($hideIntroduction);
  $skipExerciseIntroduction = LibString::cleanString($skipExerciseIntroduction);
  $socialConnect = LibString::cleanString($socialConnect);
  $hideSolutions = LibString::cleanString($hideSolutions);
  $hideProgressionBar = LibString::cleanString($hideProgressionBar);
  $hidePageTabs = LibString::cleanString($hidePageTabs);
  $disableNextPageTabs = LibString::cleanString($disableNextPageTabs);
  $numberPageTabs = LibString::cleanString($numberPageTabs);
  $hideKeyboard = LibString::cleanString($hideKeyboard);
  $contactPage = LibString::cleanString($contactPage);
  $maxDuration = LibString::cleanString($maxDuration);
  $webpageId = LibString::cleanString($webpageId);
  $webpageName = LibString::cleanString($webpageName);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // The name must not already exist
  if ($elearningExercise = $elearningExerciseUtils->selectByName($name)) {
    if ($elearningExerciseId != $elearningExercise->getId()) {
      array_push($warnings, $mlText[9]);
    }
  }

  // Validate the maximum duration if any
  if ($maxDuration && !is_numeric(LibString::stripSpaces($maxDuration))) {
    array_push($warnings, $mlText[16]);
  }

  // Validate the release
  if ($releaseDate && !$clockUtils->isLocalNumericDateValid($releaseDate)) {
    array_push($warnings, $mlText[21] . " " . $clockUtils->getDateNumericFormatTip());
  }

  if ($elearningExerciseUtils->isLockedForLoggedInAdmin($elearningExerciseId)) {
    array_push($warnings, $mlText[1]);
  }

  if ($elearningCourseId) {
    if ($elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
      array_push($warnings, $mlText[11]);
    }
  }

  if ($releaseDate) {
    $releaseDate = $clockUtils->localToSystemDate($releaseDate);
  } else {
    $releaseDate = $clockUtils->getSystemDate();
  }

  // Clear the page if necessary
  if (!$webpageName) {
    $webpageId = '';
  }

  if (count($warnings) == 0) {

    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $elearningExercise->setName($name);
      $elearningExercise->setDescription($description);
      $elearningExercise->setReleaseDate($releaseDate);
      $elearningExercise->setPublicAccess($publicAccess);
      $elearningExercise->setSecured($secured);
      $elearningExercise->setHideIntroduction($hideIntroduction);
      $elearningExercise->setSkipExerciseIntroduction($skipExerciseIntroduction);
      $elearningExercise->setSocialConnect($socialConnect);
      $elearningExercise->setHideSolutions($hideSolutions);
      $elearningExercise->setHideProgressionBar($hideProgressionBar);
      $elearningExercise->setHidePageTabs($hidePageTabs);
      $elearningExercise->setDisableNextPageTabs($disableNextPageTabs);
      $elearningExercise->setNumberPageTabs($numberPageTabs);
      $elearningExercise->setHideKeyboard($hideKeyboard);
      $elearningExercise->setContactPage($contactPage);
      $elearningExercise->setMaxDuration($maxDuration);
      $elearningExercise->setCategoryId($elearningCategoryId);
      $elearningExercise->setWebpageId($webpageId);
      $elearningExercise->setLevelId($elearningLevelId);
      $elearningExercise->setSubjectId($elearningSubjectId);
      $elearningExercise->setScoringId($scoringId);
      $elearningExerciseUtils->update($elearningExercise);
    } else {
      $elearningExercise = new ElearningExercise();
      $elearningExercise->setName($name);
      $elearningExercise->setDescription($description);
      $elearningExercise->setReleaseDate($releaseDate);
      $elearningExercise->setPublicAccess($publicAccess);
      $elearningExercise->setSecured($secured);
      $elearningExercise->setHideIntroduction($hideIntroduction);
      $elearningExercise->setSkipExerciseIntroduction($skipExerciseIntroduction);
      $elearningExercise->setSocialConnect($socialConnect);
      $elearningExercise->setHideSolutions($hideSolutions);
      $elearningExercise->setHideProgressionBar($hideProgressionBar);
      $elearningExercise->setHidePageTabs($hidePageTabs);
      $elearningExercise->setDisableNextPageTabs($disableNextPageTabs);
      $elearningExercise->setNumberPageTabs($numberPageTabs);
      $elearningExercise->setHideKeyboard($hideKeyboard);
      $elearningExercise->setContactPage($contactPage);
      $elearningExercise->setMaxDuration($maxDuration);
      $elearningExercise->setCategoryId($elearningCategoryId);
      $elearningExercise->setWebpageId($webpageId);
      $elearningExercise->setLevelId($elearningLevelId);
      $elearningExercise->setSubjectId($elearningSubjectId);
      $elearningExercise->setScoringId($scoringId);
      $elearningExerciseUtils->insert($elearningExercise);
      $elearningExerciseId = $elearningExerciseUtils->getLastInsertId();
    }

    if ($elearningCourseId) {
      if (!$elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
        $elearningCourseItem = new ElearningCourseItem();
        $elearningCourseItem->setElearningCourseId($elearningCourseId);
        $elearningCourseItem->setElearningExerciseId($elearningExerciseId);
        $listOrder = $elearningCourseItemUtils->getNextListOrder($elearningCourseId);
        $elearningCourseItem->setListOrder($listOrder);
        $elearningCourseItemUtils->insert($elearningCourseItem);
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $name = '';
  $description = '';
  $releaseDate = '';
  $publicAccess = '';
  $secured = '';
  $hideIntroduction = '';
  $skipExerciseIntroduction = '';
  $socialConnect = '';
  $hideSolutions = '';
  $hideProgressionBar = '';
  $hidePageTabs = '';
  $disableNextPageTabs = '';
  $numberPageTabs = '';
  $hideKeyboard = '';
  $contactPage = '';
  $maxDuration = '';
  $webpageId = '';
  $elearningCategoryId = '';
  $elearningLevelId = '';
  $elearningSubjectId = '';
  $scoringId = '';
  if ($elearningExerciseId) {
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $name = $elearningExercise->getName();
      $description = $elearningExercise->getDescription();
      $releaseDate = $elearningExercise->getReleaseDate();
      $publicAccess = $elearningExercise->getPublicAccess();
      $secured = $elearningExercise->getSecured();
      $hideIntroduction = $elearningExercise->getHideIntroduction();
      $skipExerciseIntroduction = $elearningExercise->getSkipExerciseIntroduction();
      $socialConnect = $elearningExercise->getSocialConnect();
      $hideSolutions = $elearningExercise->getHideSolutions();
      $hideProgressionBar = $elearningExercise->getHideProgressionBar();
      $hidePageTabs = $elearningExercise->getHidePageTabs();
      $disableNextPageTabs = $elearningExercise->getDisableNextPageTabs();
      $numberPageTabs = $elearningExercise->getNumberPageTabs();
      $hideKeyboard = $elearningExercise->getHideKeyboard();
      $contactPage = $elearningExercise->getContactPage();
      $maxDuration = $elearningExercise->getMaxDuration();
      $webpageId = $elearningExercise->getWebpageId();
      $elearningCategoryId = $elearningExercise->getCategoryId();
      $elearningLevelId = $elearningExercise->getLevelId();
      $elearningSubjectId = $elearningExercise->getSubjectId();
      $scoringId = $elearningExercise->getScoringId();
    }
  }

  $webpageName = $templateUtils->getPageName($webpageId);
}

if (!$clockUtils->systemDateIsSet($releaseDate)) {
  $releaseDate = $clockUtils->getSystemDate();
}

$releaseDate = $clockUtils->systemToLocalNumericDate($releaseDate);

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

$elearningScorings = $elearningScoringUtils->selectAll();
$elearningScoringList = Array('' => '');
foreach ($elearningScorings as $elearningScoring) {
  $wId = $elearningScoring->getId();
  $wName = $elearningScoring->getName();
  $elearningScoringList[$wId] = $wName;
}
$strSelectScoring = LibHtml::getSelectList("scoringId", $elearningScoringList, $scoringId);

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

if ($hideIntroduction == '1') {
  $checkedHideIntroduction = "CHECKED";
} else {
  $checkedHideIntroduction = '';
}

if ($skipExerciseIntroduction == '1') {
  $checkedSkipExerciseIntroduction = "CHECKED";
} else {
  $checkedSkipExerciseIntroduction = '';
}

if ($hideSolutions == '1') {
  $checkedHideSolutions = "CHECKED";
} else {
  $checkedHideSolutions = '';
}

if ($hideProgressionBar == '1') {
  $checkedHideProgressionBar = "CHECKED";
} else {
  $checkedHideProgressionBar = '';
}

if ($hidePageTabs == '1') {
  $checkedHidePageTabs = "CHECKED";
} else {
  $checkedHidePageTabs = '';
}

if ($socialConnect == '1') {
  $checkedSocialConnect = "CHECKED";
} else {
  $checkedSocialConnect = '';
}

if ($disableNextPageTabs == '1') {
  $checkedDisableNextPageTabs = "CHECKED";
} else {
  $checkedDisableNextPageTabs = '';
}

$numberPageTabsList = array(
  '0' => '',
  ELEARNING_PAGE_TAB_IS_NUMBER => $mlText[53],
  ELEARNING_PAGE_TAB_WITH_NUMBER => $mlText[54],
);
$strSelectNumberPageTab = LibHtml::getSelectList("numberPageTabs", $numberPageTabsList, $numberPageTabs);

if ($hideKeyboard == '1') {
  $checkedHideKeyboard = "CHECKED";
} else {
  $checkedHideKeyboard = '';
}

if ($contactPage == '1') {
  $checkedContactPage = "CHECKED";
} else {
  $checkedContactPage = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[29], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<textarea name='description' cols='30' rows='5'>$description</textarea>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[25], $mlText[41], 300, 300);
$strJsSuggestCourse = $commonUtils->ajaxAutocomplete("$gElearningUrl/course/suggest.php", "courseName", "elearningCourseId");
$panelUtils->addContent($strJsSuggestCourse);
$panelUtils->addHiddenField('elearningCourseId', '');
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' id='courseName' name='' value='' />", "n"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[13], $mlText[15], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='maxDuration' value='$maxDuration' size='3' maxlength='3'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[26], $mlText[27], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='secured' $checkedSecured value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[7], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='publicAccess' $checkedPublicAccess value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[28], $mlText[30], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hideIntroduction' $checkedHideIntroduction value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[31], $mlText[32], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='skipExerciseIntroduction' $checkedSkipExerciseIntroduction value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[47], $mlText[48], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='socialConnect' $checkedSocialConnect value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[23], $mlText[24], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hideSolutions' $checkedHideSolutions value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[33], $mlText[34], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hideProgressionBar' $checkedHideProgressionBar value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[35], $mlText[36], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hidePageTabs' $checkedHidePageTabs value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[37], $mlText[38], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='disableNextPageTabs' $checkedDisableNextPageTabs value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[49], $mlText[50], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNumberPageTab);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[39], $mlText[40], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hideKeyboard' $checkedHideKeyboard value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[51], $mlText[52], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='contactPage' $checkedContactPage value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='releaseDate' id='releaseDate' value='$releaseDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 200);
$strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[19]'> $mlText[20]", "$gTemplateUrl/select.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage", "n"));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[22], "nbr"), $strSelectScoring);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectCategory);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[12], "nbr"), $strSelectSubject);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $strSelectLevel);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('webpageId', $webpageId);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
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
