<?PHP

$specific = '';
if ($argc == 2) {
  $specific = $argv[1];
} else {
  die("Some arguments are missing for the file $PHP_SELF");
}

if (!@is_file($specific)) {
  die("The file $specific is missing for the file $PHP_SELF");
}
include($specific);

/*
define('DB_TABLE_ELEARNING_EXERCISE_OLD', "elearning_exercise_old");
define('DB_TABLE_ELEARNING_EXERCISE_PAGE_OLD', "elearning_exercise_page_old");
define('DB_TABLE_CONTAINER_ITEM', "container_item");

require_once("cli.php");
require_once($gElearningPath . "exercise/ElearningExerciseOld.php");
require_once($gElearningPath . "exercise/ElearningExerciseOldDao.php");
require_once($gElearningPath . "exercise/ElearningExerciseOldUtils.php");
require_once($gElearningPath . "exercise_page/ElearningExercisePageOld.php");
require_once($gElearningPath . "exercise_page/ElearningExercisePageOldDao.php");
require_once($gElearningPath . "exercise_page/ElearningExercisePageOldUtils.php");
require_once($gContainerPath . "ContainerItem.php");
require_once($gContainerPath . "ContainerItemDao.php");
require_once($gContainerPath . "ContainerItemDB.php");
require_once($gContainerPath . "ContainerItemUtils.php");

$elearningExerciseOldUtils = new ElearningExerciseOldUtils();
$elearningExercisePageOldUtils = new ElearningExercisePageOldUtils();

$elearningExerciseOlds = $elearningExerciseOldUtils->selectAll();
if ($elearningExerciseOlds) {
  foreach ($elearningExerciseOlds as $elearningExerciseOld) {
    $elearningExerciseId = $elearningExerciseOld->getId();
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $containerId = $elearningExerciseOld->getInstructionsId();
      if ($containerItems = $containerItemUtils->selectByContainerId($containerId)) {
        foreach ($containerItems as $containerItem) {
          $language = $containerItem->getLanguage();
          $content = $containerItem->getContent();
          $elearningExercise->setInstructions($languageUtils->setTextForLanguage($elearningExercise->getInstructions(), $language, $content));
          $elearningExerciseUtils->update($elearningExercise);
        }
      }
    }
die();
  }
}

/*

$elearningExercisePageOlds = $elearningExercisePageOldUtils->selectAll();
if ($elearningExercisePageOlds) {
  foreach ($elearningExercisePageOlds as $elearningExercisePageOld) {
    $elearningExercisePageId = $elearningExercisePageOld->getId();
    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $containerId = $elearningExercisePageOld->getInstructionsId();
      if ($containerItems = $containerItemUtils->selectByContainerId($containerId)) {
        foreach ($containerItems as $containerItem) {
          $language = $containerItem->getLanguage();
          $content = $containerItem->getContent();
          $elearningExercisePage->setInstructions($languageUtils->setTextForLanguage($elearningExercisePage->getInstructions(), $language, $content));
          $elearningExercisePageUtils->update($elearningExercisePage);
        }
      }
    }
die();
  }
}

require_once("cli.php");

if ($templateElements = $templateElementUtils->selectAll()) {
  foreach ($templateElements as $templateElement) {
    $templateElementId = $templateElement->getId();
    $elementType = $templateElement->getElementType();
    $objectId = $templateElement->getObjectId();
    if ($elementType == 'NEWS_FEED' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE') {
      $templateElementLanguageId = $templateElementLanguageUtils->add($templateElementId, '', $objectId);
    }
  }
}

 */

?>
