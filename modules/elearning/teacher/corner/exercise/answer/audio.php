<?PHP

require_once("website.php");

$fileUploadUtils->loadLanguageTexts();

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningAnswerId = LibEnv::getEnvHttpPOST("elearningAnswerId");
  $deleteFile = LibEnv::getEnvHttpPOST("deleteFile");

  if ($deleteFile == 1) {
    $audio = '';
  } else {
    // Get the file characteristics
    // Note how the form parameter "userfile" creates several variables
    $uploaded_file = LibEnv::getEnvHttpFILE("userfile");
    $userfile = $uploaded_file['tmp_name'];
    $userfile_name = $uploaded_file['name'];
    $userfile_type = $uploaded_file['type'];
    $userfile_size = $uploaded_file['size'];

    // Clean up the filename
    $userfile_name = LibString::stripNonFilenameChar($userfile_name);

    // Check if a file has been specified...
    if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->checkAudioFileType($userfile_name)) {
      // Check if the image file name has a correct file type
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $elearningAnswerUtils->audioFileSize)) {
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $elearningAnswerUtils->audioFilePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
    }

    // Update the audio file
    $audio = $userfile_name;
  }

  // The content must belong to the user
  if ($elearningAnswerId && !$elearningAnswerUtils->createdByUser($elearningAnswerId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
      $elearningAnswer->setAudio($audio);
      $elearningAnswerUtils->update($elearningAnswer);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

}

$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
if (!$elearningAnswerId) {
  $elearningAnswerId = LibEnv::getEnvHttpPOST("elearningAnswerId");
}

$audio = '';
if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
  $audio = $elearningAnswer->getAudio();
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[1], 300, 400);

$str .= "\n<div style='text-align:right;'>$help</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/answer/audio.php' method='post' enctype='multipart/form-data'>";

if ($audio) {
  $fileUrl = "$elearningAnswerUtils->audioFileUrl/$audio";
  $str .= "\n<div class='system_label'>$websiteText[6]</div>";
  $str .= "\n<div class='system_field'><a href='$fileUrl' $gJSNoStatus title=''>$audio</a></div>";
  $str .= "\n<div class='system_label'>$websiteText[7]</div>";
  $str .= "\n<div class='system_field'><input type='checkbox' name='deleteFile' value='1'></div>";
}
$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'><input type=file name='userfile' size='15' maxlength='50'></div>";
$str .= "\n<div class='system_field'>" . $fileUploadUtils->getFileSizeMessage($elearningAnswerUtils->audioFileSize) . "</div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[4]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='max_file_size' value='" . $elearningAnswerUtils->audioFileSize . "' />";
$str .= "\n<input type='hidden' name='elearningAnswerId' value='$elearningAnswerId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[8]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
