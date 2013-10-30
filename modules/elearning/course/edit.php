<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $instantCorrection = LibEnv::getEnvHttpPOST("instantCorrection");
  $instantCongratulation = LibEnv::getEnvHttpPOST("instantCongratulation");
  $instantSolution = LibEnv::getEnvHttpPOST("instantSolution");
  $importable = LibEnv::getEnvHttpPOST("importable");
  $secured = LibEnv::getEnvHttpPOST("secured");
  $freeSamples = LibEnv::getEnvHttpPOST("freeSamples");
  $autoSubscription = LibEnv::getEnvHttpPOST("autoSubscription");
  $autoUnsubscription = LibEnv::getEnvHttpPOST("autoUnsubscription");
  $interruptTimedOutExercise = LibEnv::getEnvHttpPOST("interruptTimedOutExercise");
  $resetExerciseAnswers = LibEnv::getEnvHttpPOST("resetExerciseAnswers");
  $exerciseOnlyOnce = LibEnv::getEnvHttpPOST("exerciseOnlyOnce");
  $exerciseAnyOrder = LibEnv::getEnvHttpPOST("exerciseAnyOrder");
  $saveResultOption = LibEnv::getEnvHttpPOST("saveResultOption");
  $shuffleQuestions = LibEnv::getEnvHttpPOST("shuffleQuestions");
  $shuffleAnswers = LibEnv::getEnvHttpPOST("shuffleAnswers");
  $elearningMatterId = LibEnv::getEnvHttpPOST("elearningMatterId");
  $userId = LibEnv::getEnvHttpPOST("userId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $instantCorrection = LibString::cleanString($instantCorrection);
  $instantCongratulation = LibString::cleanString($instantCongratulation);
  $instantSolution = LibString::cleanString($instantSolution);
  $importable = LibString::cleanString($importable);
  $secured = LibString::cleanString($secured);
  $freeSamples = LibString::cleanString($freeSamples);
  $autoSubscription = LibString::cleanString($autoSubscription);
  $autoUnsubscription = LibString::cleanString($autoUnsubscription);
  $interruptTimedOutExercise = LibString::cleanString($interruptTimedOutExercise);
  $resetExerciseAnswers = LibString::cleanString($resetExerciseAnswers);
  $exerciseOnlyOnce = LibString::cleanString($exerciseOnlyOnce);
  $exerciseAnyOrder = LibString::cleanString($exerciseAnyOrder);
  $saveResultOption = LibString::cleanString($saveResultOption);
  $shuffleQuestions = LibString::cleanString($shuffleQuestions);
  $shuffleAnswers = LibString::cleanString($shuffleAnswers);
  $userId = LibString::cleanString($userId);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // The matter is required
  if (!$elearningMatterId) {
    array_push($warnings, $mlText[2]);
  }

  // The name must not already exist
  if (!$elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
    if ($elearningCourse = $elearningCourseUtils->selectByName($name)) {
      array_push($warnings, $mlText[9]);
    }
  }

  if ($elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
    array_push($warnings, $mlText[1]);
  }

  // Check that an importable course, contains some exercises
  if ($importable) {
    if (!$elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
      array_push($warnings, $mlText[35]);
    }
  }

  // Check that a course for which participants can subscribe themselves, contains some exercises
  if ($autoSubscription) {
    if (!$elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
      array_push($warnings, $mlText[27]);
    }
  }

  if ($userId == '-1') {
    $userId = '';
  }

  if (count($warnings) == 0) {

    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $elearningCourse->setName($name);
      $elearningCourse->setDescription($description);
      $elearningCourse->setInstantCorrection($instantCorrection);
      $elearningCourse->setInstantCongratulation($instantCongratulation);
      $elearningCourse->setInstantSolution($instantSolution);
      $elearningCourse->setImportable($importable);
      $elearningCourse->setSecured($secured);
      $elearningCourse->setFreeSamples($freeSamples);
      $elearningCourse->setAutoSubscription($autoSubscription);
      $elearningCourse->setAutoUnsubscription($autoUnsubscription);
      $elearningCourse->setInterruptTimedOutExercise($interruptTimedOutExercise);
      $elearningCourse->setResetExerciseAnswers($resetExerciseAnswers);
      $elearningCourse->setExerciseOnlyOnce($exerciseOnlyOnce);
      $elearningCourse->setExerciseAnyOrder($exerciseAnyOrder);
      $elearningCourse->setSaveResultOption($saveResultOption);
      $elearningCourse->setShuffleQuestions($shuffleQuestions);
      $elearningCourse->setShuffleAnswers($shuffleAnswers);
      $elearningCourse->setMatterId($elearningMatterId);
      $elearningCourse->setUserId($userId);
      $elearningCourseUtils->update($elearningCourse);
    } else {
      $elearningCourse = new ElearningCourse();
      $elearningCourse->setName($name);
      $elearningCourse->setDescription($description);
      $elearningCourse->setInstantCorrection($instantCorrection);
      $elearningCourse->setInstantCongratulation($instantCongratulation);
      $elearningCourse->setInstantSolution($instantSolution);
      $elearningCourse->setImportable($importable);
      $elearningCourse->setSecured($secured);
      $elearningCourse->setFreeSamples($freeSamples);
      $elearningCourse->setAutoSubscription($autoSubscription);
      $elearningCourse->setAutoUnsubscription($autoUnsubscription);
      $elearningCourse->setInterruptTimedOutExercise($interruptTimedOutExercise);
      $elearningCourse->setResetExerciseAnswers($resetExerciseAnswers);
      $elearningCourse->setExerciseOnlyOnce($exerciseOnlyOnce);
      $elearningCourse->setExerciseAnyOrder($exerciseAnyOrder);
      $elearningCourse->setSaveResultOption($saveResultOption);
      $elearningCourse->setShuffleQuestions($shuffleQuestions);
      $elearningCourse->setShuffleAnswers($shuffleAnswers);
      $elearningCourse->setMatterId($elearningMatterId);
      $elearningCourse->setUserId($userId);
      $elearningCourseUtils->insert($elearningCourse);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/course/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $name = '';
  $description = '';
  $instantCorrection = '';
  $instantCongratulation = '';
  $instantSolution = '';
  $importable = '';
  $secured = '';
  $freeSamples = '';
  $autoSubscription = '';
  $autoUnsubscription = '';
  $interruptTimedOutExercise = '';
  $resetExerciseAnswers = '';
  $exerciseOnlyOnce = '';
  $exerciseAnyOrder = '';
  $saveResultOption = '';
  $shuffleQuestions = '';
  $shuffleAnswers = '';
  $elearningMatterId = '';
  $userId = '';
  if ($elearningCourseId) {
    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $name = $elearningCourse->getName();
      $description = $elearningCourse->getDescription();
      $instantCorrection = $elearningCourse->getInstantCorrection();
      $instantCongratulation = $elearningCourse->getInstantCongratulation();
      $instantSolution = $elearningCourse->getInstantSolution();
      $importable = $elearningCourse->getImportable();
      $secured = $elearningCourse->getSecured();
      $freeSamples = $elearningCourse->getFreeSamples();
      $autoSubscription = $elearningCourse->getAutoSubscription();
      $autoUnsubscription = $elearningCourse->getAutoUnsubscription();
      $interruptTimedOutExercise = $elearningCourse->getInterruptTimedOutExercise();
      $resetExerciseAnswers = $elearningCourse->getResetExerciseAnswers();
      $exerciseOnlyOnce = $elearningCourse->getExerciseOnlyOnce();
      $exerciseAnyOrder = $elearningCourse->getExerciseAnyOrder();
      $saveResultOption = $elearningCourse->getSaveResultOption();
      $shuffleQuestions = $elearningCourse->getShuffleQuestions();
      $shuffleAnswers = $elearningCourse->getShuffleAnswers();
      $elearningMatterId = $elearningCourse->getMatterId();
      $userId = $elearningCourse->getUserId();
    }
  }

}

