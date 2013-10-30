<?PHP

require_once("website.php");

$newsPublicationId = LibEnv::getEnvHttpGET("newsPublicationId");
if (!$newsPublicationId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
  }

$str = $newsPublicationUtils->printNewsPublication($newsPublicationId);

print($templateUtils->renderPopup($str));

?>
