<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$imagePath = $newsPaperUtils->imagePath;
$imageUrl = $newsPaperUtils->imageUrl;
$imageSize = $newsPaperUtils->imageSize;

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
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
      } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $imageSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imagePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }

    // Update the image
    $image = $userfile_name;
    }

  if (count($warnings) == 0) {

    if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
      $newsPaper->setImage($image);
      $newsPaperUtils->update($newsPaper);
      }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }

  }

$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
if (!$newsPaperId) {
  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
  }

if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
  $image = $newsPaper->getImage();
  }

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
    }
  }

$panelUtils->openMultipartForm($PHP_SELF);

if ($image) {
  if (LibImage::isImage($image) && !LibImage::isGif($image)) {
    $filename = urlencode($imagePath . $image);
    $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&width=120&height=";
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<img src='$url' border='0' title='' href=''>");
    }
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='checkbox' name='deleteImage' value='1'>");
  }

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());

$panelUtils->addHiddenField('max_file_size', $imageSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsPaperId', $newsPaperId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
