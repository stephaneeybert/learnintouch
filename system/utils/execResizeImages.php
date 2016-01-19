<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

$total = 0;

resizeImagesOfDir($gDataPath . 'news/newsPaper/image', $newsPaperUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'news/newsHeading/image', $newsHeadingUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'news/newsStory/image', $newsStoryUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'dynpage/image', $dynpageUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'lexicon/image', $lexiconEntryUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'elearning/answer/image', $elearningQuestionUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'elearning/exercise/image', $elearningExerciseUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'elearning/course/image', $elearningExerciseUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'elearning/exercise_page/image', $elearningExercisePageUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'elearning/heading/image', $elearningLessonUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'elearning/lesson/image', $elearningLessonUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'elearning/question/image', $elearningQuestionUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'mail/image', $mailUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'client/image', $clientUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'form/image', $formUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'link/image', $linkUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'people/image', $peopleUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'shop/image', $shopItemUtils->getImageWidth());
resizeImagesOfDir($gDataPath . 'user/image', $userUtils->getImageWidth());
resizeImagesOfAllSubDir($gDataPath . 'photo/image', $photoUtils->getImageWidth());

function resizeImagesOfAllSubDir($modulePath, $minWidth) {
  $dirNames = LibDir::getDirNames($modulePath);
  foreach ($dirNames as $dirName) {
    if ($dirName != "." && $dirName != "..") {
      resizeImagesOfDir($modulePath . '/' . $dirName, $minWidth);
    }
  }
}

function resizeImagesOfDir($modulePath, $minWidth) {
  global $fileUploadUtils;
  global $total;

  $str = 'Dir: <b>' . $modulePath . '</b><br>';
  print($str);
  $filenames = LibDir::getFileNames($modulePath);
  if (is_array($filenames)) {
    foreach ($filenames as $filename) {
      $image = $modulePath . '/' . $filename;
      if (!$fileUploadUtils->isGifImage($image)) {
        $width = LibImage::getWidth($image);
        if ($width > $minWidth) {
          print($image . ' Previous width: <b>' . $width . '</b><br>');
          LibImage::resizeImageToWidth($image, $minWidth);
          $newWidth = LibImage::getWidth($image);
          print('</b> New width: <b>' . $newWidth . '</b><br>');
          $total++;
        }
      }
    }
  }
}

print("<br>total: <b>$total</b>");

?>
