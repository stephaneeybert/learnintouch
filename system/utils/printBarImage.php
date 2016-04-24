<?PHP

require_once("website.php");

$color = LibEnv::getEnvHttpGET("color");
$width = LibEnv::getEnvHttpGET("width");
$height = LibEnv::getEnvHttpGET("height");

$color = urldecode($color);

if ($color && $width > 0 && $height> 0) {
  LibImage::printBarImage($color, $width, $height);
}

?>
