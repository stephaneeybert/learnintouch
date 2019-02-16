<?PHP

require_once("website.php");

if (!isLocalhost()) {
  $adminUtils->checkForStaffLogin();
}

$backupUtils->createDirectories();

$dynpageUtils->createDirectories();

$flashUtils->createDirectories();

$profileUtils->createDirectories();

$lexiconEntryUtils->createDirectories();

$templateUtils->createDirectories();

$templateModelUtils->createDirectories();

$templatePropertyUtils->createDirectories();

$navbarItemUtils->createDirectories();

$navlinkItemUtils->createDirectories();

$navmenuItemUtils->createDirectories();

$userUtils->createDirectories();

$clientUtils->createDirectories();

$documentUtils->createDirectories();

$elearningExerciseUtils->createDirectories();

$elearningExercisePageUtils->createDirectories();

$elearningQuestionUtils->createDirectories();

$elearningAnswerUtils->createDirectories();

$elearningCourseUtils->createDirectories();

$elearningLessonHeadingUtils->createDirectories();

$elearningLessonUtils->createDirectories();

$elearningLessonParagraphUtils->createDirectories();

$linkUtils->createDirectories();

$mailUtils->createDirectories();

$formUtils->createDirectories();

$newsHeadingUtils->createDirectories();

$newsPaperUtils->createDirectories();

$newsStoryUtils->createDirectories();

$newsStoryImageUtils->createDirectories();

$newsFeedUtils->createDirectories();

$peopleUtils->createDirectories();

$photoUtils->createDirectories();

$shopItemImageUtils->createDirectories();

print("Directories created !!");

?>
