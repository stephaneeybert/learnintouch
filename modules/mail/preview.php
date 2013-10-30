<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$mailId = LibEnv::getEnvHttpGET("mailId");

if (!$mailId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$str = '';
if ($mail = $mailUtils->selectById($mailId)) {
  $str = $mailUtils->renderBody($mail);
}

print($str);

?>
