<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FLASH);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();
$fileSize = $flashUtils->fileSize;
$filePath = $flashUtils->filePath;
$fileUrl = $flashUtils->fileUrl;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $flashId = LibEnv::getEnvHttpPOST("flashId");
  $deleteFile = LibEnv::getEnvHttpPOST("deleteFile");

  if ($deleteFile == 1) {
    $filename = '';
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
      } else if (($str = $fileUploadUtils->checkMediaFileType($userfile_name)) && ($str = $fileUploadUtils->checkAudioFileType($userfile_name))) {
      // Check if the file name has a correct file type
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $fileSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $filePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }

    // Update the file
    $filename = $userfile_name;
    }

  if (count($warnings) == 0) {

    if ($flash = $flashUtils->selectById($flashId)) {
      if ($filename) {
      $flash->setFile($filename);
        // Either the swf file is removed and the wddx needs to be removed too
        // Or a swf file is set and the wddx needs to be reset
        $flash->setWddx('');
        $flashUtils->update($flash);
        } else {
        $flashUtils->deleteFile($flashId);
        }
      }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }
  }

$flashId = LibEnv::getEnvHttpGET("flashId");
if (!$flashId) {
  $flashId = LibEnv::getEnvHttpPOST("flashId");
  }

$filename = '';
if ($flash = $flashUtils->selectById($flashId)) {
  $filename = $flash->getFile();
  }

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
    }
  }

$panelUtils->openMultipartForm($PHP_SELF);

if (@file_exists($filePath . $filename)) {
  $strFile = "<a href='$fileUrl/$filename' $gJSNoStatus title=''>$filename</a>";
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $strFile);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='checkbox' name='deleteFile' value='1'>");
  }

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($fileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $fileSize);
$panelUtils->addHiddenField('flashId', $flashId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
