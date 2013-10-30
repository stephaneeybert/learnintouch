<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");

  if ($deleteImage == 1) {
    $image = '';
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
      } else if ($str = $fileUploadUtils->checkMediaFileType($userfile_name)) {
      // Check if the image file name has a correct file type
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $elearningExercisePageUtils->imageFileSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $elearningExercisePageUtils->imageFilePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }

    // Update the image
    $image = $userfile_name;
    }

  if (count($warnings) == 0) {

    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $elearningExercisePage->setImage($image);
      $elearningExercisePageUtils->update($elearningExercisePage);
      }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }

  }

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
if (!$elearningExercisePageId) {
  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  }

if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $image = $elearningExercisePage->getImage();
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

if ($image) {
  if (LibImage::isImage($image) && !LibImage::isGif($image)) {
    $filename = urlencode($elearningExercisePageUtils->imageFilePath . $image);
    $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&width=120&height=";
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<img src='$url' border='0' title='' href=''>");
    } else {
    $fileUrl = "$elearningExercisePageUtils->imageFileUrl/$image";
    $strImage = "<a href='$fileUrl' $gJSNoStatus title=''>$image</a>";
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), $strImage);
    }
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), "<input type='checkbox' name='deleteImage' value='1'>");
  }

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($elearningExercisePageUtils->imageFileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $elearningExercisePageUtils->imageFileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExercisePageId', $elearningExercisePageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
