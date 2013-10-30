<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");
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
      } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $elearningLessonParagraphUtils->audioFileSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $elearningLessonParagraphUtils->audioFilePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }

    // Update the audio file
    $audio = $userfile_name;
    }

  if (count($warnings) == 0) {

    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $elearningLessonParagraph->setAudio($audio);
      $elearningLessonParagraphUtils->update($elearningLessonParagraph);
      }

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }

  }

$elearningLessonParagraphId = LibEnv::getEnvHttpGET("elearningLessonParagraphId");
if (!$elearningLessonParagraphId) {
  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");
  }

$audio = '';
if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
  $audio = $elearningLessonParagraph->getAudio();
  }

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
    }
  }

$help = $popupUtils->getHelpPopup($mlText[1], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openMultipartForm($PHP_SELF);

if ($audio) {
  $fileUrl = "$elearningLessonParagraphUtils->audioFileUrl/$audio";
  $strAudio = "<a href='$fileUrl' $gJSNoStatus title=''>$audio</a>";
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $strAudio);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='checkbox' name='deleteFile' value='1'>");
  }
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($elearningLessonParagraphUtils->audioFileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $elearningLessonParagraphUtils->audioFileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonParagraphId', $elearningLessonParagraphId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
