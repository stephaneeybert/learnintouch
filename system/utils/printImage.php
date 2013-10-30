<?PHP

require_once("website.php");

$filename = LibEnv::getEnvHttpGET("filename");
$width = LibEnv::getEnvHttpGET("width");
$height = LibEnv::getEnvHttpGET("height");

$filename = urldecode($filename);

// If an image is passed and if at least the width or the height is correctly specified
// then output the image resized from the width or the height
if ($filename && ($width > 2 || $height > 2)) {

  if ($height < 2) {
    $height = LibImage::getHeightFromWidth($filename, $width);
  }

  if ($width < 2) {
    $width = LibImage::getWidthFromHeight($filename, $height);
  }

  LibImage::printImage($filename, $width, $height);
}

?>
