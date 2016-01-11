<?PHP

require_once("website.php");

$filename = LibEnv::getEnvHttpGET("filename");
$filename = urldecode($filename);

if (file_exists($filename)) {
  // Make sure the file belongs to the website
  if (stristr($filename, $gRootPath)) {
    LibFile::downloadFile($filename);
  }
}

?>
