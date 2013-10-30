<?PHP

require_once("website.php");

$newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");
if (!$newsStoryId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
  }

if ($newsStoryId) {
  $str = $newsStoryUtils->printNewsStory($newsStoryId);

  print($templateUtils->renderPopup($str));
  }

?>