$elearningMatters = $elearningMatterUtils->selectAll();
$elearningMatterList = Array('' => '');
foreach ($elearningMatters as $elearningMatter) {
  $wMatterId = $elearningMatter->getId();
  $wName = $elearningMatter->getName();
  $elearningMatterList[$wMatterId] = $wName;
}
$strSelectMatter = LibHtml::getSelectList("elearningMatterId", $elearningMatterList, $elearningMatterId);

$freeSamplesList = Array('' => '');
for ($i = 1; $i <= 10; $i++) {
  $freeSamplesList[$i] = $i;
}
$strSelectFreeSamples = LibHtml::getSelectList("freeSamples", $freeSamplesList, $freeSamples);

if ($instantCorrection == '1') {
  $checkedInstantCorrection = "CHECKED";
} else {
  $checkedInstantCorrection = '';
}

if ($instantCongratulation == '1') {
  $checkedInstantCongratulation = "CHECKED";
} else {
  $checkedInstantCongratulation = '';
}

if ($instantSolution == '1') {
  $checkedInstantSolution = "CHECKED";
} else {
  $checkedInstantSolution = '';
}

if ($importable == '1') {
  $checkedImportable = "CHECKED";
} else {
  $checkedImportable = '';
}

if ($secured == '1') {
  $checkedSecured = "CHECKED";
} else {
  $checkedSecured = '';
}

