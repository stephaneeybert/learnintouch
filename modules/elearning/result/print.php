<?PHP

require_once("website.php");

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

if (!$elearningResultId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
  }

// Check if the user is logged in
$userUtils->checkValidUserLogin();

$str = $elearningResultUtils->printResult($elearningResultId);

print($templateUtils->renderPopup($str));

?>
