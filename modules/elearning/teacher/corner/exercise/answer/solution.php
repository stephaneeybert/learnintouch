<?PHP

require_once("website.php");


$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
$notSolution = LibEnv::getEnvHttpGET("notSolution");

// The content must belong to the user
if ($elearningAnswerId && !$elearningAnswerUtils->createdByUser($elearningAnswerId, $userId)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

if ($notSolution) {
  $elearningAnswerUtils->specifyAsNotSolution($elearningAnswerId);
} else {
  $elearningAnswerUtils->specifyAsSolution($elearningAnswerId);
}

$str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
printContent($str);
return;

?>
