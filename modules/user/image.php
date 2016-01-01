<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getWebsiteText(__FILE__);

$imageSize = $userUtils->imageSize;
$imagePath = $userUtils->imagePath;
$imageUrl = $userUtils->imageUrl;

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $userId = LibEnv::getEnvHttpPOST("userId");
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
    } else if ($str = $fileUploadUtils->checkImageFileType($userfile_name)) {
      // Check if the image file name has a correct file type
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $imageSize)) {
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imagePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
    }

    if ($fileUploadUtils->isImageType($userUtils->imagePath . $userfile_name) && !$fileUploadUtils->isGifImage($userUtils->imagePath . $userfile_name)) {
      $destWidth = $userUtils->getImageWidth();
      LibImage::resizeImageToWidth($userUtils->imagePath . $userfile_name, $destWidth);
    }

    // Update the image
    $image = $userfile_name;
  }

  if (count($warnings) == 0) {

    if ($user = $userUtils->selectById($userId)) {
      $user->setImage($image);
      $userUtils->update($user);
    }

    $str = LibHtml::urlRedirect("$gUserUrl/editProfile.php");
    printContent($str);
    return;
  }
}

$userId = LibEnv::getEnvHttpGET("userId");
if (!$userId) {
  $userId = LibEnv::getEnvHttpPOST("userId");
}

if ($user = $userUtils->selectById($userId)) {
  $image = $user->getImage();
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[0]</div>";

$str .= "\n<br />";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form enctype='multipart/form-data' action='$PHP_SELF' method='post'>";

$str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

if ($image) {
  if (!LibImage::isGif($image)) {
    $filename = urlencode($imagePath . $image);
    $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&width=120&height=";

    $str .= "\n<tr>";
    $str .= "\n<td class='system_label'>$mlText[6]</td>";
    $str .= "\n<td class='system_field'><img src='$url' title='' alt='' /></td>";
    $str .= "\n</tr>";

    $str .= "\n<tr><td><br /></td><td></td></tr>";
  }

  $str .= "\n<tr>";
  $str .= "\n<td class='system_label'>$mlText[3]</td>";
  $str .= "\n<td class='system_field'>$image</td>";
  $str .= "\n</tr>";

  $str .= "\n<tr><td><br /></td><td></td></tr>";

  $str .= "\n<tr>";
  $str .= "\n<td class='system_label'>$mlText[7]</td>";
  $str .= "\n<td class='system_field'><input type='checkbox' name='deleteImage' value='1' /></td>";
  $str .= "\n</tr>";
}

$str .= "\n<tr><td><br /></td><td></td></tr>";

$str .= "\n<tr>";
$str .= "\n<td class='system_label'>$mlText[2]</td>";
$str .= "\n<td class='system_field'><input type='file' name='userfile' size='15' maxlength='50' /></td>";
$str .= "\n</tr>";

$str .= "\n</table>";

$str .= "\n<div class='system_comment'>"
  . $fileUploadUtils->getFileSizeMessage($imageSize)
  . "</div>";

$str .= "\n<div class='system_okay_button'>"
  . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' alt='' />"
  . "</div>";

$str .= "\n<div><input type='hidden' name='max_file_size' value='$imageSize' /></div>";
$str .= "\n<div><input type='hidden' name='formSubmitted' value='1' /></div>";
$str .= "\n<div><input type='hidden' name='userId' value='$userId' /></div>";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
