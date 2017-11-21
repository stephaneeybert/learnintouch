<?PHP

require_once("website.php");

// Prevent any possible previous header from being sent before the following image header that must be the first
ob_end_clean();

$color = LibEnv::getEnvHttpGET("color");
$width = LibEnv::getEnvHttpGET("width");
$height = LibEnv::getEnvHttpGET("height");

$color = urldecode($color);

if ($color && $width > 0 && $height> 0) {
  LibImage::printBarImage($color, $width, $height);
}

?>
