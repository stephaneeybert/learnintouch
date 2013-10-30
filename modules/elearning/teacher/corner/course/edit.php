<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $instantCorrection = LibEnv::getEnvHttpPOST("instantCorrection");
  $instantCongratulation = LibEnv::getEnvHttpPOST("instantCongratulation");
  $instantSolution = LibEnv::getEnvHttpPOST("instantSolution");
  $autoSubscription = LibEnv::getEnvHttpPOST("autoSubscription");
  $freeSamples = LibEnv::getEnvHttpPOST("freeSamples");
  $elearningMatterId = LibEnv::getEnvHttpPOST("elearningMatterId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $instantCorrection = LibString::cleanString($instantCorrection);
  $instantCongratulation = LibString::cleanString($instantCongratulation);
  $instantSolution = LibString::cleanString($instantSolution);
  $autoSubscription = LibString::cleanString($autoSubscription);
  $freeSamples = LibString::cleanString($freeSamples);

  // The course must belong to the user
  if ($elearningCourseId && !$elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
    array_push($warnings, $websiteText[10]);
  }

  // The name is required
  if (!$name) {
    array_push($warnings, $websiteText[3]);
  }

  // The matter is required
  if (!$elearningMatterId) {
    array_push($warnings, $websiteText[4]);
  }

  // The name must not already exist
  if (!$elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
    if ($elearningCourse = $elearningCourseUtils->selectByName($name)) {
      array_push($warnings, $websiteText[5]);
    }
  }

  if (count($warnings) == 0) {

    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $elearningCourse->setName($name);
      $elearningCourse->setDescription($description);
      $elearningCourse->setInstantCorrection($instantCorrection);
      $elearningCourse->setInstantCongratulation($instantCongratulation);
      $elearningCourse->setInstantSolution($instantSolution);
      $elearningCourse->setMatterId($elearningMatterId);
      $elearningCourse->setAutoSubscription($autoSubscription);
      $elearningCourse->setFreeSamples($freeSamples);
      $elearningCourseUtils->update($elearningCourse);
    } else {
      $elearningCourse = new ElearningCourse();
      $elearningCourse->setName($name);
      $elearningCourse->setDescription($description);
      $elearningCourse->setInstantCorrection($instantCorrection);
      $elearningCourse->setInstantCongratulation($instantCongratulation);
      $elearningCourse->setInstantSolution($instantSolution);
      $elearningCourse->setMatterId($elearningMatterId);
      $elearningCourse->setAutoSubscription($autoSubscription);
      $elearningCourse->setFreeSamples($freeSamples);
      $elearningCourse->setUserId($userId);
      $elearningCourseUtils->insert($elearningCourse);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    exit;
  }

} else {

  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $name = '';
  $description = '';
  $instantCorrection = '';
  $instantCongratulation = '';
  $instantSolution = '';
  $autoSubscription = '';
  $freeSamples = '';
  $elearningMatterId = '';
  if ($elearningCourseId) {
    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $name = $elearningCourse->getName();
      $description = $elearningCourse->getDescription();
      $instantCorrection = $elearningCourse->getInstantCorrection();
      $instantCongratulation = $elearningCourse->getInstantCongratulation();
      $instantSolution = $elearningCourse->getInstantSolution();
      $autoSubscription = $elearningCourse->getAutoSubscription();
      $freeSamples = $elearningCourse->getFreeSamples();
      $elearningMatterId = $elearningCourse->getMatterId();
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

if ($autoSubscription == '1') {
  $checkedAutoSubscription = "CHECKED";
} else {
  $checkedAutoSubscription = '';
}

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

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/course/edit.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[9]</div>";
$str .= "\n<div class='system_field'>$strSelectMatter</div>";

$str .= "\n<div class='system_label'>$websiteText[6]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='name' value='$name' size='30' maxlength='50' /></div>";

$str .= "\n<div class='system_label'>$websiteText[8]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='description' value='$description' size='30' maxlength='255' /></div>";

$label = $userUtils->getTipPopup($websiteText[20], $websiteText[21], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='instantCorrection' $checkedInstantCorrection value='1'></div>";

$label = $userUtils->getTipPopup($websiteText[22], $websiteText[23], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='instantCongratulation' $checkedInstantCongratulation value='1'></div>";

$label = $userUtils->getTipPopup($websiteText[24], $websiteText[25], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='instantSolution' $checkedInstantSolution value='1'></div>";

if ($elearningCourseId) {
  $label = $popupUtils->getUserTipPopup($websiteText[16], $websiteText[17], 300, 300);
  $strInfo = "<a href='$gElearningUrl/teacher/corner/course/info/list.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[18]'></a>";
  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'>$strInfo</div>";
}

$label = $popupUtils->getUserTipPopup($websiteText[11], $websiteText[12], 300, 300);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='autoSubscription' $checkedAutoSubscription value='1'></div>";

$label = $popupUtils->getUserTipPopup($websiteText[14], $websiteText[15], 300, 300);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'>$strSelectFreeSamples</div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningCourseId' value='$elearningCourseId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/course/list.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[13]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
