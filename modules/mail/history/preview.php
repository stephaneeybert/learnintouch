<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$mailHistoryId = LibEnv::getEnvHttpGET("mailHistoryId");

if (!$mailHistoryId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
  }

if ($mailHistory = $mailHistoryUtils->selectById($mailHistoryId)) {
  $str = "<div style='background-color:#ffffff; padding: 4px;'>" . $mailHistory->getBody() . "</div>";
  }

print($str);

?>