if ($autoSubscription == '1') {
  $checkedAutoSubscription = "CHECKED";
} else {
  $checkedAutoSubscription = '';
}

if ($autoUnsubscription == '1') {
  $checkedAutoUnsubscription = "CHECKED";
} else {
  $checkedAutoUnsubscription = '';
}

if ($interruptTimedOutExercise == '1') {
  $checkedInterruptTimedOutExercise = "CHECKED";
} else {
  $checkedInterruptTimedOutExercise = '';
}

if ($resetExerciseAnswers == '1') {
  $checkedResetExerciseAnswers = "CHECKED";
} else {
  $checkedResetExerciseAnswers = '';
}

if ($exerciseOnlyOnce == '1') {
  $checkedExerciseOnlyOnce = "CHECKED";
} else {
  $checkedExerciseOnlyOnce = '';
}

if ($exerciseAnyOrder == '1') {
  $checkedExerciseAnyOrder = "CHECKED";
} else {
  $checkedExerciseAnyOrder = '';
}

if ($shuffleQuestions == '1') {
  $checkedShuffleQuestions = "CHECKED";
} else {
  $checkedShuffleQuestions = '';
}

if ($shuffleAnswers == '1') {
  $checkedShuffleAnswers = "CHECKED";
} else {
  $checkedShuffleAnswers = '';
}

$saveResultOptionList = Array('0' => '', 'ELEARNING_SAVE_RESULT_FIRST' => $mlText[32], 'ELEARNING_SAVE_RESULT_EVERY_TIME' => $mlText[33], 'ELEARNING_SAVE_RESULT_BETTER' => $mlText[34]);
$strSaveResultOption = LibHtml::getSelectList("saveResultOption", $saveResultOptionList, $saveResultOption);

$userName = '';
if ($user = $userUtils->selectById($userId)) {
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/course/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[47], $mlText[42], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='instantCorrection' $checkedInstantCorrection value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[43], $mlText[44], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='instantCongratulation' $checkedInstantCongratulation value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[45], $mlText[46], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='instantSolution' $checkedInstantSolution value='1'>");
$panelUtils->addLine();
if ($elearningCourseId) {
  $label = $popupUtils->getTipPopup($mlText[14], $mlText[15], 300, 300);
  $strInfo = "<a href='$gElearningUrl/course/info/admin.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[20]'></a>";
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strInfo);
  $panelUtils->addLine();
}
$panelUtils->addLine($panelUtils->addCell($mlText[11], "nbr"), $strSelectMatter);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[24], $mlText[26], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='secured' $checkedSecured value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectFreeSamples);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[21], $mlText[22], 300, 200);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gUserUrl/suggestUsers.php", "userName", "userId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('userId', $userId);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='userName' value='$userName' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='autoSubscription' $checkedAutoSubscription value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[28], $mlText[23], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='autoUnsubscription' $checkedAutoUnsubscription value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[36], $mlText[37], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='importable' $checkedImportable value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[16], $mlText[17], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='interruptTimedOutExercise' $checkedInterruptTimedOutExercise value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[18], $mlText[19], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='resetExerciseAnswers' $checkedResetExerciseAnswers value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[25], $mlText[29], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='exerciseOnlyOnce' $checkedExerciseOnlyOnce value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[7], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='exerciseAnyOrder' $checkedExerciseAnyOrder value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[38], $mlText[39], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='shuffleQuestions' $checkedShuffleQuestions value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[40], $mlText[41], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='shuffleAnswers' $checkedShuffleAnswers value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[30], $mlText[31], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSaveResultOption);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
