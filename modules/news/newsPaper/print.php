<?PHP

require_once("website.php");

$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
if (!$newsPaperId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
  }

$str = $newsPaperUtils->printNewsPaper($newsPaperId);

print($templateUtils->renderPopup($str));

?>
