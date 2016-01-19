<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$imageSize = $userUtils->imageSize;
$imagePath = $userUtils->imagePath;
$imageUrl = $userUtils->imageUrl;

$fileUploadUtils->loadLanguageTexts();

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $userId = LibEnv::getEnvHttpPOST("userId");
  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");
  $imageWidth = LibEnv::getEnvHttpPOST("imageWidth");

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

    // Update the image
    $image = $userfile_name;
  }

  if (count($warnings) == 0) {

    if ($imageWidth) {
      if ($fileUploadUtils->isImageType($userUtils->imagePath . $image) && !$fileUploadUtils->isGifImage($userUtils->imagePath . $image)) {
        LibImage::resizeImageToWidth($userUtils->imagePath . $image, $imageWidth);
      }
    }

    if ($user = $userUtils->selectById($userId)) {
      $user->setImage($image);
      $userUtils->update($user);
    }

    $str = LibHtml::urlRedirect("$gUserUrl/editProfile.php");
    printContent($str);
    return;
  }

} else {

  $imageWidth = $userUtils->getImageWidth();

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

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= "\n<br />";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form enctype='multipart/form-data' action='$PHP_SELF' method='post'>";

$str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

if ($image) {
  $url = $imageUrl . "/" . $image;
  $str .= "\n<tr>";
  $str .= "\n<td class='system_label'>$websiteText[6]</td>";
  $str .= "\n<td class='system_field'><img src='$url' title='' alt='' /></td>";
  $str .= "\n</tr>";

  $str .= "\n<tr><td><br /></td><td></td></tr>";

  $str .= "\n<tr>";
  $str .= "\n<td class='system_label'>$websiteText[3]</td>";
  $str .= "\n<td class='system_field'>$image</td>";
  $str .= "\n</tr>";

  $str .= "\n<tr><td><br /></td><td></td></tr>";

  $str .= "\n<tr>";
  $str .= "\n<td class='system_label'>$websiteText[7]</td>";
  $str .= "\n<td class='system_field'><input type='checkbox' name='deleteImage' value='1' /></td>";
  $str .= "\n</tr>";
}

$str .= "\n<tr><td><br /></td><td></td></tr>";

$label = $popupUtils->getTipPopup($websiteText[8], $websiteText[9], 300, 300);
$str .= "\n<tr>";
$str .= "\n<td class='system_label'>$label</td>";
$str .= "\n<td class='system_field'><input type='text' name='imageWidth' value= '$imageWidth' size='5' maxlength='5' /></td>";
$str .= "\n</tr>";

$str .= "\n<tr>";
$str .= "\n<td class='system_label'>$websiteText[2]</td>";
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
