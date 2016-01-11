<?PHP

$specific = '';
if ($argc == 2) {
  $specific = $argv[1];
} else {
  die("Some arguments are missing for the file $PHP_SELF");
}

if (!is_file($specific)) {
  die("The file $specific is missing for the file $PHP_SELF");
}
include($specific);

require_once("cli.php");

$dynpageUtils->deleteUnusedImageFiles();

$languageUtils->deleteUnusedImageFiles();

$flashUtils->deleteUnusedFiles();
$flashUtils->deleteUnusedWddxFiles();

$profileUtils->deleteUnusedFiles();

$templateUtils->deleteUnusedCacheFiles();

$templatePropertyUtils->deleteUnusedImageFiles();

$navbarItemUtils->deleteUnusedImageFiles();

$navlinkItemUtils->deleteUnusedImageFiles();

$navmenuItemUtils->deleteUnusedImageFiles();

$userUtils->deleteUnusedImageFiles();

$clientUtils->deleteUnusedImageFiles();

$documentUtils->deleteUnusedFiles();

$elearningExerciseUtils->deleteUnusedImageFiles();
$elearningExerciseUtils->deleteUnusedAudioFiles();

$elearningLessonUtils->deleteUnusedImageFiles();
$elearningLessonUtils->deleteUnusedAudioFiles();

$elearningLessonParagraphUtils->deleteUnusedImageFiles();
$elearningLessonParagraphUtils->deleteUnusedAudioFiles();

$elearningExercisePageUtils->deleteUnusedImageFiles();
$elearningExercisePageUtils->deleteUnusedAudioFiles();

$elearningQuestionUtils->deleteUnusedImageFiles();
$elearningQuestionUtils->deleteUnusedAudioFiles();

$elearningAnswerUtils->deleteUnusedImageFiles();
$elearningAnswerUtils->deleteUnusedAudioFiles();

$elearningCourseUtils->deleteUnusedImageFiles();

$linkUtils->deleteUnusedImageFiles();

$lexiconEntryUtils->deleteUnusedImageFiles();

$mailUtils->deleteUnusedImages();
$mailUtils->deleteUnusedAttachedFiles();

$newsHeadingUtils->deleteUnusedImageFiles();

$newsPaperUtils->deleteUnusedImageFiles();

$newsStoryUtils->deleteUnusedAudioFiles();

$newsStoryImageUtils->deleteUnusedImageFiles();

$newsFeedUtils->deleteUnusedImageFiles();

$peopleUtils->deleteUnusedImageFiles();

$photoUtils->deleteUnusedImageFiles();

$shopItemImageUtils->deleteUnusedImageFiles();

error_log("The data files were cleaned up successfully.");

?>
